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
            'role' => ['required', 'string', Rule::in(['volunteer'])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Single Source of Truth: Write to Supabase first
        // Note: We still need to create a local user for Laravel authentication
        // This is a hybrid approach - auth in MySQL, data in Supabase
        $supabaseResult = $this->queryService->upsertUser([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? 'volunteer',
            'notification_pref' => true,
            'dark_mode' => false,
            'password' => Hash::make($request->password), // Store hashed password in Supabase
        ]);

        if (isset($supabaseResult['error'])) {
            Log::error('Failed to create user in Supabase: ' . ($supabaseResult['error'] ?? 'Unknown error'));
            // Continue with local user creation for auth, but log the error
        }

        // Still create local user for Laravel authentication
        // TODO: Consider migrating to Supabase Auth in the future
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect admins/superadmins to admin dashboard
        $redirectRoute = $user->isAdminOrSuperAdmin() ? 'admin.dashboard' : 'dashboard';
        return redirect(route($redirectRoute, absolute: false));
    }
}
