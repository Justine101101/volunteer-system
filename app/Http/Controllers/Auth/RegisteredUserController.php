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
            'phone' => ['required', 'string', 'max:30'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $skipSupabase = app()->runningUnitTests();

        // Ensure Supabase is configured; registration is expected to be mirrored to Supabase.
        if (!$skipSupabase && (!config('supabase.url') || !config('supabase.service_role_key'))) {
            return back()->withInput()->with('error', 'Supabase is not configured. Please set SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY in your .env.');
        }

        // Default role for all new registrations
        $defaultRole = 'volunteer';

        // Create local user but leave email as NOT verified yet
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => (int) $request->age,
            'role' => $defaultRole,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        // Some DB setups (UUID defaults, differing PK types) may not populate `$user->id`
        // immediately after create(), so re-fetch by unique email.
        $user = User::where('email', $request->email)->firstOrFail();

        // Mirror user to Supabase (Single Source of Truth for user data)
        $supabaseResult = [];
        if (!$skipSupabase) {
            $supabaseResult = $this->queryService->upsertUser([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $defaultRole,
                'phone' => $user->phone ?? null,
                'age' => $user->age ?? null,
                'notification_pref' => true,
                'dark_mode' => false,
                'password' => Hash::make($request->password),
                'email_verified_at' => null,
            ]);
        }

        if (!$skipSupabase && isset($supabaseResult['error'])) {
            Log::error('Failed to create user in Supabase (OTP registration)', [
                'error' => $supabaseResult['error'] ?? 'Unknown error',
                'status' => $supabaseResult['status'] ?? null,
                'details' => $supabaseResult['details'] ?? null,
                'email' => $user->email,
            ]);

            // Keep databases consistent: if Supabase write fails, rollback local user creation.
            $user->delete();

            return back()
                ->withInput()
                ->with('error', 'Registration failed because the Supabase users table could not be updated. Please contact the admin to fix Supabase schema/RLS.');
        }

        // Store Supabase UUID on local mirror for easier mapping (if available)
        if (!$skipSupabase) {
            $supabaseRow = is_array($supabaseResult) && isset($supabaseResult[0]) ? $supabaseResult[0] : $supabaseResult;
            $supabaseId = is_array($supabaseRow) ? ($supabaseRow['id'] ?? null) : null;
            if ($supabaseId && $user->fillable && in_array('supabase_user_id', $user->getFillable(), true)) {
                $user->supabase_user_id = $supabaseId;
                $user->save();
            }
        }

        // Generate secure 6-digit OTP
        $otpPlain = (string) random_int(100000, 999999);
        $otpHashed = Hash::make($otpPlain);
        $expiresAt = Carbon::now()->addMinutes(5);

        // Invalidate previous OTPs for this user
        $userId = (string) $user->id;
        OtpCode::where('user_id', $userId)->delete();

        // Store hashed OTP with expiration
        OtpCode::create([
            'user_id' => $userId,
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
