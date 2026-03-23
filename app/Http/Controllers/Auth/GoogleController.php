<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\OtpCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerificationMail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    private function googleRedirectUrl(): string
    {
        $configured = (string) config('services.google.redirect', '');
        $fallback = url('/auth/google/callback');
        $url = $configured !== '' ? $configured : $fallback;

        // Cloud OAuth callback should be HTTPS to match Google Console settings.
        if (str_starts_with($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    /**
     * Redirect the user to Google OAuth provider.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirectUrl($this->googleRedirectUrl())
            ->stateless()
            ->redirect();
    }

    /**
     * Handle the callback from Google OAuth provider.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->googleRedirectUrl())
                ->stateless()
                ->user();

            // Check if user exists by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update google_id if not set
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                    
                    // Sync updated google_id to Supabase
                    try {
                        $updateData = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role ?? 'volunteer',
                            'google_id' => $user->google_id,
                            'phone' => $user->phone ?? null,
                            'notification_pref' => $user->notification_pref ?? true,
                            'dark_mode' => $user->dark_mode ?? false,
                            'email_verified_at' => $user->email_verified_at?->toISOString(),
                        ];
                        $this->queryService->upsertUser($updateData);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to sync google_id to Supabase: ' . $e->getMessage());
                    }
                }
                
                // User exists, log them in
                Auth::login($user, true);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(32)), // Random password since OAuth
                    'email_verified_at' => now(), // Google emails are verified
                    'role' => 'volunteer', // Default role
                    'google_id' => $googleUser->getId(),
                ]);

                // Sync to Supabase
                try {
                    $updateData = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role ?? 'volunteer',
                        'google_id' => $user->google_id,
                        'notification_pref' => true,
                        'dark_mode' => false,
                        'email_verified_at' => $user->email_verified_at?->toISOString(),
                    ];

                    $this->queryService->upsertUser($updateData);
                } catch (\Exception $e) {
                    // Log error but don't fail authentication
                    \Log::warning('Failed to sync Google user to Supabase: ' . $e->getMessage());
                }

                Auth::login($user, true);
            }

            // If 2FA is enabled, challenge before redirecting.
            $twoFactorEnabledLocal = null;
            $attrs = $user->getAttributes();
            if (array_key_exists('two_factor_enabled', $attrs)) {
                $twoFactorEnabledLocal = (bool) $attrs['two_factor_enabled'];
            }

            $twoFactorEnabled = $twoFactorEnabledLocal;
            if ($twoFactorEnabled === null && $user->email) {
                try {
                    $supUser = $this->queryService->getUserByEmail($user->email);
                    $twoFactorEnabled = (bool) ($supUser['two_factor_enabled'] ?? false);
                } catch (\Throwable $e) {
                    $twoFactorEnabled = false;
                }
            }

            if ((bool) ($twoFactorEnabled ?? false)) {
                $destination = $user->isAdminOrSuperAdmin()
                    ? route('admin.dashboard', absolute: false)
                    : route('dashboard', absolute: false);

                $otpPlain = (string) random_int(100000, 999999);
                $otpHashed = Hash::make($otpPlain);
                $expiresAt = Carbon::now()->addMinutes(5);

                OtpCode::where('user_id', (string) $user->id)->delete();

                OtpCode::create([
                    'user_id' => (string) $user->id,
                    'otp_code' => $otpHashed,
                    'expires_at' => $expiresAt,
                ]);

                Mail::to($user->email)->send(new OtpVerificationMail($otpPlain));

                request()->session()->put([
                    'two_factor_mode' => 'challenge',
                    'two_factor_user_id' => (string) $user->id,
                    'two_factor_destination' => $destination,
                ]);

                Auth::logout();
                request()->session()->regenerate();

                return redirect()->route('two_factor.verify.form');
            }

            // Redirect based on user role
            if ($user->isAdminOrSuperAdmin()) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
