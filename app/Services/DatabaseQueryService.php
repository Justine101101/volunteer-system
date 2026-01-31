<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseQueryService
{
    protected SupabaseService $supabase;

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
        $query = $this->supabase->from('events')
            ->select('*, creator:users(name, email), registrations:event_registrations(count)')
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
     * Get event by ID with full details
     */
    public function getEventById(int $eventId)
    {
        try {
            return $this->supabase->from('events')
                ->select('*, creator:users(name, email), registrations:event_registrations(*, user:users(name, email))')
                ->eq('id', $eventId)
                ->single()
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching event: ' . $e->getMessage());
            return ['error' => 'Event not found'];
        }
    }

    /**
     * Create a new event
     */
    public function createEvent(array $eventData)
    {
        try {
            $eventData['created_at'] = now()->toISOString();
            $eventData['updated_at'] = now()->toISOString();

            // Privileged upsert by title+event_date+event_time+location as a simple uniqueness proxy
            return $this->supabase->from('events')
                ->insertPrivileged([$eventData])
            ;
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return ['error' => 'Failed to create event'];
        }
    }

    /**
     * Update an event in Supabase (best-effort, non-blocking)
     * Note: Supabase uses UUIDs while local DB uses integers, so this may not always work
     */
    public function updateEvent(int $eventId, array $eventData): void
    {
        // Skip if Supabase is not configured
        if (!config('supabase.url') || !config('supabase.service_role_key')) {
            return;
        }

        try {
            $eventData['updated_at'] = now()->toISOString();

            // Try to update by ID using the correct API format
            // Supabase REST API requires filter format: id=eq.{value}
            // May fail if IDs don't match between systems
            $this->supabase->from('events')
                ->updatePrivileged($eventData, ['id' => 'eq.' . (string) $eventId]);
        } catch (\Exception $e) {
            // Silently log and continue - Supabase update is optional
            Log::debug('Could not update event in Supabase (ID mismatch expected): ' . $e->getMessage());
        } catch (\Throwable $e) {
            // Catch any other errors (including fatal errors)
            Log::debug('Could not update event in Supabase: ' . $e->getMessage());
        }
    }

    /**
     * Delete an event from Supabase (best-effort, non-blocking)
     * Note: Supabase uses UUIDs while local DB uses integers, so this may not always work
     */
    public function deleteEvent(int $eventId): void
    {
        // Skip if Supabase is not configured
        if (!config('supabase.url') || !config('supabase.service_role_key')) {
            return;
        }

        try {
            // Attempt to delete from Supabase using the correct API format
            // Supabase REST API requires filter format: id=eq.{value}
            // Since Supabase uses UUIDs and local DB uses integers, this is best-effort only
            $this->supabase->from('events')
                ->deletePrivileged(['id' => 'eq.' . (string) $eventId]);
        } catch (\Exception $e) {
            // Silently log and continue - Supabase deletion is optional
            Log::debug('Could not delete event from Supabase (ID mismatch expected): ' . $e->getMessage());
        } catch (\Throwable $e) {
            // Catch any other errors (including fatal errors)
            Log::debug('Could not delete event from Supabase: ' . $e->getMessage());
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
     * Get user by ID
     */
    public function getUserById(int $userId)
    {
        try {
            return $this->supabase->from('users')
                ->select('*')
                ->eq('id', $userId)
                ->single()
                ->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return ['error' => 'User not found'];
        }
    }

    /**
     * Privileged upsert of a user into Supabase users table.
     * Uses email as conflict target; will insert or update.
     */
    public function upsertUser(array $user): array
    {
        try {
            $payload = [[
                'name' => $user['name'] ?? null,
                'email' => $user['email'] ?? null,
                // Supabase users.password is NOT NULL in schema; store placeholder
                'password' => $user['password'] ?? '',
                'role' => $user['role'] ?? 'volunteer',
                'notification_pref' => $user['notification_pref'] ?? true,
                'dark_mode' => $user['dark_mode'] ?? false,
                'email_verified_at' => $user['email_verified_at'] ?? null,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('users')->insertPrivileged($payload, 'email');
        } catch (\Exception $e) {
            Log::error('Error upserting user to Supabase: ' . $e->getMessage());
            return ['error' => 'Failed to upsert user to Supabase'];
        }
    }

    /**
     * Get event registrations with filters
     */
    public function getEventRegistrations(int $page = 1, int $limit = 10, array $filters = [])
    {
        try {
            $query = $this->supabase->from('event_registrations')
                ->select('*, user:users(name, email), event:events(title, event_date)')
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

            return $query->execute();
        } catch (\Exception $e) {
            Log::error('Error fetching registrations: ' . $e->getMessage());
            return ['error' => 'Failed to fetch registrations'];
        }
    }

    /**
     * Register user for an event
     */
    public function registerForEvent(int $userId, int $eventId, array $additionalData = [])
    {
        try {
            $registrationData = array_merge([
                'user_id' => $userId,
                'event_id' => $eventId,
                'registration_status' => 'pending',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ], $additionalData);

            return $this->supabase->from('event_registrations')
                ->insertPrivileged([$registrationData], 'user_id,event_id')
            ;
        } catch (\Exception $e) {
            Log::error('Error registering for event: ' . $e->getMessage());
            return ['error' => 'Failed to register for event'];
        }
    }

    /**
     * Update registration status
     */
    public function updateRegistrationStatus(int $registrationId, string $status)
    {
        try {
            return $this->supabase->from('event_registrations')
                ->updatePrivileged([
                    'registration_status' => $status,
                    'updated_at' => now()->toISOString(),
                ], ['id' => $registrationId])
            ;
        } catch (\Exception $e) {
            Log::error('Error updating registration: ' . $e->getMessage());
            return ['error' => 'Failed to update registration'];
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
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]];

            return $this->supabase->from('members')->insertPrivileged($payload, 'email');
        } catch (\Exception $e) {
            Log::error('Error upserting member: ' . $e->getMessage());
            return ['error' => 'Failed to upsert member'];
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
                ->select('*, user:users(name), event:events(title)')
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
}
