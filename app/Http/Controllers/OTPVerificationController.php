<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OTPVerificationController extends Controller
{
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

        $otpRecord = OtpCode::where('user_id', $user->id)->latest()->first();

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

        $otpRecord->delete();

        return redirect()
            ->route('login')
            ->with('status', 'Your email has been verified. You can now log in.');
    }
}

