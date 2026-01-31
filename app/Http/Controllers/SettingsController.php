<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $user->name = $request->name;
        $user->email = $request->email;
        $user->notification_pref = $request->has('notification_pref');
        $user->dark_mode = $request->has('dark_mode');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Reflect profile preferences in Supabase users
        $queryService->upsertUser([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'volunteer',
            'notification_pref' => (bool) $user->notification_pref,
            'dark_mode' => (bool) $user->dark_mode,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
        ]);

        return redirect()->route('settings')->with('success', 'Settings updated successfully!');
    }
}
