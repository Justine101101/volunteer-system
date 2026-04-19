<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\DatabaseQueryService;

class AttendanceController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Attendance is always scoped to one event; keep this URL for bookmarks and nav.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.attendance.event');
    }

    /**
     * Attendance scoped to a single event (admin selects event first).
     */
    public function event(Request $request)
    {
        $eventId = trim((string) $request->query('event_id', ''));

        $eventsResult = $this->queryService->getEventsPrivileged(1, 1000);
        $events = [];
        if (is_array($eventsResult) && !isset($eventsResult['error'])) {
            $events = collect($eventsResult)
                ->filter(fn ($e) => is_array($e) && !empty($e['id']))
                ->map(function ($e) {
                    $date = isset($e['event_date']) ? \Carbon\Carbon::parse((string) $e['event_date']) : null;

                    return (object) [
                        'id' => (string) ($e['id'] ?? ''),
                        'title' => (string) ($e['title'] ?? ''),
                        'date' => $date,
                    ];
                })
                ->sortByDesc(fn ($e) => $e->date?->timestamp ?? 0)
                ->values()
                ->all();
        }

        $selectedEvent = null;
        $registrations = collect();
        $summary = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'present_onsite' => 0,
        ];

        if ($eventId !== '') {
            $selectedEvent = collect($events)->firstWhere('id', $eventId);

            $all = $this->queryService->getEventRegistrations(1, 5000, ['event_id' => $eventId]);
            $pending = $this->queryService->getEventRegistrations(1, 5000, ['event_id' => $eventId, 'status' => 'pending']);
            $approved = $this->queryService->getEventRegistrations(1, 5000, ['event_id' => $eventId, 'status' => 'approved']);
            $rejected = $this->queryService->getEventRegistrations(1, 5000, ['event_id' => $eventId, 'status' => 'rejected']);

            $rows = collect(is_array($all) && !isset($all['error']) ? $all : []);
            $registrations = $this->mapRegistrationRowsToViewModels($rows);
            $summary = $this->buildRegistrationSummary($all, $pending, $approved, $rejected);
        }

        return view('admin.attendance-event', [
            'events' => $events,
            'selectedEventId' => $eventId,
            'selectedEvent' => $selectedEvent,
            'registrations' => $registrations,
            'summary' => $summary,
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection<int, array<string,mixed>> $pageRows
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function mapRegistrationRowsToViewModels(\Illuminate\Support\Collection $pageRows): \Illuminate\Support\Collection
    {
        $emails = $pageRows
            ->pluck('user.email')
            ->filter(fn ($email) => is_string($email) && $email !== '')
            ->unique()
            ->values();

        $localUsersByEmail = User::query()
            ->whereIn('email', $emails->all())
            ->get(['id', 'email'])
            ->keyBy('email');

        return $pageRows->map(function ($reg) use ($localUsersByEmail) {
            $registrationId = $reg['id'] ?? null;
            $status = $reg['registration_status'] ?? ($reg['status'] ?? 'pending');
            $createdAt = isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at']) : null;

            $embeddedUser = $reg['user'] ?? null;
            $user = null;
            if (is_array($embeddedUser)) {
                $user = (object) [
                    'name' => $embeddedUser['name'] ?? 'Unknown',
                    'email' => $embeddedUser['email'] ?? null,
                ];
            }

            $localUser = (!empty($user?->email) && $localUsersByEmail->has($user->email))
                ? $localUsersByEmail->get($user->email)
                : null;
            $localUserId = $localUser?->id;

            $embeddedEvent = $reg['event'] ?? null;
            $event = null;
            if (is_array($embeddedEvent)) {
                $event = (object) [
                    'title' => $embeddedEvent['title'] ?? '',
                ];
            }

            $attendedAt = null;
            if (!empty($reg['attended_at'])) {
                try {
                    $attendedAt = \Carbon\Carbon::parse((string) $reg['attended_at']);
                } catch (\Throwable $e) {
                    $attendedAt = null;
                }
            }

            return (object) [
                'id' => $registrationId,
                'registration_status' => $status,
                'created_at' => $createdAt,
                'attended_at' => $attendedAt,
                'user' => $user,
                'local_user_id' => $localUserId,
                'event' => $event,
            ];
        });
    }

    /**
     * @param mixed $all
     * @param mixed $pending
     * @param mixed $approved
     * @param mixed $rejected
     * @return array{total:int,pending:int,approved:int,rejected:int,present_onsite:int}
     */
    private function buildRegistrationSummary(mixed $all, mixed $pending, mixed $approved, mixed $rejected): array
    {
        $total = (is_array($all) && !isset($all['error'])) ? count($all) : 0;
        $presentOnsite = 0;
        if (is_array($all) && !isset($all['error'])) {
            foreach ($all as $row) {
                if (empty($row['attended_at'])) {
                    continue;
                }
                $st = strtolower((string) ($row['registration_status'] ?? ($row['status'] ?? '')));
                if ($st === 'approved') {
                    $presentOnsite++;
                }
            }
        }

        return [
            'total' => $total,
            'pending' => (is_array($pending) && !isset($pending['error'])) ? count($pending) : 0,
            'approved' => (is_array($approved) && !isset($approved['error'])) ? count($approved) : 0,
            'rejected' => (is_array($rejected) && !isset($rejected['error'])) ? count($rejected) : 0,
            'present_onsite' => $presentOnsite,
        ];
    }
}


