<?php

namespace App\Http\Controllers;

use App\Services\DatabaseQueryService;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupabaseController extends Controller
{
    protected DatabaseQueryService $queryService;
    protected SupabaseService $supabaseService;

    public function __construct(DatabaseQueryService $queryService, SupabaseService $supabaseService)
    {
        $this->queryService = $queryService;
        $this->supabaseService = $supabaseService;
    }

    /**
     * Get all events from Supabase
     */
    public function getEvents(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $filters = $request->only(['status', 'date_from', 'date_to']);

        $events = $this->queryService->getEvents($page, $limit, $filters);

        return response()->json([
            'success' => true,
            'data' => $events,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
            ]
        ]);
    }

    /**
     * Get single event from Supabase
     */
    public function getEvent(Request $request, int $id): JsonResponse
    {
        $event = $this->queryService->getEventById($id);

        if (isset($event['error'])) {
            return response()->json([
                'success' => false,
                'message' => $event['error']
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    /**
     * Create event in Supabase
     */
    public function createEvent(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required|string',
            'location' => 'required|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'event_status' => 'nullable|in:active,inactive,cancelled',
        ]);

        $eventData = $request->only([
            'title', 'description', 'event_date', 'event_time', 'location', 
            'max_participants', 'event_status'
        ]);
        $eventData['created_by'] = auth()->id();
        $eventData['event_status'] = $eventData['event_status'] ?? 'active';

        $result = $this->queryService->createEvent($eventData);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $result
        ], 201);
    }

    /**
     * Update event in Supabase
     */
    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'event_date' => 'sometimes|date',
            'event_time' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'event_status' => 'sometimes|in:active,inactive,cancelled',
        ]);

        $eventData = $request->only([
            'title', 'description', 'event_date', 'event_time', 'location', 
            'max_participants', 'event_status'
        ]);

        $result = $this->queryService->updateEvent($id, $eventData);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $result
        ]);
    }

    /**
     * Delete event from Supabase
     */
    public function deleteEvent(Request $request, int $id): JsonResponse
    {
        $result = $this->queryService->deleteEvent($id);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Get all users from Supabase
     */
    public function getUsers(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $filters = $request->only(['role', 'search']);

        $users = $this->queryService->getUsers($page, $limit, $filters);

        return response()->json([
            'success' => true,
            'data' => $users,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
            ]
        ]);
    }

    /**
     * Get user by ID from Supabase
     */
    public function getUser(Request $request, int $id): JsonResponse
    {
        $user = $this->queryService->getUserById($id);

        if (isset($user['error'])) {
            return response()->json([
                'success' => false,
                'message' => $user['error']
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Get event registrations from Supabase
     */
    public function getEventRegistrations(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $filters = $request->only(['status', 'event_id', 'user_id']);

        $registrations = $this->queryService->getEventRegistrations($page, $limit, $filters);

        return response()->json([
            'success' => true,
            'data' => $registrations,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
            ]
        ]);
    }

    /**
     * Register user for event
     */
    public function registerForEvent(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = auth()->id();
        $eventId = $request->event_id;
        $additionalData = $request->only(['notes']);

        $result = $this->queryService->registerForEvent($userId, $eventId, $additionalData);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for event',
            'data' => $result
        ], 201);
    }

    /**
     * Update registration status
     */
    public function updateRegistrationStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'registration_status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $result = $this->queryService->updateRegistrationStatus($id, $request->registration_status);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration status updated successfully',
            'data' => $result
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        $stats = $this->queryService->getDashboardStats();

        if (isset($stats['error'])) {
            return response()->json([
                'success' => false,
                'message' => $stats['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Search across multiple tables
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'tables' => 'nullable|array',
            'tables.*' => 'in:events,users,event_registrations',
        ]);

        $query = $request->query;
        $tables = $request->get('tables', ['events', 'users']);

        $results = $this->queryService->search($query, $tables);

        if (isset($results['error'])) {
            return response()->json([
                'success' => false,
                'message' => $results['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'nullable|in:7d,30d,90d,1y',
        ]);

        $period = $request->get('period', '30d');
        $analytics = $this->queryService->getAnalytics($period);

        if (isset($analytics['error'])) {
            return response()->json([
                'success' => false,
                'message' => $analytics['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Upload file to Supabase Storage
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'bucket' => 'nullable|string',
            'path' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $bucket = $request->get('bucket', config('supabase.bucket_name'));
        $path = $request->get('path', 'uploads/' . $file->getClientOriginalName());

        try {
            $result = $this->supabaseService->uploadFile($bucket, $path, $file->getContent());

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'url' => $this->supabaseService->getFileUrl($bucket, $path),
                    'path' => $path,
                    'bucket' => $bucket,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Test Supabase connection
     */
    public function testConnection(): JsonResponse
    {
        try {
            // Simple test query to check connection
            $result = $this->supabaseService->from('users')->select('count')->execute();

            return response()->json([
                'success' => true,
                'message' => 'Supabase connection successful',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supabase connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
}