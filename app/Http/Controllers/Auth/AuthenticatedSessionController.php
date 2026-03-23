<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use App\Services\DatabaseQueryService;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $authUser = Auth::user();
        $twoFactorEnabledLocal = null;
        if ($authUser) {
            $attrs = $authUser->getAttributes();
            if (array_key_exists('two_factor_enabled', $attrs)) {
                $twoFactorEnabledLocal = (bool) $attrs['two_factor_enabled'];
            }
        }

        // If the local column isn't present or not set, fall back to Supabase.
        $twoFactorEnabled = $twoFactorEnabledLocal;
        if ($twoFactorEnabled === null && $authUser?->email) {
            try {
                $supUser = $this->queryService->getUserByEmail($authUser->email);
                $twoFactorEnabled = (bool) ($supUser['two_factor_enabled'] ?? false);
            } catch (\Throwable $e) {
                $twoFactorEnabled = false;
            }
        }

        // If 2FA is enabled, challenge the user before allowing access.
        if ($authUser && (bool) ($twoFactorEnabled ?? false)) {
            $destination = $authUser->isAdminOrSuperAdmin()
                ? route('admin.dashboard', absolute: false)
                : route('dashboard', absolute: false);

            $otpPlain = (string) random_int(100000, 999999);
            $otpHashed = Hash::make($otpPlain);
            $expiresAt = Carbon::now()->addMinutes(5);

            OtpCode::where('user_id', (string) $authUser->id)->delete();

            OtpCode::create([
                'user_id' => (string) $authUser->id,
                'otp_code' => $otpHashed,
                'expires_at' => $expiresAt,
            ]);

            Mail::to($authUser->email)->send(new OtpVerificationMail($otpPlain));

            $request->session()->put([
                'two_factor_mode' => 'challenge',
                'two_factor_user_id' => (string) $authUser->id,
                'two_factor_destination' => $destination,
            ]);

            Auth::logout();
            $request->session()->regenerate();

            return redirect()->route('two_factor.verify.form');
        }

        // Redirect admins/superadmins to admin dashboard
        if ($authUser->isAdminOrSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
