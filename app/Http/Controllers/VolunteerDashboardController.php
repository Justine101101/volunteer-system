<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VolunteerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Get user's event registrations
        $userRegistrations = EventRegistration::where('user_id', $user->id)
            ->with('event')
            ->latest()
            ->get();

        // Get registered event IDs
        $registeredEventIds = $userRegistrations->pluck('event_id')->toArray();

        // Get upcoming events the volunteer is registered for (approved)
        $upcomingRegisteredEvents = Event::whereIn('id', $userRegistrations->where('status', 'approved')->pluck('event_id'))
            ->whereDate('date', '>=', now()->startOfDay())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        // Get available events (not yet registered)
        $availableEvents = Event::whereDate('date', '>=', now()->startOfDay())
            ->whereNotIn('id', $registeredEventIds)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        // Count registrations by status
        $stats = [
            'total_registrations' => $userRegistrations->count(),
            'approved_registrations' => $userRegistrations->where('status', 'approved')->count(),
            'pending_registrations' => $userRegistrations->where('status', 'pending')->count(),
            'rejected_registrations' => $userRegistrations->where('status', 'rejected')->count(),
            'upcoming_registered_events' => $upcomingRegisteredEvents->count(),
            'available_events' => $availableEvents->count(),
            'recent_registrations' => EventRegistration::where('user_id', $user->id)
                ->with('event')
                ->latest()
                ->take(10)
                ->get(),
        ];

        $quickStats = [
            'upcoming_events' => Event::whereDate('date', '>=', now()->startOfDay())->count(),
            'registrations_today' => EventRegistration::where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
            'events_this_month' => Event::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'my_registrations_this_month' => $userRegistrations
                ->filter(function ($reg) {
                    return $reg->created_at->month === now()->month 
                        && $reg->created_at->year === now()->year;
                })
                ->count(),
        ];

        // Calculate participation rate
        $totalEvents = Event::count();
        $participationRate = $totalEvents > 0 
            ? round(($stats['approved_registrations'] / $totalEvents) * 100, 1) 
            : 0;

        // Simple analytics for header cards
        $approved = (int) $stats['approved_registrations'];
        $totalRegs = max(1, (int) $stats['total_registrations']);

        $eventSuccess = round(($approved / $totalRegs) * 100); // % of approved vs all
        $avgAttendance = $approved > 0 ? round($approved / max(1, $totalEvents)) : 0; // approved per event

        // Monthly trend (last 5 months) of user's approved registrations
        $trendRows = EventRegistration::select(
                DB::raw("strftime('%Y-%m', created_at) as ym"),
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $user->id)
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
            'participation_rate' => $participationRate,
        ];

        return view('volunteer.dashboard', compact(
            'stats', 
            'analytics', 
            'quickStats',
            'upcomingRegisteredEvents',
            'availableEvents'
        ));
    }
}

