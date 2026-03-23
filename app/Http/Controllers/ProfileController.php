<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\DatabaseQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\SupabaseService;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Default: no participation stats
        $participationStats = null;

        // For volunteers, reuse the volunteer dashboard analytics
        if (method_exists($user, 'isVolunteer') && $user->isVolunteer()) {
            /** @var \App\Http\Controllers\VolunteerDashboardController $volController */
            $volController = app(VolunteerDashboardController::class);
            $participationStats = $volController->buildStatsForUser($user);
        }

        return view('profile.edit', [
            'user' => $user,
            'participationStats' => $participationStats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, DatabaseQueryService $queryService, SupabaseService $supabaseService): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Upload new photo
            $photo = $request->file('photo');
            $filename = Str::slug($user->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();

            // Upload directly to Supabase Storage so Cloud doesn't depend on local `storage:link`.
            $bucket = (string) config('supabase.bucket_name', 'volunteer-portal');
            $supabasePath = 'profiles/' . $filename;

            try {
                $supabaseService->uploadFile($bucket, $supabasePath, $photo->getContent());
                $user->photo_url = $supabaseService->getFileUrl($bucket, $supabasePath);
            } catch (\Throwable $e) {
                // Fallback for local dev.
                Log::warning('Profile photo upload to Supabase failed; falling back to local storage', [
                    'message' => $e->getMessage(),
                ]);

                $path = $photo->storeAs('profiles', $filename, 'public');
                $user->photo_url = Storage::url($path);
            }
        }

        // Save to Laravel DB first
        $user->save();

        // Sync to Supabase (Single Source of Truth)
        try {
            $updateData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'google_id' => $user->google_id ?? null,
                'role' => $user->role ?? 'volunteer',
                'notification_pref' => $user->notification_pref ?? true,
                'dark_mode' => $user->dark_mode ?? false,
                'email_verified_at' => $user->email_verified_at?->toISOString(),
            ];

            $result = $queryService->upsertUser($updateData);

            if (isset($result['error'])) {
                Log::warning('Failed to sync profile to Supabase: ' . ($result['error'] ?? 'Unknown error'));
                // Don't fail the request, just log the warning
            }
        } catch (\Exception $e) {
            Log::warning('Error syncing profile to Supabase: ' . $e->getMessage());
            // Don't fail the request, just log the warning
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
