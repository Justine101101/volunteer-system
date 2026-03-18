<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(DatabaseQueryService $queryService)
    {
        // Users are still counted from the local SQLite mirror
        $userCount = User::count();

        // Events + registrations come from Supabase (single source of truth)
        $supabaseEvents = $queryService->getEventsPrivileged(1, 500, ['status' => 'active']);
        $supabaseRegistrations = $queryService->getEventRegistrations(1, 2000, []);

        $eventsArray = (is_array($supabaseEvents) && !isset($supabaseEvents['error'])) ? $supabaseEvents : [];
        $regsArray = (is_array($supabaseRegistrations) && !isset($supabaseRegistrations['error'])) ? $supabaseRegistrations : [];

        // High-level KPIs
        $kpis = [
            'users' => $userCount,
            'events' => count($eventsArray),
            'registrations' => count($regsArray),
            'approved' => collect($regsArray)->where('registration_status', 'approved')->count(),
        ];

        // Build upcoming events list (next 6 by Supabase event_date)
        $events = collect($eventsArray)
            ->map(function (array $event) {
                $date = isset($event['event_date']) ? Carbon::parse($event['event_date']) : null;

                return (object) [
                    'title' => $event['title'] ?? 'Untitled event',
                    'date' => $date,
                ];
            })
            ->filter(function ($event) {
                return $event->date !== null;
            })
            ->sortBy('date')
            ->take(6);

        // Registrations by status
        $byStatus = [
            'pending' => collect($regsArray)->where('registration_status', 'pending')->count(),
            'approved' => collect($regsArray)->where('registration_status', 'approved')->count(),
            'rejected' => collect($regsArray)->where('registration_status', 'rejected')->count(),
        ];

        return view('admin.reports', compact('kpis', 'events', 'byStatus'));
    }
}


