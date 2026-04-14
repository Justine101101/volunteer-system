<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationDecisionMail;

class EventRegistrationController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
    }

    public function join(string $eventId)
    {
        $user = Auth::user();

        // Guard: do not allow joining past/ended events (server-side)
        $event = $this->queryService->getEventById($eventId);
        if (is_array($event) && !isset($event['error'])) {
            $eventStatus = strtolower((string) ($event['event_status'] ?? ''));
            if ($eventStatus === 'completed') {
                return redirect()->back()->with('error', 'This event has ended. Registration is closed.');
            }

            $eventDateRaw = $event['event_date'] ?? null;
            if (!empty($eventDateRaw)) {
                try {
                    $eventDate = Carbon::parse((string) $eventDateRaw);
                    if ($eventDate->isPast() && !$eventDate->isToday()) {
                        return redirect()->back()->with('error', 'This event has ended. Registration is closed.');
                    }
                } catch (\Throwable $e) {
                    // If parsing fails, don’t block; just proceed.
                }
            }
        }

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

        if (!empty($user->email)) {
            Cache::forget('events:user-reg-map:v1:' . sha1((string) $user->email));
            Cache::forget('dashboard:volunteer:v1:' . sha1((string) $user->email));
            Cache::forget('admin:dashboard:v1');
        }

        // Notify admins/presidents that a volunteer submitted a registration.
        // Keep registration success independent from notification errors.
        $this->notifyAdminsOfNewRegistration(
            volunteerSupabaseId: $supabaseUserId,
            eventId: $eventId,
            volunteerName: (string) ($user->name ?? 'Volunteer'),
            volunteerEmail: (string) ($user->email ?? ''),
            eventTitle: (string) ((is_array($event) && !isset($event['error'])) ? ($event['title'] ?? 'Event') : 'Event')
        );

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

        if (!empty($user->email)) {
            Cache::forget('events:user-reg-map:v1:' . sha1((string) $user->email));
            Cache::forget('dashboard:volunteer:v1:' . sha1((string) $user->email));
            Cache::forget('admin:dashboard:v1');
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

        $this->notifyRegistrationDecision($registrationId, 'approved');

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

        $this->notifyRegistrationDecision($registrationId, 'rejected');

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

        $this->notifyRegistrationDecision($registrationId, 'approved');

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

        $this->notifyRegistrationDecision($registrationId, 'rejected');

        return redirect()->back()->with('success', 'Registration rejected.');
    }

    /**
     * Mark an approved registration as physically present (Supabase UUID).
     */
    public function markPresentSupabase(string $registrationId)
    {
        if (!Auth::user() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // attendance_marked_by references Supabase users(id), not the local Laravel users table.
        // Resolve the current admin's Supabase UUID by email (upsert if missing) so we don't
        // violate the FK constraint when writing attendance.
        $admin = Auth::user();
        $supabaseAdminId = null;
        if (!empty($admin?->email)) {
            $supabaseAdmin = $this->queryService->getUserByEmail((string) $admin->email);
            if (!is_array($supabaseAdmin) || isset($supabaseAdmin['error'])) {
                $upsert = $this->queryService->upsertUser([
                    'name' => $admin->name ?? null,
                    'email' => $admin->email ?? null,
                    'role' => $admin->role ?? 'admin',
                    'email_verified_at' => $admin->email_verified_at ?? null,
                ]);
                if (is_array($upsert) && !isset($upsert['error'])) {
                    $row = (is_array($upsert) && isset($upsert[0]) && is_array($upsert[0])) ? $upsert[0] : $upsert;
                    $supabaseAdminId = is_array($row) ? ($row['id'] ?? null) : null;
                }
            } else {
                $supabaseAdminId = $supabaseAdmin['id'] ?? null;
            }
        }

        $reg = $this->queryService->getEventRegistrationById($registrationId);
        if (!is_array($reg) || isset($reg['error'])) {
            return redirect()->back()->with('error', 'Registration not found.');
        }

        $status = strtolower((string) ($reg['registration_status'] ?? ''));
        if ($status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved volunteers can be marked present.');
        }

        $result = $this->queryService->updateRegistrationAttendance(
            $registrationId,
            Carbon::now()->toIso8601String(),
            is_string($supabaseAdminId) && $supabaseAdminId !== '' ? $supabaseAdminId : null
        );

        if (isset($result['error'])) {
            Log::error('Failed to mark registration present', [
                'registration_id' => $registrationId,
                'error' => $result['error'] ?? null,
            ]);

            return redirect()->back()->with('error', 'Could not save attendance. If this persists, run the Supabase SQL patch `database/supabase/ensure_event_registrations_attendance.sql`.');
        }

        Cache::forget('admin:dashboard:v1');

        return redirect()->back()->with('success', 'Marked present.');
    }

    /**
     * Clear onsite attendance for an approved registration (Supabase UUID).
     */
    public function markAbsentSupabase(string $registrationId)
    {
        if (!Auth::user() || !Auth::user()->isAdminOrSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $reg = $this->queryService->getEventRegistrationById($registrationId);
        if (!is_array($reg) || isset($reg['error'])) {
            return redirect()->back()->with('error', 'Registration not found.');
        }

        $status = strtolower((string) ($reg['registration_status'] ?? ''));
        if ($status !== 'approved') {
            return redirect()->back()->with('error', 'Attendance can only be cleared for approved registrations.');
        }

        $result = $this->queryService->updateRegistrationAttendance($registrationId, null, null);

        if (isset($result['error'])) {
            Log::error('Failed to clear registration attendance', [
                'registration_id' => $registrationId,
                'error' => $result['error'] ?? null,
            ]);

            return redirect()->back()->with('error', 'Could not clear attendance. If this persists, run the Supabase SQL patch `database/supabase/ensure_event_registrations_attendance.sql`.');
        }

        Cache::forget('admin:dashboard:v1');

        return redirect()->back()->with('success', 'Attendance cleared.');
    }

    private function notifyRegistrationDecision(string $registrationId, string $status): void
    {
        try {
            $reg = $this->queryService->getEventRegistrationById($registrationId);
            if (!is_array($reg) || isset($reg['error'])) {
                return;
            }

            $userId = $reg['user_id'] ?? null;
            if (!$userId) {
                return;
            }

            $eventTitle = 'Event';
            $eventDate = '';
            $eventId = (string) ($reg['event_id'] ?? '');
            if ($eventId) {
                $event = $this->queryService->getEventById($eventId);
                if (is_array($event) && !isset($event['error'])) {
                    $eventTitle = (string) ($event['title'] ?? $eventTitle);
                    $eventDate = (string) ($event['event_date'] ?? $eventDate);
                }
            }

            $type = $status === 'approved' ? 'registration.approved' : 'registration.rejected';
            $title = $status === 'approved'
                ? 'Registration approved'
                : 'Registration declined';
            $body = $status === 'approved'
                ? "Your registration was approved for {$eventTitle}."
                : "Your registration was declined for {$eventTitle}.";

            if ($eventDate) {
                $body .= " ({$eventDate})";
            }

            $created = $this->queryService->createNotification([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'metadata' => [
                    'registration_id' => $registrationId,
                    'event_id' => $reg['event_id'] ?? null,
                    'event_title' => $eventTitle,
                    'event_date' => $eventDate ?: null,
                    'status' => $status,
                ],
            ]);

            if (is_array($created) && isset($created['error'])) {
                Log::error('Failed to insert notification to Supabase', [
                    'registration_id' => $registrationId,
                    'user_id' => $userId,
                    'status' => $status,
                    'error' => $created['error'] ?? null,
                    'details' => $created['details'] ?? null,
                ]);
            }

            // Email only if user has enabled it in settings (notification_pref).
            $supabaseUser = $this->queryService->getUserById($userId);
            $notificationPref = true;
            $email = null;
            if (is_array($supabaseUser) && !isset($supabaseUser['error'])) {
                $notificationPref = (bool) ($supabaseUser['notification_pref'] ?? true);
                $email = $supabaseUser['email'] ?? null;
            }

            if ($notificationPref === true && !empty($email)) {
                Mail::to($email)->send(new RegistrationDecisionMail(
                    eventTitle: $eventTitle,
                    status: $status,
                ));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to create registration decision notification', [
                'registration_id' => $registrationId,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create in-app notifications for admin/president users when a volunteer applies to an event.
     */
    private function notifyAdminsOfNewRegistration(
        string $volunteerSupabaseId,
        string $eventId,
        string $volunteerName,
        string $volunteerEmail,
        string $eventTitle
    ): void {
        try {
            $targets = [];
            foreach (['superadmin', 'admin', 'president'] as $role) {
                $rows = $this->queryService->getUsersPrivileged(1, 200, ['role' => $role]);
                if (is_array($rows) && !isset($rows['error'])) {
                    foreach ($rows as $row) {
                        if (is_array($row) && !empty($row['id'])) {
                            $targets[$row['id']] = $row;
                        }
                    }
                }
            }

            foreach ($targets as $adminId => $adminRow) {
                // Do not notify the same user if an admin applies as volunteer.
                if ($adminId === $volunteerSupabaseId) {
                    continue;
                }

                $created = $this->queryService->createNotification([
                    'user_id' => $adminId,
                    'type' => 'registration.pending',
                    'title' => 'New event application',
                    'body' => "{$volunteerName} applied for {$eventTitle}.",
                    'metadata' => [
                        'event_id' => $eventId,
                        'event_title' => $eventTitle,
                        'volunteer_id' => $volunteerSupabaseId,
                        'volunteer_name' => $volunteerName,
                        'volunteer_email' => $volunteerEmail ?: null,
                        'status' => 'pending',
                    ],
                ]);

                if (is_array($created) && isset($created['error'])) {
                    Log::warning('Failed to create admin notification for new registration', [
                        'admin_id' => $adminId,
                        'event_id' => $eventId,
                        'error' => $created['error'] ?? null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to notify admins of new registration', [
                'event_id' => $eventId,
                'volunteer_id' => $volunteerSupabaseId,
                'error' => $e->getMessage(),
            ]);
        }
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
