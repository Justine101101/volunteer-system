<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OTPVerificationController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', [
            'email' => $request->query('email'),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $otpRecord = OtpCode::where('user_id', (string) $user->id)->latest()->first();

        if (! $otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.'])->withInput();
        }

        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            return back()->withErrors(['otp' => 'This verification code has expired.'])->withInput();
        }

        if (! Hash::check($request->otp, $otpRecord->otp_code)) {
            return back()->withErrors(['otp' => 'Incorrect verification code.'])->withInput();
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        // Keep Supabase mirror in sync: once verified locally, also mark verified in Supabase
        try {
            $existing = $this->queryService->getUserByEmail($user->email);
            if (is_array($existing) && !isset($existing['error'])) {
                $this->queryService->upsertUser([
                    // include required fields to avoid accidentally overwriting with nulls
                    'name' => $existing['name'] ?? $user->name,
                    'email' => $existing['email'] ?? $user->email,
                    'password' => $existing['password'] ?? '',
                    'role' => $existing['role'] ?? $user->role,
                    'phone' => $existing['phone'] ?? null,
                    'google_id' => $existing['google_id'] ?? null,
                    'photo_url' => $existing['photo_url'] ?? null,
                    'notification_pref' => $existing['notification_pref'] ?? true,
                    'dark_mode' => $existing['dark_mode'] ?? false,
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed syncing email_verified_at to Supabase after OTP verification', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $otpRecord->delete();

        return redirect()
            ->route('login')
            ->with('status', 'Your email has been verified. You can now log in.');
    }
}

