<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VolunteerDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DatabaseQueryService;
use App\Services\SupabaseService;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Default: no participation stats
        $participationStats = null;

        // For volunteers, reuse the volunteer dashboard analytics
        if (method_exists($user, 'isVolunteer') && $user->isVolunteer()) {
            /** @var \App\Http\Controllers\VolunteerDashboardController $volController */
            $volController = app(VolunteerDashboardController::class);
            $participationStats = $volController->buildStatsForUser($user);
        }

        return view('settings', [
            'user' => $user,
            'participationStats' => $participationStats,
        ]);
    }

    public function update(Request $request, DatabaseQueryService $queryService, SupabaseService $supabaseService)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'current_password' => 'nullable|current_password',
            'password' => 'nullable|min:8|confirmed',
            'notification_pref' => 'boolean',
            'dark_mode' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload (Only for Volunteers)
        if ($request->hasFile('photo') && $user->isVolunteer()) {
            $photo = $request->file('photo');
            $filename = Str::slug($user->name) . '-' . time() . '.' . $photo->getClientOriginalExtension();

            // Upload directly to Supabase Storage so Cloud doesn't depend on local `storage:link`.
            $bucket = (string) config('supabase.bucket_name', 'volunteer-portal');
            $supabasePath = 'profiles/' . $filename;

            try {
                $supabaseService->uploadFile($bucket, $supabasePath, $photo->getContent());
                $user->photo_url = $supabaseService->getFileUrl($bucket, $supabasePath);
            } catch (\Throwable $e) {
                // Fallback: store locally (useful for local dev).
                Log::warning('Profile photo upload to Supabase failed; falling back to local storage', [
                    'message' => $e->getMessage(),
                ]);

                $localPath = $photo->storeAs('profiles', $filename, 'public');
                $user->photo_url = Storage::url($localPath);
            }
        }
        
        // Always remove photo_url for admins (force default avatar)
        if ($user->isAdmin()) {
            if ($user->photo_url) {
                $oldPath = str_replace('/storage/', '', $user->photo_url);
                Storage::disk('public')->delete($oldPath);
            }
            $user->photo_url = null;
        }

        // Update user fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone ?? null;
        $user->notification_pref = $request->has('notification_pref');
        $user->dark_mode = $request->has('dark_mode');

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Save to Laravel DB first
        $user->save();

        // Single Source of Truth: Update Supabase
        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'google_id' => $user->google_id ?? null,
            'role' => $user->role ?? 'volunteer',
            'notification_pref' => $user->notification_pref ?? true,
            'dark_mode' => $user->dark_mode ?? false,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'photo_url' => $user->isAdmin() ? null : ($user->photo_url ?? null),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $result = $queryService->upsertUser($updateData);

        if (isset($result['error'])) {
            Log::warning('Failed to sync settings to Supabase: ' . ($result['error'] ?? 'Unknown error'));
            // Don't fail the request, just log the warning
        }

        return redirect()->route('settings')->with('success', 'Settings updated successfully!');
    }
}
