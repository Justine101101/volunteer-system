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

class AuthenticatedSessionController extends Controller
{
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

        // If 2FA is enabled, challenge the user before allowing access.
        $authUser = Auth::user();
        if ($authUser && (bool) ($authUser->two_factor_enabled ?? false)) {
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
