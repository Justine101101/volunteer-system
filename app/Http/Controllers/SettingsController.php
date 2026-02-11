<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\DatabaseQueryService;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('settings');
    }

    public function update(Request $request, DatabaseQueryService $queryService)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|current_password',
            'password' => 'nullable|min:8|confirmed',
            'notification_pref' => 'boolean',
            'dark_mode' => 'boolean',
        ]);

        // Single Source of Truth: Update Supabase first
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $user->role ?? 'volunteer',
            'notification_pref' => $request->has('notification_pref'),
            'dark_mode' => $request->has('dark_mode'),
            'email_verified_at' => $user->email_verified_at?->toISOString(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $result = $queryService->upsertUser($updateData);

        if (isset($result['error'])) {
            Log::error('Failed to update user in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update settings. Please try again.');
        }

        // Sync to local user for Laravel authentication
        // TODO: Consider migrating to Supabase Auth in the future
        $user->name = $request->name;
        $user->email = $request->email;
        $user->notification_pref = $request->has('notification_pref');
        $user->dark_mode = $request->has('dark_mode');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('settings')->with('success', 'Settings updated successfully!');
    }
}
