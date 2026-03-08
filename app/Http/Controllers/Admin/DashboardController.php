<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Contact;
use App\Models\Member;
use App\Services\DatabaseQueryService;
use Illuminate\Http\Request;
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

        // Extra defensive logging so dashboard never crashes even if Supabase response shape changes
        $recentSample = null;
        if (is_array($recentRegsRaw) && !isset($recentRegsRaw['error']) && !empty($recentRegsRaw)) {
            // Use first element in a safe way, whether the array is indexed or associative
            $recentSample = reset($recentRegsRaw);
        }

        \Log::debug('Admin dashboard Supabase registrations summary', [
            'recent_count' => is_array($recentRegsRaw) && !isset($recentRegsRaw['error']) ? count($recentRegsRaw) : 'error',
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'total_registrations' => $totalRegCount,
            'recent_sample' => $recentSample,
        ]);

        $stats = [
            'total_users' => User::count(),
            'total_volunteers' => User::where('role', 'volunteer')->count(),
            'total_events' => $totalEventsCount,
            // Use Supabase registrations count
            'total_registrations' => $totalRegCount,
            'total_contacts' => Contact::count(),
            'total_members' => Member::count(),
            // Use Supabase registration statuses
            'pending_registrations' => $pendingCount,
            'approved_registrations' => $approvedCount,
            'recent_events' => $recentEvents,
            // Use Supabase registrations for approvals
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

        // Role breakdown from members as a proxy for skills
        $roleBreakdown = Member::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // Simple analytics for header cards
        $approved = (int) $stats['approved_registrations'];
        $totalRegs = max(1, (int) $stats['total_registrations']);
        $totalEvents = max(1, (int) $stats['total_events']);

        $eventSuccess = round(($approved / $totalRegs) * 100); // % of approved vs all
        $avgAttendance = round($approved / $totalEvents);      // approved per event

        // Monthly trend (last 5 months) of approved registrations
        $trendRows = EventRegistration::select(
                DB::raw("strftime('%Y-%m', created_at) as ym"),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'approved')
            ->groupBy('ym')
            ->orderBy('ym', 'asc')
            ->get()
            ->pluck('total', 'ym');

        $months = [];
        for ($i = 4; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $months[$m] = (int) ($trendRows[$m] ?? 0);
        }

        // Growth rate: current month vs previous month
        $vals = array_values($months);
        $prev = max(1, $vals[count($vals)-2] ?? 1);
        $curr = $vals[count($vals)-1] ?? 0;
        $growthRate = round((($curr - $prev) / $prev) * 100, 1);

        $analytics = [
            'event_success_pct' => $eventSuccess,
            'avg_attendance' => $avgAttendance,
            'growth_rate_pct' => $growthRate,
            'trend' => $months,
        ];

        return view('admin.dashboard', compact('stats', 'roleBreakdown', 'analytics', 'quickStats'));
    }
}