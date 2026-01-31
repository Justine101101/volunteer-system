<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
    }

    public function index()
    {
        // High-level KPIs
        $kpis = [
            'users' => User::count(),
            'events' => Event::count(),
            'registrations' => EventRegistration::count(),
            'approved' => EventRegistration::where('status', 'approved')->count(),
        ];

        // Simple time-series data (last 6 events by date)
        $events = Event::orderBy('date', 'asc')->take(6)->get(['title', 'date']);
        $byStatus = [
            'pending' => EventRegistration::where('status', 'pending')->count(),
            'approved' => EventRegistration::where('status', 'approved')->count(),
            'rejected' => EventRegistration::where('status', 'rejected')->count(),
        ];

        return view('admin.reports', compact('kpis', 'events', 'byStatus'));
    }
}


