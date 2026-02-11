<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Log;

class EventRegistrationController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
    }

    public function join(Event $event)
    {
        // Check if already registered in Supabase
        $existingRegistration = $this->queryService->getEventRegistration(Auth::id(), $event->id);

        if ($existingRegistration && !isset($existingRegistration['error'])) {
            return redirect()->back()->with('error', 'You have already registered for this event.');
        }

        // Single Source of Truth: Write only to Supabase
        $result = $this->queryService->registerForEvent(Auth::id(), $event->id, [
            'registration_status' => 'pending',
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to register for event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to register for event. Please try again.');
        }

        return redirect()->back()->with('success', 'Successfully registered for the event!');
    }

    public function leave(Event $event)
    {
        // Get registration from Supabase
        $registration = $this->queryService->getEventRegistration(Auth::id(), $event->id);

        if (!$registration || isset($registration['error'])) {
            return redirect()->back()->with('error', 'Registration not found.');
        }

        $registrationId = is_array($registration) ? ($registration['id'] ?? null) : null;
        if (!$registrationId) {
            return redirect()->back()->with('error', 'Could not identify registration.');
        }

        // Single Source of Truth: Delete only from Supabase
        $result = $this->queryService->deleteEventRegistration($registrationId);

        if (isset($result['error'])) {
            Log::error('Failed to delete registration from Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to unregister. Please try again.');
        }

        return redirect()->back()->with('success', 'Successfully unregistered from the event.');
    }

    public function approve(EventRegistration $registration)
    {
        if (!Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get Supabase registration ID
        // Since IDs don't match, find by user_id and event_id
        $supabaseRegistration = $this->queryService->getEventRegistration(
            $registration->user_id,
            $registration->event_id
        );

        if (!$supabaseRegistration || isset($supabaseRegistration['error'])) {
            Log::warning('Could not find Supabase registration to approve for local ID: ' . $registration->id);
            return redirect()->back()->with('error', 'Registration not found in database.');
        }

        $registrationId = is_array($supabaseRegistration) ? ($supabaseRegistration['id'] ?? null) : null;
        if (!$registrationId) {
            return redirect()->back()->with('error', 'Could not identify registration.');
        }

        // Single Source of Truth: Update only in Supabase
        $result = $this->queryService->updateRegistrationStatus($registrationId, 'approved');

        if (isset($result['error'])) {
            Log::error('Failed to approve registration in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to approve registration. Please try again.');
        }

        return redirect()->back()->with('success', 'Registration approved successfully!');
    }

    public function reject(EventRegistration $registration)
    {
        if (!Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get Supabase registration ID
        $supabaseRegistration = $this->queryService->getEventRegistration(
            $registration->user_id,
            $registration->event_id
        );

        if (!$supabaseRegistration || isset($supabaseRegistration['error'])) {
            Log::warning('Could not find Supabase registration to reject for local ID: ' . $registration->id);
            return redirect()->back()->with('error', 'Registration not found in database.');
        }

        $registrationId = is_array($supabaseRegistration) ? ($supabaseRegistration['id'] ?? null) : null;
        if (!$registrationId) {
            return redirect()->back()->with('error', 'Could not identify registration.');
        }

        // Single Source of Truth: Update only in Supabase
        $result = $this->queryService->updateRegistrationStatus($registrationId, 'rejected');

        if (isset($result['error'])) {
            Log::error('Failed to reject registration in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to reject registration. Please try again.');
        }

        return redirect()->back()->with('success', 'Registration rejected.');
    }

    public function bulkApprove(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No registrations selected.');
        }

        // Get local registrations to find their Supabase equivalents
        $localRegistrations = EventRegistration::whereIn('id', $ids)->get();
        $successCount = 0;
        $failCount = 0;

        foreach ($localRegistrations as $registration) {
            $supabaseRegistration = $this->queryService->getEventRegistration(
                $registration->user_id,
                $registration->event_id
            );

            if ($supabaseRegistration && !isset($supabaseRegistration['error'])) {
                $registrationId = is_array($supabaseRegistration) ? ($supabaseRegistration['id'] ?? null) : null;
                if ($registrationId) {
                    $result = $this->queryService->updateRegistrationStatus($registrationId, 'approved');
                    if (!isset($result['error'])) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } else {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }

        if ($successCount > 0) {
            $message = "{$successCount} registration(s) approved.";
            if ($failCount > 0) {
                $message .= " {$failCount} failed.";
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to approve registrations.');
    }

    public function bulkReject(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No registrations selected.');
        }

        // Get local registrations to find their Supabase equivalents
        $localRegistrations = EventRegistration::whereIn('id', $ids)->get();
        $successCount = 0;
        $failCount = 0;

        foreach ($localRegistrations as $registration) {
            $supabaseRegistration = $this->queryService->getEventRegistration(
                $registration->user_id,
                $registration->event_id
            );

            if ($supabaseRegistration && !isset($supabaseRegistration['error'])) {
                $registrationId = is_array($supabaseRegistration) ? ($supabaseRegistration['id'] ?? null) : null;
                if ($registrationId) {
                    $result = $this->queryService->updateRegistrationStatus($registrationId, 'rejected');
                    if (!isset($result['error'])) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } else {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }

        if ($successCount > 0) {
            $message = "{$successCount} registration(s) rejected.";
            if ($failCount > 0) {
                $message .= " {$failCount} failed.";
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to reject registrations.');
    }
}
