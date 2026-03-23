<?php

namespace App\Http\Controllers;

use App\Mail\OtpVerificationMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function showVerifyForm(): \Illuminate\View\View
    {
        return view('auth.verify-2fa');
    }

    /**
     * Start 2FA setup for the currently authenticated user.
     * Sends an OTP to the user's email, then redirects to /2fa/verify.
     */
    public function startSetup(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Generate secure 6-digit OTP
        $otpPlain = (string) random_int(100000, 999999);
        $otpHashed = Hash::make($otpPlain);
        $expiresAt = Carbon::now()->addMinutes(5);

        // Invalidate previous OTPs for this user
        OtpCode::where('user_id', (string) $user->id)->delete();

        OtpCode::create([
            'user_id' => (string) $user->id,
            'otp_code' => $otpHashed,
            'expires_at' => $expiresAt,
        ]);

        try {
            Mail::to($user->email)->send(new OtpVerificationMail($otpPlain));
        } catch (\Throwable $e) {
            Log::warning('Failed to send 2FA OTP email during setup', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            // Don't allow setup without sending OTP.
            return back()->withErrors([
                'two_factor' => 'Unable to send the verification email. Please check mail configuration.',
            ]);
        }

        $request->session()->put([
            'two_factor_mode' => 'setup',
            'two_factor_user_id' => (string) $user->id,
        ]);

        return redirect()->route('two_factor.verify.form');
    }

    /**
     * Disable 2FA (no OTP required).
     */
    public function disable(Request $request, DatabaseQueryService $queryService): RedirectResponse
    {
        $user = Auth::user();

        $user->two_factor_enabled = false;
        try {
            $user->save();
        } catch (\Throwable $e) {
            Log::warning('Failed to persist 2FA disabled locally', [
                'user_id' => (string) $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Sync change to Supabase (preserve everything else).
        try {
            $queryService->upsertUser([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'volunteer',
                'phone' => $user->phone ?? null,
                'google_id' => $user->google_id ?? null,
                'photo_url' => $user->photo_url ?? null,
                'notification_pref' => $user->notification_pref ?? true,
                'dark_mode' => $user->dark_mode ?? false,
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'two_factor_enabled' => false,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to sync disable 2FA to Supabase', [
                'user_id' => (string) $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('settings')->with('success', 'Two-factor authentication disabled.');
    }

    /**
     * Verify OTP for either 2FA setup or 2FA login challenge.
     */
    public function verify(Request $request, DatabaseQueryService $queryService): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $mode = (string) $request->session()->get('two_factor_mode', '');
        $userId = (string) $request->session()->get('two_factor_user_id', '');

        if ($mode === '' || $userId === '') {
            throw ValidationException::withMessages([
                'otp' => 'Your 2FA session is missing or expired. Please try again.',
            ]);
        }

        /** @var \App\Models\User|null $user */
        $user = User::find($userId);

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => 'User not found.',
            ]);
        }

        $otpRecord = OtpCode::where('user_id', $userId)->latest()->first();

        if (! $otpRecord) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired verification code.',
            ]);
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            throw ValidationException::withMessages([
                'otp' => 'This verification code has expired. Please request a new one.',
            ]);
        }

        if (!Hash::check($request->otp, $otpRecord->otp_code)) {
            throw ValidationException::withMessages([
                'otp' => 'Incorrect verification code.',
            ]);
        }

        // OTP is valid; consume it.
        $otpRecord->delete();
        $request->session()->forget(['two_factor_mode', 'two_factor_user_id']);

        // Mode dispatch
        if ($mode === 'setup') {
            $user->two_factor_enabled = true;
            try {
                $user->save();
            } catch (\Throwable $e) {
                // On Laravel Cloud, migrations may not have added the local column yet.
                // We still continue with Supabase sync so verification doesn't crash.
                Log::warning('Failed to persist 2FA enabled locally', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }

            try {
                $queryService->upsertUser([
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'volunteer',
                    'phone' => $user->phone ?? null,
                    'google_id' => $user->google_id ?? null,
                    'photo_url' => $user->photo_url ?? null,
                    'notification_pref' => $user->notification_pref ?? true,
                    'dark_mode' => $user->dark_mode ?? false,
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                    'two_factor_enabled' => true,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to sync enable 2FA to Supabase', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('settings')->with('success', 'Two-factor authentication enabled.');
        }

        if ($mode === 'challenge') {
            Auth::login($user, true);

            $destination = $request->session()->get('two_factor_destination', route('dashboard'));
            $request->session()->forget(['two_factor_destination']);

            return redirect()->to($destination);
        }

        // Unknown mode: fail closed
        return redirect()->route('login')->withErrors([
            'otp' => 'Unknown 2FA mode. Please try again.',
        ]);
    }
}

