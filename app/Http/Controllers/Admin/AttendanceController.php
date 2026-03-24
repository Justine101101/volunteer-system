<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        // Use the embedded `user` and `event` data that already comes from Supabase
        // to avoid doing hundreds of extra HTTP requests (which were causing timeouts).
        $items = collect(is_array($raw) && !isset($raw['error']) ? $raw : [])->map(function ($reg) {
            $registrationId = $reg['id'] ?? null;
            $status = $reg['registration_status'] ?? ($reg['status'] ?? 'pending');
            $createdAt = isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at']) : null;

            // Prefer embedded user if present; fall back to minimal object
            $embeddedUser = $reg['user'] ?? null;
            $user = null;
            if (is_array($embeddedUser)) {
                $user = (object) [
                    'name' => $embeddedUser['name'] ?? 'Unknown',
                    'email' => $embeddedUser['email'] ?? null,
                ];
            }

            // Resolve local Laravel user so Attendance can link to the same admin profile page.
            $localUserId = null;
            if (!empty($user?->email)) {
                $localUserId = User::query()
                    ->where('email', $user->email)
                    ->value('id');
            }

            // Prefer embedded event if present; fall back to minimal object
            $embeddedEvent = $reg['event'] ?? null;
            $event = null;
            if (is_array($embeddedEvent)) {
                $event = (object) [
                    'title' => $embeddedEvent['title'] ?? '',
                ];
            }

            return (object) [
                'id' => $registrationId,
                'registration_status' => $status,
                'created_at' => $createdAt,
                'user' => $user,
                'local_user_id' => $localUserId,
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


