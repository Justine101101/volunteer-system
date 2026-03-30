<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Contact;
use App\Models\Member;
use App\Services\DatabaseQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $payload = Cache::remember('admin:dashboard:v1', 60, function () {
            // Supabase-backed events (Single Source of Truth)
            $eventsRaw = $this->queryService->getEventsPrivileged(1, 5000);
            $events = [];
            if (is_array($eventsRaw) && !isset($eventsRaw['error'])) {
                $events = collect($eventsRaw)->map(function ($event) {
                    return (object) [
                        'id' => $event['id'] ?? null,
                        'title' => $event['title'] ?? '',
                        'description' => $event['description'] ?? '',
                        'date' => isset($event['event_date']) ? Carbon::parse($event['event_date']) : null,
                        'time' => $event['event_time'] ?? '',
                        'location' => $event['location'] ?? '',
                        'created_at' => isset($event['created_at']) ? Carbon::parse($event['created_at']) : null,
                        'event_status' => $event['event_status'] ?? 'active',
                    ];
                })->all();
            }

            $totalEventsCount = is_countable($events) ? count($events) : 0;
            $recentEvents = collect($events)
                ->sortByDesc(function ($e) {
                    return $e->created_at?->timestamp
                        ?? $e->date?->timestamp
                        ?? 0;
                })
                ->take(5)
                ->values()
                ->all();

            $upcomingEventsCount = collect($events)->filter(function ($e) {
                return $e->date instanceof Carbon && $e->date->gte(now()->startOfDay());
            })->count();

            // Supabase-backed registrations (Single Source of Truth)
            $recentRegsRaw = $this->queryService->getEventRegistrations(1, 10);
            $pendingRegsRaw = $this->queryService->getEventRegistrations(1, 2000, ['status' => 'pending']);
            $approvedRegsRaw = $this->queryService->getEventRegistrations(1, 2000, ['status' => 'approved']);
            $rejectedRegsRaw = $this->queryService->getEventRegistrations(1, 2000, ['status' => 'rejected']);
            $allRegsRaw = $this->queryService->getEventRegistrations(1, 5000);

            $recentSupabaseRegistrations = [];
            if (is_array($recentRegsRaw) && !isset($recentRegsRaw['error'])) {
                $recentSupabaseRegistrations = collect($recentRegsRaw)->map(function ($reg) {
                    return (object) [
                        'id' => $reg['id'] ?? null,
                        'registration_status' => $reg['registration_status'] ?? ($reg['status'] ?? 'pending'),
                        'created_at' => isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at']) : null,
                        'user' => isset($reg['user']) ? (object) $reg['user'] : null,
                        'event' => isset($reg['event']) ? (object) $reg['event'] : null,
                    ];
                })->all();
            }

            $pendingCount = (is_array($pendingRegsRaw) && !isset($pendingRegsRaw['error'])) ? count($pendingRegsRaw) : 0;
            $approvedCount = (is_array($approvedRegsRaw) && !isset($approvedRegsRaw['error'])) ? count($approvedRegsRaw) : 0;
            $rejectedCount = (is_array($rejectedRegsRaw) && !isset($rejectedRegsRaw['error'])) ? count($rejectedRegsRaw) : 0;
            $totalRegCount = (is_array($allRegsRaw) && !isset($allRegsRaw['error'])) ? count($allRegsRaw) : 0;
            $allRegsArray = (is_array($allRegsRaw) && !isset($allRegsRaw['error'])) ? $allRegsRaw : [];

            $stats = [
                'total_users' => User::count(),
                'total_volunteers' => User::where('role', 'volunteer')->count(),
                'total_events' => $totalEventsCount,
                'total_registrations' => $totalRegCount,
                'total_contacts' => Contact::count(),
                'total_members' => Member::count(),
                'pending_registrations' => $pendingCount,
                'approved_registrations' => $approvedCount,
                'recent_events' => $recentEvents,
                'recent_registrations' => $recentSupabaseRegistrations,
                'recent_contacts' => Contact::latest()->take(5)->get(),
            ];

            $quickStats = [
                'new_volunteers_30d' => User::where('role', 'volunteer')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'upcoming_events' => $upcomingEventsCount,
                'registrations_today' => EventRegistration::whereDate('created_at', now()->toDateString())->count(),
                'contacts_week' => Contact::where('created_at', '>=', now()->subDays(7))->count(),
            ];

            $roleBreakdown = Member::select('role', DB::raw('COUNT(*) as total'))
                ->groupBy('role')
                ->pluck('total', 'role')
                ->toArray();

            $approved = (int) $stats['approved_registrations'];
            $totalRegs = max(1, (int) $stats['total_registrations']);
            $totalEvents = max(1, (int) $stats['total_events']);

            $eventSuccess = round(($approved / $totalRegs) * 100);
            $avgAttendance = round($approved / $totalEvents);

            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i)->startOfMonth();
                $key = $m->format('Y-m');
                $months[$key] = collect($allRegsArray)->filter(function ($r) use ($m) {
                    $status = (string) ($r['registration_status'] ?? ($r['status'] ?? 'pending'));
                    if (strtolower($status) !== 'approved') return false;
                    if (empty($r['created_at'])) return false;
                    try {
                        $c = Carbon::parse((string) $r['created_at']);
                        return $c->year === $m->year && $c->month === $m->month;
                    } catch (\Throwable $e) {
                        return false;
                    }
                })->count();
            }

            $eventsPerMonth = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i)->startOfMonth();
                $key = $m->format('Y-m');
                $eventsPerMonth[$key] = collect(is_array($eventsRaw) ? $eventsRaw : [])->filter(function ($e) use ($m) {
                    if (!is_array($e)) return false;
                    if (empty($e['event_date'])) return false;
                    try {
                        $d = Carbon::parse((string) $e['event_date']);
                        return $d->year === $m->year && $d->month === $m->month;
                    } catch (\Throwable $e) {
                        return false;
                    }
                })->count();
            }

            $vals = array_values($months);
            $prev = max(1, $vals[count($vals) - 2] ?? 1);
            $curr = $vals[count($vals) - 1] ?? 0;
            $growthRate = round((($curr - $prev) / $prev) * 100, 1);

            $analytics = [
                'event_success_pct' => $eventSuccess,
                'avg_attendance' => $avgAttendance,
                'growth_rate_pct' => $growthRate,
                'trend' => $months,
                'by_status' => [
                    'pending' => $pendingCount,
                    'approved' => $approvedCount,
                    'rejected' => $rejectedCount,
                ],
            ];

            return compact('stats', 'roleBreakdown', 'analytics', 'quickStats', 'eventsPerMonth');
        });

        return view('admin.dashboard', $payload);
    }
}