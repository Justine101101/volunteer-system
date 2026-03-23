<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\RecordsAuditLogs;

class DatabaseQueryService
{
    use RecordsAuditLogs;
    protected SupabaseService $supabase;

    /**
     * Supabase `public.users.role` check constraint only accepts a limited set of values.
     * In practice for this project, it accepts `superadmin` and `volunteer` (rejects `admin/president/officer`).
     */
    private function normalizeSupabaseUserRole(?string $role): string
    {
        $role = $role ? strtolower(trim($role)) : null;

        return match ($role) {
            'admin', 'president', 'superadmin' => 'superadmin',
            // Keep volunteer as-is; treat any other legacy/non-elevated roles as volunteer.
            'volunteer', null => 'volunteer',
            default => 'volunteer',
        };
    }

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Get all events with pagination
     */
    public function getEvents(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
        // NOTE: Supabase has multiple relationships between events <-> event_registrations in this schema.
        // We must specify the relationship name to avoid PGRST201 "more than one relationship was found".
        $query = $this->supabase->from('events')
            ->select('*, creator:users(name, email), registrations:event_registrations!event_registrations_event_id_fkey(count)')
            ->order('event_date', 'asc');

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->eq('event_status', $filters['status']);
            }

            if (isset($filters['date_from'])) {
                $query = $query->gte('event_date', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query = $query->lte('event_date', $filters['date_to']);
            }

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return ['error' => 'Failed to fetch events'];
        }
    }

    /**
     * Get all events with pagination using service role (bypass RLS).
     * Use for admin dashboards/analytics.
     */
    public function getEventsPrivileged(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
            $query = $this->supabase->fromPrivileged('events')
                ->select('*, creator:users(name, email), registrations:event_registrations!event_registrations_event_id_fkey(count)')
                ->order('event_date', 'asc');

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->eq('event_status', $filters['status']);
            }

            if (isset($filters['date_from'])) {
                $query = $query->gte('event_date', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query = $query->lte('event_date', $filters['date_to']);
            }

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching events (privileged): ' . $e->getMessage());
            return ['error' => 'Failed to fetch events'];
        }
    }

    /**
     * Get event by ID with full details
     * Accepts UUID string (Supabase) or can find by title+date+location for compatibility
     */
    public function getEventById(string $eventId)
    {
        try {
            $result = $this->supabase->from('events')
                ->select('*, creator:users(name, email), registrations:event_registrations!event_registrations_event_id_fkey(*, user:users(name, email))')
                ->eq('id', $eventId)
                ->single()
                ->execute();

            // Normalize possible [0 => record] shape into a single record
            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error fetching event: ' . $e->getMessage());
            return ['error' => 'Event not found'];
        }
    }

    /**
     * Get event by ID using privileged (service role) access, for admin screens.
     */
    public function getEventByIdPrivileged(string $eventId)
    {
        try {
            $result = $this->supabase->fromPrivileged('events')
                ->select('id,title,event_date,event_time,location,event_status,created_at')
                ->eq('id', $eventId)
                ->single()
                ->execute();

            // Normalize possible [0 => record] shape into a single record
            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error fetching event (privileged): ' . $e->getMessage());
            return ['error' => 'Event not found'];
        }
    }

    /**
     * Find event by matching fields (for compatibility with MySQL integer IDs)
     */
    public function findEventByFields(string $title, string $date, string $location)
    {
        try {
            $events = $this->supabase->from('events')
                ->select('*')
                ->eq('title', $title)
                ->eq('event_date', $date)
                ->eq('location', $location)
                ->limit(1)
                ->execute();

            if (is_array($events) && count($events) > 0) {
                return $events[0];
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error finding event by fields: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new event
     */
    public function createEvent(array $eventData)
    {
        try {
            // Skip if Supabase is not configured
            if (!config('supabase.url') || !config('supabase.service_role_key')) {
                return ['error' => 'Supabase not configured'];
            }

            // Handle created_by: Supabase expects UUID, but we have MySQL integer ID
            // Try to find the Supabase user UUID, or set to null if not found
            if (isset($eventData['created_by']) && is_numeric($eventData['created_by'])) {
                $mysqlUser = \App\Models\User::find($eventData['created_by']);
                if ($mysqlUser && $mysqlUser->email) {
                    $supabaseUser = $this->getUserByEmail($mysqlUser->email);
                    $eventData['created_by'] = $supabaseUser['id'] ?? null;
                } else {
                    $eventData['created_by'] = null;
                }
            }

            // Format date to Y-m-d (Supabase DATE type)
            if (isset($eventData['event_date'])) {
                if ($eventData['event_date'] instanceof \DateTime || $eventData['event_date'] instanceof \Carbon\Carbon) {
                    $eventData['event_date'] = $eventData['event_date']->format('Y-m-d');
                } elseif (is_string($eventData['event_date'])) {
                    // Try to parse and format
                    try {
                        $eventData['event_date'] = \Carbon\Carbon::parse($eventData['event_date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        Log::warning('Could not parse event_date: ' . $eventData['event_date']);
                    }
                }
            }

            // Format time to H:i:s (Supabase TIME type)
            if (isset($eventData['event_time'])) {
                if (is_string($eventData['event_time'])) {
                    // Convert "04:00 PM" to "16:00:00" format
                    try {
                        $time = \Carbon\Carbon::createFromFormat('g:i A', $eventData['event_time']);
                        $eventData['event_time'] = $time->format('H:i:s');
                    } catch (\Exception $e) {
                        // Try other formats
                        try {
                            $time = \Carbon\Carbon::createFromFormat('H:i', $eventData['event_time']);
                            $eventData['event_time'] = $time->format('H:i:s');
                        } catch (\Exception $e2) {
                            // If already in H:i:s format, keep it
                            if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventData['event_time'])) {
                                Log::warning('Could not parse event_time: ' . $eventData['event_time']);
                            }
                        }
                    }
                }
            }

            // Format end time to H:i:s (Supabase TIME type) when present
            if (isset($eventData['event_end_time'])) {
                if (is_string($eventData['event_end_time'])) {
                    try {
                        $time = \Carbon\Carbon::createFromFormat('H:i', $eventData['event_end_time']);
                        $eventData['event_end_time'] = $time->format('H:i:s');
                    } catch (\Exception $e) {
                        // If already in H:i:s format, keep it; otherwise warn
                        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventData['event_end_time'])) {
                            Log::warning('Could not parse event_end_time: ' . $eventData['event_end_time']);
                        }
                    }
                }
            }

            // Build clean data array with only valid columns
            // Core required fields (must exist in schema)
            $cleanData = [
                'title' => $eventData['title'] ?? null,
                'description' => $eventData['description'] ?? null,
                'event_date' => $eventData['event_date'] ?? null,
                'event_time' => $eventData['event_time'] ?? null,
                'location' => $eventData['location'] ?? null,
                'event_status' => $eventData['event_status'] ?? 'active',
            ];

            // Optional: end time (requires column in Supabase schema)
            if (isset($eventData['event_end_time']) && $eventData['event_end_time'] !== null && $eventData['event_end_time'] !== '') {
                $cleanData['event_end_time'] = $eventData['event_end_time'];
            }
            
            // Optional fields - only add if they have values
            // Note: photo_url might not exist in schema yet, so we'll try without it first if it fails
            if (isset($eventData['max_participants']) && $eventData['max_participants'] !== null && $eventData['max_participants'] !== '') {
                $cleanData['max_participants'] = (int) $eventData['max_participants'];
            }
            
            if (isset($eventData['created_by']) && $eventData['created_by'] !== null && $eventData['created_by'] !== '') {
                $cleanData['created_by'] = $eventData['created_by'];
            }

            // Add timestamps (Supabase will use defaults if not provided, but we set them explicitly)
            $cleanData['created_at'] = now()->toISOString();
            $cleanData['updated_at'] = now()->toISOString();
            
            // Validate required fields
            $requiredFields = ['title', 'description', 'event_date', 'event_time', 'location'];
            foreach ($requiredFields as $field) {
                if (!isset($cleanData[$field]) || $cleanData[$field] === null || $cleanData[$field] === '') {
                    Log::error('Missing required field when creating event', [
                        'field' => $field,
                        'event_data' => $cleanData,
                    ]);
                    return ['error' => "Missing required field: {$field}"];
                }
            }
            
            // Always include photo_url if provided (column should exist now)
            if (isset($eventData['photo_url']) && !empty($eventData['photo_url']) && $eventData['photo_url'] !== 'null') {
                $cleanData['photo_url'] = $eventData['photo_url'];
                Log::debug('Including photo_url in event creation', [
                    'photo_url' => $eventData['photo_url'],
                ]);
            } else {
                Log::debug('No photo_url provided for event creation', [
                    'photo_url_value' => $eventData['photo_url'] ?? 'not set',
                ]);
            }

            // Log the data being sent for debugging
            Log::debug('Creating event in Supabase', [
                'event_data' => $cleanData,
            ]);

            // Privileged insert
            $result = $this->supabase->from('events')
                ->insertPrivileged([$cleanData])
            ;

            // Check for errors in response
            if (isset($result['error'])) {
                $errorMessage = $result['error'] ?? 'Unknown error';
                $errorDetails = $result;
                
                // If error mentions event_end_time column, retry without it (still allow event creation)
                if ((str_contains(strtolower($errorMessage), 'event_end_time') ||
                     str_contains(strtolower($errorMessage), 'column')) &&
                    isset($cleanData['event_end_time'])) {
                    Log::warning('event_end_time column may not exist, retrying without it', [
                        'error' => $errorMessage,
                    ]);

                    unset($cleanData['event_end_time']);
                    $result = $this->supabase->from('events')
                        ->insertPrivileged([$cleanData]);

                    if (isset($result['error'])) {
                        $errorMessage = $result['error'] ?? 'Unknown error';
                        Log::error('Supabase error creating event (after end_time retry)', [
                            'error' => $errorMessage,
                            'status' => $result['status'] ?? null,
                            'details' => $result,
                            'event_data' => $cleanData,
                        ]);
                        return ['error' => $errorMessage];
                    }
                }

                // If error mentions photo_url column, retry without it
                if ((str_contains(strtolower($errorMessage), 'photo_url') || 
                     str_contains(strtolower($errorMessage), 'column')) && 
                    isset($cleanData['photo_url'])) {
                    Log::warning('photo_url column may not exist, retrying without it', [
                        'error' => $errorMessage,
                    ]);
                    
                    // Remove photo_url and retry
                    unset($cleanData['photo_url']);
                    $result = $this->supabase->from('events')
                        ->insertPrivileged([$cleanData])
                    ;
                    
                    // Check again after retry
                    if (isset($result['error'])) {
                        $errorMessage = $result['error'] ?? 'Unknown error';
                        Log::error('Supabase error creating event (after retry)', [
                            'error' => $errorMessage,
                            'status' => $result['status'] ?? null,
                            'details' => $result,
                            'event_data' => $cleanData,
                        ]);
                        return ['error' => $errorMessage];
                    }
                } else {
                    Log::error('Supabase error creating event', [
                        'error' => $errorMessage,
                        'status' => $result['status'] ?? null,
                        'details' => $errorDetails,
                        'event_data' => $cleanData,
                    ]);
                    return ['error' => $errorMessage];
                }
            }

            // Check if result is empty or invalid
            if (empty($result)) {
                Log::error('Empty response from Supabase when creating event', [
                    'response' => $result,
                    'event_data' => $cleanData,
                ]);
                return ['error' => 'Empty response from database'];
            }

            // Handle response - Supabase returns array of created records
            if (!is_array($result)) {
                Log::error('Invalid response type from Supabase when creating event', [
                    'response' => $result,
                    'response_type' => gettype($result),
                    'event_data' => $cleanData,
                ]);
                return ['error' => 'Invalid response format from database'];
            }

            // Check if result array has errors
            if (isset($result[0]) && isset($result[0]['error'])) {
                $errorMessage = $result[0]['error'] ?? 'Unknown error';
                Log::error('Supabase error in result array', [
                    'error' => $errorMessage,
                    'result' => $result,
                ]);
                return ['error' => $errorMessage];
            }

            // Return the created event (Supabase returns array of created records)
            $created = is_array($result) && count($result) > 0 ? $result[0] : $result;

            if (!isset($created['id'])) {
                Log::error('Created event missing ID', [
                    'created' => $created,
                    'result' => $result,
                ]);
                return ['error' => 'Event created but missing ID'];
            }

            // Log the created event to verify photo_url was saved
            Log::info('Event created successfully in Supabase', [
                'event_id' => $created['id'] ?? null,
                'title' => $created['title'] ?? null,
                'photo_url' => $created['photo_url'] ?? null,
                'photo_url_in_clean_data' => $cleanData['photo_url'] ?? null,
            ]);

            // Audit log
            $this->audit(
                action: 'event.created',
                resourceType: 'Event',
                resourceId: $created['id'],
                payload: [
                    'new' => $created,
                ],
            );

            return $created;
        } catch (\Exception $e) {
            Log::error('Exception creating event: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'event_data' => $eventData ?? [],
            ]);
            return ['error' => 'Failed to create event: ' . $e->getMessage()];
        }
    }

    /**
     * Update an event in Supabase
     * Note: Supabase uses UUIDs, so eventId should be a UUID string
     */
    public function updateEvent(string $eventId, array $eventData): array
    {
        // Skip if Supabase is not configured
        if (!config('supabase.url') || !config('supabase.service_role_key')) {
            return ['error' => 'Supabase not configured'];
        }

        try {
            // Normalize any TIME-ish inputs
            if (isset($eventData['event_time']) && is_string($eventData['event_time'])) {
                try {
                    $eventData['event_time'] = \Carbon\Carbon::createFromFormat('H:i', $eventData['event_time'])->format('H:i:s');
                } catch (\Throwable $e) {
                    // leave as-is if already H:i:s
                }
            }
            if (isset($eventData['event_end_time']) && is_string($eventData['event_end_time'])) {
                try {
                    $eventData['event_end_time'] = \Carbon\Carbon::createFromFormat('H:i', $eventData['event_end_time'])->format('H:i:s');
                } catch (\Throwable $e) {
                    // leave as-is if already H:i:s
                }
            }

            $eventData['updated_at'] = now()->toISOString();

            // Update by ID using the correct API format
            // Supabase REST API requires filter format: id=eq.{value}
            $result = $this->supabase->from('events')
                ->updatePrivileged($eventData, ['id' => 'eq.' . $eventId]);

            if (isset($result['error'])) {
                // If end-time column doesn't exist yet, retry update without it
                $err = strtolower((string) ($result['error'] ?? ''));
                if (isset($eventData['event_end_time']) && (str_contains($err, 'event_end_time') || str_contains($err, 'column'))) {
                    $retryData = $eventData;
                    unset($retryData['event_end_time']);

                    $retry = $this->supabase->from('events')
                        ->updatePrivileged($retryData, ['id' => 'eq.' . $eventId]);

                    if (!isset($retry['error'])) {
                        $updated = is_array($retry) && count($retry) > 0 ? $retry[0] : $retry;
                        return $updated;
                    }
                }

                Log::error('Error updating event in Supabase', [
                    'error' => $result['error'] ?? 'Unknown error',
                    'status' => $result['status'] ?? null,
                    'details' => $result['details'] ?? null,
                    'event_id' => $eventId,
                    'event_data' => $eventData,
                ]);
                return ['error' => 'Failed to update event: ' . ($result['error'] ?? 'Unknown error')];
            }

            // Return the updated event (Supabase returns array of updated records)
            $updated = is_array($result) && count($result) > 0 ? $result[0] : $result;

            $this->audit(
                action: 'event.updated',
                resourceType: 'Event',
                resourceId: $eventId,
                payload: [
                    'new' => $updated,
                    // Optionally include partial update data
                    'changed' => $eventData,
                ],
            );

            return $updated;
        } catch (\Exception $e) {
            Log::error('Error updating event in Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to update event'];
        }
    }

    /**
     * Delete an event from Supabase
     * Note: Supabase uses UUIDs, so eventId should be a UUID string
     */
    public function deleteEvent(string $eventId): array
    {
        // Skip if Supabase is not configured
        if (!config('supabase.url') || !config('supabase.service_role_key')) {
            return ['error' => 'Supabase not configured'];
        }

        try {
            // Delete from Supabase using the correct API format
            // Supabase REST API requires filter format: id=eq.{value}
            $result = $this->supabase->from('events')
                ->deletePrivileged(['id' => 'eq.' . $eventId]);

            if (isset($result['error'])) {
                Log::error('Error deleting event from Supabase: ' . ($result['error'] ?? 'Unknown error'));
                return $result;
            }

            $this->audit(
                action: 'event.deleted',
                resourceType: 'Event',
                resourceId: $eventId,
                payload: null,
            );

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Error deleting event from Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to delete event'];
        }
    }

    /**
     * Get all users with pagination
     */
    public function getUsers(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
            $query = $this->supabase->from('users')
                ->select('id, name, email, role, created_at, updated_at')
                ->order('created_at', 'desc');

            // Apply filters
            if (isset($filters['role'])) {
                $query = $query->eq('role', $filters['role']);
            }

            if (isset($filters['search'])) {
                $query = $query->or('name.ilike.%' . $filters['search'] . '%,email.ilike.%' . $filters['search'] . '%');
            }

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return ['error' => 'Failed to fetch users'];
        }
    }

    /**
     * Get user by ID (UUID string)
     */
    public function getUserById(string $userId)
    {
        try {
            // Use privileged access so admin/backend calls bypass RLS
            $result = $this->supabase->fromPrivileged('users')
                ->select('*')
                ->eq('id', $userId)
                ->single()
                ->execute();

            // Normalize possible [0 => record] shape into a single record
            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return ['error' => 'User not found'];
        }
    }

    /**
     * Get user by email (for compatibility)
     */
    public function getUserByEmail(string $email)
    {
        try {
            // Use privileged access so server-side lookups aren't blocked by RLS
            $result = $this->supabase->fromPrivileged('users')
                ->select('*')
                ->eq('email', $email)
                ->single()
                ->execute();

            // Our SupabaseService::single() is implemented as limit=1, which may still return an array.
            // Normalize to a single record.
            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::debug('User not found by email: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Privileged upsert of a user into Supabase users table.
     * Uses email as conflict target; will insert or update.
     */
    public function upsertUser(array $user): array
    {
        try {
            $email = $user['email'] ?? null;

            // If the caller didn't provide a password, preserve the existing Supabase password
            // instead of overwriting it with an empty string (which breaks email/password login).
            $passwordToSet = null;
            $existingRecord = $email ? $this->getUserByEmail($email) : null;
            if (array_key_exists('password', $user) && $user['password'] !== null && $user['password'] !== '') {
                $passwordToSet = $user['password'];
            } elseif ($existingRecord && is_array($existingRecord)) {
                $passwordToSet = $existingRecord['password'] ?? '';
            } else {
                $passwordToSet = '';
            }

            $payload = [[
                'name' => $user['name'] ?? null,
                'email' => $user['email'] ?? null,
                // Supabase users.password is NOT NULL in schema; store placeholder
                'password' => $passwordToSet,
                'role' => $this->normalizeSupabaseUserRole($user['role'] ?? null),
                'phone' => $user['phone'] ?? null,
                'google_id' => $user['google_id'] ?? null,
                'photo_url' => $user['photo_url'] ?? null,
                'notification_pref' => $user['notification_pref'] ?? true,
                'dark_mode' => $user['dark_mode'] ?? false,
                'email_verified_at' => $user['email_verified_at'] ?? null,
                'two_factor_enabled' => array_key_exists('two_factor_enabled', $user)
                    ? (bool) $user['two_factor_enabled']
                    : (isset($existingRecord['two_factor_enabled']) ? (bool) $existingRecord['two_factor_enabled'] : false),
                'updated_at' => now()->toISOString(),
            ]];

            $result = $this->supabase->from('users')->insertPrivileged($payload, 'email');

            // Sometimes PostgREST can return an empty array even with return=representation
            // (e.g., depending on conflict handling). If so, re-fetch by email.
            if (is_array($result) && empty($result) && !empty($user['email'])) {
                $refetched = $this->getUserByEmail($user['email']);
                if (is_array($refetched) && !isset($refetched['error'])) {
                    return [$refetched];
                }
            }

            if (!isset($result['error'])) {
                $userRecord = is_array($result) && isset($result[0]) ? $result[0] : ($result ?: $payload[0]);

                $this->audit(
                    action: 'user.upserted',
                    resourceType: 'User',
                    resourceId: $userRecord['id'] ?? ($user['id'] ?? ($user['email'] ?? null)),
                    payload: [
                        'new' => $userRecord,
                    ],
                );
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error upserting user to Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to upsert user to Supabase'];
        }
    }

    /**
     * Upsert a local audit log into Supabase (idempotent by local_audit_log_id).
     */
    public function upsertAuditLog(array $auditLog): array
    {
        try {
            if (!config('supabase.url') || !config('supabase.service_role_key')) {
                return ['error' => 'Supabase not configured'];
            }

            $payload = [[
                'local_audit_log_id' => $auditLog['local_audit_log_id'],
                'user_id' => $auditLog['user_id'] ?? null,
                'local_user_id' => $auditLog['local_user_id'] ?? null,
                'user_email' => $auditLog['user_email'] ?? null,
                'action' => $auditLog['action'],
                'resource_type' => $auditLog['resource_type'],
                'resource_id' => $auditLog['resource_id'] ?? null,
                'payload' => $auditLog['payload'] ?? null,
                // Preserve original timestamps when syncing
                'created_at' => $auditLog['created_at'] ?? now()->toISOString(),
                'updated_at' => $auditLog['updated_at'] ?? now()->toISOString(),
            ]];

            return $this->supabase
                ->from('audit_logs')
                ->insertPrivileged($payload, 'local_audit_log_id');
        } catch (\Throwable $e) {
            Log::error('Error upserting audit log to Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to upsert audit log to Supabase'];
        }
    }

    /**
     * Upsert a password reset token row into Supabase (idempotent by email).
     */
    public function upsertPasswordResetToken(array $row): array
    {
        try {
            if (!config('supabase.url') || !config('supabase.service_role_key')) {
                return ['error' => 'Supabase not configured'];
            }

            $payload = [[
                'email' => $row['email'],
                'token' => $row['token'],
                'created_at' => $row['created_at'] ?? null,
            ]];

            return $this->supabase
                ->from('password_reset_tokens')
                ->insertPrivileged($payload, 'email');
        } catch (\Throwable $e) {
            Log::error('Error upserting password reset token to Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to upsert password reset token to Supabase'];
        }
    }

    /**
     * Get event registrations with filters
     */
    public function getEventRegistrations(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
            // Use privileged access so admin views can see all registrations regardless of RLS
            $query = $this->supabase->fromPrivileged('event_registrations')
                // NOTE: There are multiple relationships between event_registrations <-> users/events.
                // Specify relationships explicitly to avoid PGRST201 ambiguity.
                ->select('*, user:users!event_registrations_user_id_fkey(name, email), event:events!event_registrations_event_id_fkey(title, event_date)')
                ->order('created_at', 'desc');

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->eq('registration_status', $filters['status']);
            }

            if (isset($filters['event_id'])) {
                $query = $query->eq('event_id', $filters['event_id']);
            }

            if (isset($filters['user_id'])) {
                $query = $query->eq('user_id', $filters['user_id']);
            }

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            $result = $query->execute();

            // Guard against PostgREST error payloads like PGRST201 which come back
            // as a single associative array instead of a list of rows.
            if (is_array($result) && isset($result['code']) && isset($result['message']) && !isset($result[0])) {
                Log::error('Supabase getEventRegistrations error payload', $result);
                return ['error' => $result['message'] ?? 'Supabase error', 'code' => $result['code'] ?? null];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error fetching registrations: ' . $e->getMessage());
            return ['error' => 'Failed to fetch registrations'];
        }
    }

    /**
     * Lightweight helper: get all registrations for a single user (by Supabase UUID).
     * Used for the public events list to decide Join/Pending/Approved per event.
     */
    public function getEventRegistrationsForUser(string $userId)
    {
        try {
            return $this->supabase->fromPrivileged('event_registrations')
                ->select('user_id,event_id,registration_status,created_at')
                ->eq('user_id', $userId)
                ->order('created_at', 'desc')
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching registrations for user', [
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);
            return ['error' => 'Failed to fetch user registrations'];
        }
    }

    /**
     * Register user for an event
     * Accepts UUID strings for user_id and event_id
     */
    public function registerForEvent(string $userId, string $eventId, array $additionalData = [])
    {
        try {
            $registrationData = array_merge([
                'user_id' => $userId,
                'event_id' => $eventId,
                'registration_status' => 'pending',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ], $additionalData);

            $result = $this->supabase->from('event_registrations')
                ->insertPrivileged([$registrationData], 'user_id,event_id');

            if (isset($result['error'])) {
                return $result;
            }

            $created = is_array($result) && count($result) > 0 ? $result[0] : $result;

            $this->audit(
                action: 'registration.created',
                resourceType: 'EventRegistration',
                resourceId: $created['id'] ?? null,
                payload: [
                    'new' => $created,
                ],
            );

            return $created;
        } catch (\Exception $e) {
            Log::error('Error registering for event: ' . $e->getMessage());
            return ['error' => 'Failed to register for event'];
        }
    }

    /**
     * Get event registration by user and event
     * Accepts UUID strings
     */
    public function getEventRegistration(string $userId, string $eventId)
    {
        try {
            // Use privileged access so server-side lookups aren't blocked by RLS.
            // This must match the behavior used on the events list (getEventRegistrationsForUser).
            $result = $this->supabase->fromPrivileged('event_registrations')
                ->select('*')
                ->eq('user_id', $userId)
                ->eq('event_id', $eventId)
                ->single()
                ->execute();

            // Normalize possible [0 => record] shape into a single record
            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::debug('Event registration not found: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an event registration from Supabase
     */
    public function deleteEventRegistration(string $registrationId): array
    {
        try {
            $result = $this->supabase->from('event_registrations')
                ->deletePrivileged(['id' => 'eq.' . $registrationId]);

            if (!isset($result['error'])) {
                $this->audit(
                    action: 'registration.deleted',
                    resourceType: 'EventRegistration',
                    resourceId: $registrationId,
                );
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error deleting event registration: ' . $e->getMessage());
            return ['error' => 'Failed to delete event registration'];
        }
    }

    /**
     * Delete event registration by user and event
     * Accepts UUID strings
     */
    public function deleteEventRegistrationByUserAndEvent(string $userId, string $eventId): array
    {
        try {
            $result = $this->supabase->from('event_registrations')
                ->deletePrivileged([
                    'user_id' => 'eq.' . $userId,
                    'event_id' => 'eq.' . $eventId,
                ]);

            if (isset($result['error'])) {
                return $result;
            }

            $this->audit(
                action: 'registration.deleted',
                resourceType: 'EventRegistration',
                resourceId: null,
                payload: [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                ],
            );

            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Error deleting event registration: ' . $e->getMessage());
            return ['error' => 'Failed to delete event registration'];
        }
    }

    /**
     * Find member by name (for compatibility)
     */
    public function findMemberByName(string $name)
    {
        try {
            $members = $this->supabase->from('members')
                ->select('*')
                ->eq('name', $name)
                ->limit(1)
                ->execute();

            if (is_array($members) && count($members) > 0) {
                return $members[0];
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error finding member by name: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update registration status
     * Accepts UUID string for registrationId
     */
    public function updateRegistrationStatus(string $registrationId, string $status): array
    {
        try {
            $result = $this->supabase->from('event_registrations')
                ->updatePrivileged([
                    'registration_status' => $status,
                    'updated_at' => now()->toISOString(),
                ], ['id' => 'eq.' . $registrationId]);

            if (isset($result['error'])) {
                return $result;
            }

            return is_array($result) && count($result) > 0 ? $result[0] : $result;
        } catch (\Exception $e) {
            Log::error('Error updating registration: ' . $e->getMessage());
            return ['error' => 'Failed to update registration'];
        }
    }

    /**
     * Notifications: fetch notifications for a user (Supabase UUID).
     */
    public function getNotificationsForUser(string $userId, int $limit = 50): array
    {
        try {
            return $this->supabase->fromPrivileged('notifications')
                ->select('*')
                ->eq('user_id', $userId)
                ->order('created_at', 'desc')
                ->limit($limit)
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return ['error' => 'Failed to fetch notifications'];
        }
    }

    /**
     * Notifications: create a notification (privileged insert).
     */
    public function createNotification(array $data): array
    {
        try {
            $payload = [[
                'user_id' => $data['user_id'] ?? null,
                'type' => $data['type'] ?? 'system',
                'title' => $data['title'] ?? 'Notification',
                'body' => $data['body'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'read_at' => $data['read_at'] ?? null,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('notifications')->insertPrivileged($payload);
        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            return ['error' => 'Failed to create notification'];
        }
    }

    /**
     * Notifications: mark as read (privileged update).
     */
    public function markNotificationRead(string $notificationId): array
    {
        try {
            return $this->supabase->from('notifications')->updatePrivileged([
                'read_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ], ['id' => 'eq.' . $notificationId]);
        } catch (\Exception $e) {
            Log::error('Error marking notification read: ' . $e->getMessage());
            return ['error' => 'Failed to mark notification read'];
        }
    }

    /**
     * Registrations: fetch a registration by UUID with related user/event (privileged).
     */
    public function getEventRegistrationById(string $registrationId): array
    {
        try {
            $result = $this->supabase->fromPrivileged('event_registrations')
                // Keep this query minimal and reliable (do not depend on PostgREST embedded relationships)
                ->select('id,user_id,event_id,registration_status,created_at')
                ->eq('id', $registrationId)
                ->single()
                ->execute();

            if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                return $result[0];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error fetching registration by id: ' . $e->getMessage());
            return ['error' => 'Registration not found'];
        }
    }

    /** Contacts: privileged upsert by email+subject+message hash */
    public function upsertContact(array $data): array
    {
        try {
            $payload = [[
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'] ?? 'Contact',
                'message' => $data['message'] ?? null,
                'contact_status' => $data['contact_status'] ?? 'new',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('contacts')->insertPrivileged($payload);
        } catch (\Exception $e) {
            Log::error('Error upserting contact: ' . $e->getMessage());
            return ['error' => 'Failed to upsert contact'];
        }
    }

    /**
     * Get all members with pagination
     */
    public function getMembers(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
            $query = $this->supabase->from('members')
                ->select('*')
                ->order('order', 'asc')
                ->order('name', 'asc');

            // Apply filters
            if (isset($filters['status'])) {
                $query = $query->eq('member_status', $filters['status']);
            }

            if (isset($filters['search'])) {
                $query = $query->or('name.ilike.%' . $filters['search'] . '%,email.ilike.%' . $filters['search'] . '%');
            }

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching members: ' . $e->getMessage());
            return ['error' => 'Failed to fetch members'];
        }
    }

    /**
     * Get member by ID
     */
    public function getMemberById(string $memberId)
    {
        try {
            return $this->supabase->from('members')
                ->select('*')
                ->eq('id', $memberId)
                ->single()
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching member: ' . $e->getMessage());
            return ['error' => 'Member not found'];
        }
    }

    /** Members: privileged upsert by email */
    public function upsertMember(array $data): array
    {
        try {
            $payload = [[
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'skills' => $data['skills'] ?? null,
                'availability' => $data['availability'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'member_status' => $data['member_status'] ?? 'active',
                'role' => $data['role'] ?? null,
                'photo_url' => $data['photo_url'] ?? null,
                'order' => $data['order'] ?? 0,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('members')->insertPrivileged($payload, 'email');
        } catch (\Exception $e) {
            Log::error('Error upserting member: ' . $e->getMessage());
            return ['error' => 'Failed to upsert member'];
        }
    }

    /**
     * Update a member in Supabase
     */
    public function updateMember(string $memberId, array $memberData): array
    {
        try {
            $memberData['updated_at'] = now()->toISOString();

            return $this->supabase->from('members')
                ->updatePrivileged($memberData, ['id' => 'eq.' . $memberId]);
        } catch (\Exception $e) {
            Log::error('Error updating member: ' . $e->getMessage());
            return ['error' => 'Failed to update member'];
        }
    }

    /**
     * Delete a member from Supabase
     */
    public function deleteMember(string $memberId): array
    {
        try {
            return $this->supabase->from('members')
                ->deletePrivileged(['id' => 'eq.' . $memberId]);
        } catch (\Exception $e) {
            Log::error('Error deleting member: ' . $e->getMessage());
            return ['error' => 'Failed to delete member'];
        }
    }

    /** Settings: privileged upsert by setting_key */
    public function upsertSetting(array $data): array
    {
        try {
            $payload = [[
                'setting_key' => $data['setting_key'] ?? null,
                'setting_value' => $data['setting_value'] ?? null,
                'setting_type' => $data['setting_type'] ?? 'string',
                'description' => $data['description'] ?? null,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('settings')->insertPrivileged($payload, 'setting_key');
        } catch (\Exception $e) {
            Log::error('Error upserting setting: ' . $e->getMessage());
            return ['error' => 'Failed to upsert setting'];
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            $stats = [];

            // Get total counts
            $stats['total_events'] = $this->supabase->from('events')
                ->select('count')
                ->execute();

            $stats['total_users'] = $this->supabase->from('users')
                ->select('count')
                ->execute();

            $stats['total_registrations'] = $this->supabase->from('event_registrations')
                ->select('count')
                ->execute();

            $stats['pending_registrations'] = $this->supabase->from('event_registrations')
                ->select('count')
                ->eq('registration_status', 'pending')
                ->execute();

            // Get recent activities
            $stats['recent_events'] = $this->supabase->from('events')
                ->select('id, title, event_date, created_at')
                ->order('created_at', 'desc')
                ->limit(5)
                ->execute();

            $stats['recent_registrations'] = $this->supabase->from('event_registrations')
                ->select('*, user:users!event_registrations_user_id_fkey(name), event:events!event_registrations_event_id_fkey(title)')
                ->order('created_at', 'desc')
                ->limit(10)
                ->execute();

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return ['error' => 'Failed to fetch dashboard statistics'];
        }
    }

    /**
     * Search across multiple tables
     */
    public function search(string $query, array $tables = ['events', 'users'])
    {
        try {
            $results = [];

            foreach ($tables as $table) {
                switch ($table) {
                    case 'events':
                        $results['events'] = $this->supabase->from('events')
                            ->select('id, title, description, date, location')
                            ->or('title.ilike.%' . $query . '%,description.ilike.%' . $query . '%,location.ilike.%' . $query . '%')
                            ->limit(10)
                            ->execute();
                        break;

                    case 'users':
                        $results['users'] = $this->supabase->from('users')
                            ->select('id, name, email, role')
                            ->or('name.ilike.%' . $query . '%,email.ilike.%' . $query . '%')
                            ->limit(10)
                            ->execute();
                        break;
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Error searching: ' . $e->getMessage());
            return ['error' => 'Search failed'];
        }
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(string $period = '30d')
    {
        try {
            $dateFrom = match($period) {
                '7d' => now()->subDays(7),
                '30d' => now()->subDays(30),
                '90d' => now()->subDays(90),
                '1y' => now()->subYear(),
                default => now()->subDays(30),
            };

            $analytics = [];

            // Event registrations over time
            $analytics['registrations_over_time'] = $this->supabase->from('event_registrations')
                ->select('created_at, status')
                ->gte('created_at', $dateFrom->toISOString())
                ->order('created_at', 'asc')
                ->execute();

            // Popular events
            $analytics['popular_events'] = $this->supabase->from('event_registrations')
                ->select('event_id, count')
                ->gte('created_at', $dateFrom->toISOString())
                ->group('event_id')
                ->order('count', 'desc')
                ->limit(10)
                ->execute();

            // User activity
            $analytics['user_activity'] = $this->supabase->from('users')
                ->select('created_at, role')
                ->gte('created_at', $dateFrom->toISOString())
                ->order('created_at', 'asc')
                ->execute();

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Error fetching analytics: ' . $e->getMessage());
            return ['error' => 'Failed to fetch analytics'];
        }
    }

    /**
     * Fetch all events from Supabase (privileged, for reverse sync into SQLite).
     */
    public function getAllEventsForSync(): array
    {
        try {
            // Use the same anon-accessible query as the main app so we definitely
            // see the same events that appear in your UI.
            $results = $this->supabase->from('events')
                ->select('*')
                ->order('event_date', 'asc')
                ->execute();

            // Log count for easier debugging if needed
            if (is_array($results)) {
                Log::info('getAllEventsForSync fetched events', ['count' => count($results)]);
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Error fetching all events for sync: ' . $e->getMessage());
            return ['error' => 'Failed to fetch events for sync'];
        }
    }

    /**
     * Fetch all event registrations with related user + event for reverse sync.
     * We use user.email and event (title+event_date+location) to map back to local rows.
     */
    public function getAllEventRegistrationsForSync(): array
    {
        try {
            // Fetch raw registrations; we will resolve related user + event in PHP
            return $this->supabase->fromPrivileged('event_registrations')
                ->select('*')
                ->order('created_at', 'asc')
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching registrations for sync: ' . $e->getMessage());
            return ['error' => 'Failed to fetch registrations for sync'];
        }
    }

    /**
     * Create a new message in Supabase
     */
    public function createMessage(array $messageData): array
    {
        try {
            $messageData['created_at'] = now()->toISOString();
            $messageData['updated_at'] = now()->toISOString();

            $result = $this->supabase->from('messages')
                ->insertPrivileged([$messageData]);

            if (isset($result['error'])) {
                return $result;
            }

            // Return the created message (Supabase returns array of created records)
            return is_array($result) && count($result) > 0 ? $result[0] : $result;
        } catch (\Exception $e) {
            Log::error('Error creating message: ' . $e->getMessage());
            return ['error' => 'Failed to create message'];
        }
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(string $userId1, string $userId2, int $page = 1, int $limit = 100)
    {
        try {
            $query = $this->supabase->from('messages')
                ->select('*, sender:users(name, email), receiver:users(name, email)')
                ->or(`and(sender_id.eq.${userId1},receiver_id.eq.${userId2}),and(sender_id.eq.${userId2},receiver_id.eq.${userId1})`)
                ->order('created_at', 'asc');

            // Apply pagination
            $offset = ($page - 1) * $limit;
            $query = $query->range($offset, $offset + $limit - 1);

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching messages: ' . $e->getMessage());
            return ['error' => 'Failed to fetch messages'];
        }
    }
}
