<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', Rule::in(['volunteer', 'officer'])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Write-through to Supabase (privileged upsert by email)
        $this->queryService->upsertUser([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'volunteer',
            'notification_pref' => $user->notification_pref ?? true,
            'dark_mode' => $user->dark_mode ?? false,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect admins/superadmins to admin dashboard
        $redirectRoute = $user->isSuperAdmin() ? 'admin.dashboard' : 'dashboard';
        return redirect(route($redirectRoute, absolute: false));
    }
}
