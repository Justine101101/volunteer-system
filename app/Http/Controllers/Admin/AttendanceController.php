<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DatabaseQueryService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $page = (int) request()->get('page', 1);
        $perPage = 15;

        // Supabase-backed registrations (Single Source of Truth)
        $raw = $this->queryService->getEventRegistrations($page, $perPage);
        $all = $this->queryService->getEventRegistrations(1, 5000);
        $pending = $this->queryService->getEventRegistrations(1, 5000, ['status' => 'pending']);
        $approved = $this->queryService->getEventRegistrations(1, 5000, ['status' => 'approved']);
        $rejected = $this->queryService->getEventRegistrations(1, 5000, ['status' => 'rejected']);

        // Log a small sample so we can see exactly what Supabase returns
        if (is_array($raw)) {
            Log::debug('AttendanceController@index raw registrations sample', [
                'sample' => array_slice($raw, 0, 5),
            ]);
        } else {
            Log::debug('AttendanceController@index raw registrations non-array', [
                'raw' => $raw,
            ]);
        }

        // Build human-friendly objects for the Blade view.
        // Always hydrate user/event via separate privileged lookups so we reliably get names/titles.
        $userCache = [];
        $eventCache = [];

        $items = collect(is_array($raw) && !isset($raw['error']) ? $raw : [])->map(function ($reg) use (&$userCache, &$eventCache) {
            static $debugCount = 0;

            $registrationId = $reg['id'] ?? null;
            $status = $reg['registration_status'] ?? ($reg['status'] ?? 'pending');
            $createdAt = isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at']) : null;

            $user = null;
            $event = null;

            // Hydrate user via Supabase users table (privileged) when we have user_id
            if (!empty($reg['user_id'])) {
                $userId = $reg['user_id'];
                if (!array_key_exists($userId, $userCache)) {
                    $rawUser = app(\App\Services\DatabaseQueryService::class)->getUserById($userId);
                    $userCache[$userId] = is_array($rawUser) && !isset($rawUser['error']) ? $rawUser : null;
                }

                if ($userCache[$userId]) {
                    $user = (object) [
                        'name' => $userCache[$userId]['name'] ?? 'Unknown',
                        'email' => $userCache[$userId]['email'] ?? null,
                    ];
                }
            }

            // Hydrate event via Supabase events table when we have event_id
            if (!empty($reg['event_id'])) {
                $eventId = $reg['event_id'];
                if (!array_key_exists($eventId, $eventCache)) {
                    $rawEvent = app(\App\Services\DatabaseQueryService::class)->getEventById($eventId);
                    $eventCache[$eventId] = is_array($rawEvent) && !isset($rawEvent['error']) ? $rawEvent : null;
                }

                if ($eventCache[$eventId]) {
                    $event = (object) [
                        'title' => $eventCache[$eventId]['title'] ?? '',
                    ];
                }
            }

            // Log first few hydrated rows so we can see why "Unknown" might appear
            if ($debugCount < 5) {
                Log::debug('AttendanceController hydrated registration', [
                    'registration_id' => $registrationId,
                    'status' => $status,
                    'user_id' => $reg['user_id'] ?? null,
                    'event_id' => $reg['event_id'] ?? null,
                    'raw_user_cache' => isset($userId) ? ($userCache[$userId] ?? null) : null,
                    'raw_event_cache' => isset($eventId) ? ($eventCache[$eventId] ?? null) : null,
                ]);
                $debugCount++;
            }

            return (object) [
                'id' => $registrationId,
                'registration_status' => $status,
                'created_at' => $createdAt,
                'user' => $user,
                'event' => $event,
            ];
        });

        $total = (is_array($all) && !isset($all['error'])) ? count($all) : 0;
        $summary = [
            'total' => $total,
            'pending' => (is_array($pending) && !isset($pending['error'])) ? count($pending) : 0,
            'approved' => (is_array($approved) && !isset($approved['error'])) ? count($approved) : 0,
            'rejected' => (is_array($rejected) && !isset($rejected['error'])) ? count($rejected) : 0,
        ];

        // Build a paginator so the Blade view can keep using ->links()
        $registrations = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('admin.attendance', compact('registrations', 'summary'));
    }
}


