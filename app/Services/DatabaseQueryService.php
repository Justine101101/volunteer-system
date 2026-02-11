<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\RecordsAuditLogs;

class DatabaseQueryService
{
    use RecordsAuditLogs;
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
     * Accepts UUID string (Supabase) or can find by title+date+location for compatibility
     */
    public function getEventById(string $eventId)
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
            $eventData['created_at'] = now()->toISOString();
            $eventData['updated_at'] = now()->toISOString();

            // Privileged upsert by title+event_date+event_time+location as a simple uniqueness proxy
            $result = $this->supabase->from('events')
                ->insertPrivileged([$eventData])
            ;

            if (isset($result['error'])) {
                return $result;
            }

            // Return the created event (Supabase returns array of created records)
            $created = is_array($result) && count($result) > 0 ? $result[0] : $result;

            // Audit log
            $this->audit(
                action: 'event.created',
                resourceType: 'Event',
                resourceId: $created['id'] ?? null,
                payload: [
                    'new' => $created,
                ],
            );

            return $created;
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return ['error' => 'Failed to create event'];
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
            $eventData['updated_at'] = now()->toISOString();

            // Update by ID using the correct API format
            // Supabase REST API requires filter format: id=eq.{value}
            $result = $this->supabase->from('events')
                ->updatePrivileged($eventData, ['id' => 'eq.' . $eventId]);

            if (isset($result['error'])) {
                Log::error('Error updating event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
                return $result;
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
     * Get user by email (for compatibility)
     */
    public function getUserByEmail(string $email)
    {
        try {
            return $this->supabase->from('users')
                ->select('*')
                ->eq('email', $email)
                ->single()
                ->execute();
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

            $result = $this->supabase->from('users')->insertPrivileged($payload, 'email');

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
            return $this->supabase->from('event_registrations')
                ->select('*')
                ->eq('user_id', $userId)
                ->eq('event_id', $eventId)
                ->single()
                ->execute();
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
