<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function join(Event $event)
    {
        $existingRegistration = EventRegistration::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->first();

        if ($existingRegistration) {
            return redirect()->back()->with('error', 'You have already registered for this event.');
        }

        EventRegistration::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Successfully registered for the event!');
    }

    public function leave(Event $event)
    {
        $registration = EventRegistration::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->first();

        if ($registration) {
            $registration->delete();
            return redirect()->back()->with('success', 'Successfully unregistered from the event.');
        }

        return redirect()->back()->with('error', 'Registration not found.');
    }

    public function approve(EventRegistration $registration)
    {
        $this->middleware('role:superadmin');
        
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $registration->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Registration approved successfully!');
    }

    public function reject(EventRegistration $registration)
    {
        $this->middleware('role:superadmin');
        
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $registration->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Registration rejected.');
    }

    public function bulkApprove(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $ids = (array) $request->input('ids', []);
        if (!empty($ids)) {
            EventRegistration::whereIn('id', $ids)->update(['status' => 'approved']);
        }
        return redirect()->back()->with('success', 'Selected registrations approved.');
    }

    public function bulkReject(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $ids = (array) $request->input('ids', []);
        if (!empty($ids)) {
            EventRegistration::whereIn('id', $ids)->update(['status' => 'rejected']);
        }
        return redirect()->back()->with('success', 'Selected registrations declined.');
    }
}
