<?php

namespace App\Http\Controllers;

use App\Services\DatabaseQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class VolunteerDashboardController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
    }

    private const DASHBOARD_STATS_CACHE_TTL_SECONDS = 60;

    private function normalizePhotoUrl(?string $photoUrl): ?string
    {
        if (!$photoUrl || $photoUrl === 'null' || trim($photoUrl) === '') {
            return null;
        }

        if (str_starts_with($photoUrl, 'http://') || str_starts_with($photoUrl, 'https://')) {
            return $photoUrl;
        }

        if (str_starts_with($photoUrl, '/storage/')) {
            return $photoUrl;
        }

        if (str_starts_with($photoUrl, 'storage/')) {
            return '/' . $photoUrl;
        }

        return '/storage/' . ltrim($photoUrl, '/');
    }

    /**
     * Build participation statistics for a given volunteer user.
     * This can be reused on the volunteer dashboard or profile page.
     */
    public function buildStatsForUser($user): array
    {
        $email = $user?->email ? (string) $user->email : null;
        if ($email) {
            $cacheKey = 'dashboard:volunteer:v1:' . sha1($email);
            return Cache::remember($cacheKey, self::DASHBOARD_STATS_CACHE_TTL_SECONDS, function () use ($user) {
                return $this->buildStatsForUserUncached($user);
            });
        }

        return $this->buildStatsForUserUncached($user);
    }

    private function buildStatsForUserUncached($user): array
    {
        // Resolve Supabase user ID from local user (by email)
        $supabaseUserId = null;
        if ($user && $user->email) {
            try {
                $supabaseUser = $this->queryService->getUserByEmail($user->email);
                if ($supabaseUser && !isset($supabaseUser['error'])) {
                    $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;
                }
            } catch (\Throwable $e) {
                Log::error('Volunteer dashboard: failed to resolve Supabase user', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        // Fetch upcoming events from Supabase
        $today = now()->startOfDay();
        $allEventsResult = $this->queryService->getEvents(1, 1000, [
            'date_from' => $today->format('Y-m-d'),
        ]);

        $events = [];
        if (is_array($allEventsResult) && !isset($allEventsResult['error'])) {
            $events = array_map(function ($event) {
                $photoUrl = $event['photo_url'] ?? null;
                $photoUrl = $this->normalizePhotoUrl($photoUrl);

                return (object) [
                    'id' => $event['id'] ?? null,
                    'title' => $event['title'] ?? '',
                    'description' => $event['description'] ?? '',
                    'date' => isset($event['event_date']) ? Carbon::parse($event['event_date']) : null,
                    'time' => $event['event_time'] ?? '',
                    'location' => $event['location'] ?? '',
                    'photo_url' => $photoUrl,
                    'created_by' => $event['created_by'] ?? null,
                    'event_status' => $event['event_status'] ?? 'active',
                ];
            }, $allEventsResult);
        }

        // Index events by ID for quick lookup
        $eventsById = [];
        foreach ($events as $ev) {
            if (!empty($ev->id)) {
                $eventsById[$ev->id] = $ev;
            }
        }

        // Load this volunteer's registrations from Supabase
        $registrations = [];
        if ($supabaseUserId) {
            $regsResult = $this->queryService->getEventRegistrations(1, 1000, ['user_id' => $supabaseUserId]);
            if (is_array($regsResult) && !isset($regsResult['error'])) {
                foreach ($regsResult as $reg) {
                    $eventId = $reg['event_id'] ?? null;
                    $eventObj = $eventsById[$eventId] ?? null;

                    $registrations[] = (object) [
                        'id' => $reg['id'] ?? null,
                        'status' => strtolower($reg['registration_status'] ?? 'pending'),
                        'created_at' => isset($reg['created_at']) ? Carbon::parse($reg['created_at']) : null,
                        'event_id' => $eventId,
                        'event' => $eventObj,
                    ];
                }
            }
        }

        // Build collections for view
        $registeredEventIds = collect($registrations)->pluck('event_id')->filter()->unique()->values()->all();

        $upcomingRegisteredEvents = collect($registrations)
            ->filter(function ($reg) {
                return $reg->status === 'approved' && $reg->event && $reg->event->date !== null;
            })
            ->sortBy(function ($reg) {
                return $reg->event->date->format('Y-m-d') . ' ' . ($reg->event->time ?? '00:00:00');
            })
            ->take(5)
            ->map(function ($reg) {
                return $reg->event;
            });

        $availableEvents = collect($events)
            ->filter(function ($ev) use ($registeredEventIds, $today) {
                return $ev->date !== null
                    && $ev->date->greaterThanOrEqualTo($today)
                    && !in_array($ev->id, $registeredEventIds, true);
            })
            ->sortBy(function ($ev) {
                return $ev->date->format('Y-m-d') . ' ' . ($ev->time ?? '00:00:00');
            })
            ->take(5)
            ->values();

        // Stats based on Supabase registrations
        $registrationsCollection = collect($registrations);
        $approvedRegs = $registrationsCollection->where('status', 'approved');

        $stats = [
            'total_registrations' => $registrationsCollection->count(),
            'approved_registrations' => $approvedRegs->count(),
            'pending_registrations' => $registrationsCollection->where('status', 'pending')->count(),
            'rejected_registrations' => $registrationsCollection->where('status', 'rejected')->count(),
            'upcoming_registered_events' => $upcomingRegisteredEvents->count(),
            'available_events' => $availableEvents->count(),
            'recent_registrations' => $registrationsCollection
                ->sortByDesc('created_at')
                ->take(10)
                ->values(),
        ];

        // Quick stats
        $quickStats = [
            'upcoming_events' => collect($events)->filter(function ($ev) use ($today) {
                return $ev->date !== null && $ev->date->greaterThanOrEqualTo($today);
            })->count(),
            'registrations_today' => $registrationsCollection->filter(function ($reg) {
                return $reg->created_at && $reg->created_at->isToday();
            })->count(),
            'events_this_month' => collect($events)->filter(function ($ev) {
                return $ev->date !== null
                    && $ev->date->month === now()->month
                    && $ev->date->year === now()->year;
            })->count(),
            'my_registrations_this_month' => $registrationsCollection->filter(function ($reg) {
                return $reg->created_at
                    && $reg->created_at->month === now()->month
                    && $reg->created_at->year === now()->year;
            })->count(),
        ];

        // Participation rate
        $totalEvents = count($events);
        $participationRate = $totalEvents > 0
            ? round(($stats['approved_registrations'] / $totalEvents) * 100, 1)
            : 0;

        // Monthly trend (last 5 months) of user's approved registrations
        $trendBuckets = [];
        foreach ($approvedRegs as $reg) {
            if ($reg->created_at) {
                $ym = $reg->created_at->format('Y-m');
                $trendBuckets[$ym] = ($trendBuckets[$ym] ?? 0) + 1;
            }
        }

        $months = [];
        for ($i = 4; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $months[$m] = (int) ($trendBuckets[$m] ?? 0);
        }

        $vals = array_values($months);
        $prev = max(1, $vals[count($vals) - 2] ?? 1);
        $curr = $vals[count($vals) - 1] ?? 0;
        $growthRate = round((($curr - $prev) / $prev) * 100, 1);

        $analytics = [
            'event_success_pct' => $approvedRegs->count() > 0 && $stats['total_registrations'] > 0
                ? round(($approvedRegs->count() / max(1, $stats['total_registrations'])) * 100)
                : 0,
            'avg_attendance' => $approvedRegs->count() > 0 && $totalEvents > 0
                ? round($approvedRegs->count() / max(1, $totalEvents))
                : 0,
            'growth_rate_pct' => $growthRate,
            'trend' => $months,
            'participation_rate' => $participationRate,
        ];

        return [
            'stats' => $stats,
            'analytics' => $analytics,
            'quickStats' => $quickStats,
            'upcomingRegisteredEvents' => $upcomingRegisteredEvents,
            'availableEvents' => $availableEvents,
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $data = $this->buildStatsForUser($user);

        return view('volunteer.dashboard', $data);
    }
}

