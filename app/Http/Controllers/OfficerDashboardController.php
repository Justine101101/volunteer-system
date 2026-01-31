<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:officer']);
    }

    public function index()
    {
        $upcomingEvents = Event::whereDate('date', '>=', now()->startOfDay())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        $recentRegistrations = EventRegistration::with(['user', 'event'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'total_events' => Event::count(),
            'upcoming_events' => Event::whereDate('date', '>=', now()->startOfDay())->count(),
            'pending_registrations' => EventRegistration::where('status', 'pending')->count(),
            'approved_registrations' => EventRegistration::where('status', 'approved')->count(),
            'total_volunteers' => User::where('role', 'volunteer')->count(),
            'total_registrations' => EventRegistration::count(),
            'total_members' => Member::count(),
        ];

        $quickStats = [
            'registrations_today' => EventRegistration::whereDate('created_at', now()->toDateString())->count(),
            'events_this_month' => Event::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'new_volunteers_30d' => User::where('role', 'volunteer')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
        ];

        $totalEvents = max(1, $stats['total_events']);
        $totalRegs = max(1, $stats['total_registrations']);

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

        $analytics = [
            'trend' => $months,
            'growth_rate_pct' => $this->calculateGrowthRate(array_values($months)),
            'approval_rate_pct' => round(($stats['approved_registrations'] / $totalRegs) * 100, 1),
            'avg_attendance' => round($stats['approved_registrations'] / $totalEvents),
            'event_success_pct' => round(($stats['approved_registrations'] / $totalRegs) * 100, 1),
        ];

        return view('officer.dashboard', compact(
            'stats',
            'quickStats',
            'analytics',
            'upcomingEvents',
            'recentRegistrations'
        ));
    }

    private function calculateGrowthRate(array $values): float
    {
        if (count($values) < 2) {
            return 0.0;
        }

        $prev = max(1, $values[count($values) - 2]);
        $curr = $values[count($values) - 1] ?? 0;

        return round((($curr - $prev) / $prev) * 100, 1);
    }
}

