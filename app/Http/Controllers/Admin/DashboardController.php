<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Contact;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_volunteers' => User::where('role', 'volunteer')->count(),
            'total_events' => Event::count(),
            'total_registrations' => EventRegistration::count(),
            'total_contacts' => Contact::count(),
            'total_members' => Member::count(),
            'pending_registrations' => EventRegistration::where('status', 'pending')->count(),
            'approved_registrations' => EventRegistration::where('status', 'approved')->count(),
            'recent_events' => Event::with('creator')->latest()->take(5)->get(),
            'recent_registrations' => EventRegistration::with(['user', 'event'])->latest()->take(10)->get(),
            'recent_contacts' => Contact::latest()->take(5)->get(),
        ];

        $quickStats = [
            'new_volunteers_30d' => User::where('role', 'volunteer')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'upcoming_events' => Event::whereDate('date', '>=', now()->startOfDay())->count(),
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