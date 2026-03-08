<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function join(string $eventId)
    {
        $user = Auth::user();

        // Resolve Supabase user ID from local user (by email), creating if needed
        $supabaseUser = $user && $user->email
            ? $this->queryService->getUserByEmail($user->email)
            : null;

        if (!$supabaseUser || isset($supabaseUser['error'])) {
            // Try to upsert the user into Supabase
            $upsertResult = $this->queryService->upsertUser([
                'name' => $user->name ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? 'volunteer',
                'email_verified_at' => $user->email_verified_at ?? null,
            ]);

            if (isset($upsertResult['error'])) {
                Log::error('Failed to sync user to Supabase for event registration', [
                    'error' => $upsertResult['error'] ?? null,
                    'user_id' => $user->id ?? null,
                ]);
                return redirect()->back()->with('error', 'Could not sync your account for event registration. Please try again later.');
            }

            $supabaseUser = is_array($upsertResult) && isset($upsertResult[0]) ? $upsertResult[0] : $upsertResult;
        }

        $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;

        if (!$supabaseUserId) {
            Log::error('Supabase user ID missing when trying to register for event', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
            ]);
            return redirect()->back()->with('error', 'Unable to identify your account in the event database.');
        }

        // Check if already registered in Supabase for this user + event
        $existingRegistration = $this->queryService->getEventRegistration($supabaseUserId, $eventId);

        if ($existingRegistration && !isset($existingRegistration['error'])) {
            return redirect()->back()->with('error', 'You have already registered for this event.');
        }

        // Single Source of Truth: Write only to Supabase (UUIDs)
        $result = $this->queryService->registerForEvent($supabaseUserId, $eventId, [
            'registration_status' => 'pending',
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to register for event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to register for event. Please try again.');
        }

        return redirect()->back()->with('success', 'Successfully registered for the event!');
    }

    public function leave(string $eventId)
    {
        $user = Auth::user();

        // Resolve Supabase user ID from local user (by email)
        $supabaseUser = $user && $user->email
            ? $this->queryService->getUserByEmail($user->email)
            : null;

        $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;

        if (!$supabaseUserId) {
            return redirect()->back()->with('error', 'Unable to identify your registration.');
        }

        // Get registration from Supabase for this user + event
        $registration = $this->queryService->getEventRegistration($supabaseUserId, $eventId);

        if (!$registration || isset($registration['error'])) {
            // If there's nothing to cancel, treat it as already left to avoid scaring the user
            return redirect()->back()->with('success', 'You are not registered for this event.');
        }

        $registrationId = is_array($registration) ? ($registration['id'] ?? null) : null;
        if (!$registrationId) {
            return redirect()->back()->with('success', 'You are not registered for this event.');
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

    /**
     * Approve a Supabase-backed registration by its UUID.
     */
    public function approveSupabase(string $registrationId)
    {
        if (!Auth::user() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->queryService->updateRegistrationStatus($registrationId, 'approved');

        if (isset($result['error'])) {
            Log::error('Failed to approve Supabase registration', [
                'registration_id' => $registrationId,
                'error' => $result['error'] ?? null,
            ]);
            return redirect()->back()->with('error', 'Failed to approve registration. Please try again.');
        }

        return redirect()->back()->with('success', 'Registration approved successfully!');
    }

    /**
     * Reject a Supabase-backed registration by its UUID.
     */
    public function rejectSupabase(string $registrationId)
    {
        if (!Auth::user() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->queryService->updateRegistrationStatus($registrationId, 'rejected');

        if (isset($result['error'])) {
            Log::error('Failed to reject Supabase registration', [
                'registration_id' => $registrationId,
                'error' => $result['error'] ?? null,
            ]);
            return redirect()->back()->with('error', 'Failed to reject registration. Please try again.');
        }

        return redirect()->back()->with('success', 'Registration rejected.');
    }

    /**
     * Bulk approve Supabase-backed registrations (UUIDs).
     */
    public function bulkApproveSupabase(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No registrations selected.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            $result = $this->queryService->updateRegistrationStatus((string) $id, 'approved');
            if (!isset($result['error'])) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $message = "{$successCount} registration(s) approved.";
        if ($failCount > 0) {
            $message .= " {$failCount} failed.";
        }

        return redirect()->back()->with($successCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk reject Supabase-backed registrations (UUIDs).
     */
    public function bulkRejectSupabase(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $ids = (array) $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No registrations selected.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($ids as $id) {
            $result = $this->queryService->updateRegistrationStatus((string) $id, 'rejected');
            if (!isset($result['error'])) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $message = "{$successCount} registration(s) rejected.";
        if ($failCount > 0) {
            $message .= " {$failCount} failed.";
        }

        return redirect()->back()->with($successCount > 0 ? 'success' : 'error', $message);
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
