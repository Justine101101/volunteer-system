<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use App\Mail\OtpVerificationMail;
use App\Models\OtpCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
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
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Default role for all new registrations
        $defaultRole = 'volunteer';

        // Create local user but leave email as NOT verified yet
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $defaultRole,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        // Mirror user to Supabase (Single Source of Truth for user data)
        $supabaseResult = $this->queryService->upsertUser([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $defaultRole,
            'notification_pref' => true,
            'dark_mode' => false,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        if (isset($supabaseResult['error'])) {
            Log::error('Failed to create user in Supabase (OTP registration): ' . ($supabaseResult['error'] ?? 'Unknown error'));
        }

        // Generate secure 6-digit OTP
        $otpPlain = (string) random_int(100000, 999999);
        $otpHashed = Hash::make($otpPlain);
        $expiresAt = Carbon::now()->addMinutes(5);

        // Invalidate previous OTPs for this user
        OtpCode::where('user_id', $user->id)->delete();

        // Store hashed OTP with expiration
        OtpCode::create([
            'user_id' => $user->id,
            'otp_code' => $otpHashed,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new OtpVerificationMail($otpPlain));

        // Fire Registered event (for any listeners) but DO NOT log in yet
        event(new Registered($user));

        return redirect()
            ->route('otp.verify.form', ['email' => $user->email])
            ->with('status', 'We have emailed you a 6-digit verification code.');
    }
}
