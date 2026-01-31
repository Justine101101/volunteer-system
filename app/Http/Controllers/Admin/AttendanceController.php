<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
    }

    public function index()
    {
        $registrations = EventRegistration::with(['user', 'event'])
            ->latest()
            ->paginate(15);

        $summary = [
            'total' => EventRegistration::count(),
            'pending' => EventRegistration::where('status', 'pending')->count(),
            'approved' => EventRegistration::where('status', 'approved')->count(),
            'rejected' => EventRegistration::where('status', 'rejected')->count(),
        ];

        return view('admin.attendance', compact('registrations', 'summary'));
    }
}


