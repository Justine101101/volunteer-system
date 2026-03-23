<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // OTP email verification (after registration, before login)
    Route::get('verify-otp', [OTPVerificationController::class, 'showVerifyForm'])
        ->name('otp.verify.form');

    Route::post('verify-otp', [OTPVerificationController::class, 'verify'])
        ->name('otp.verify.post');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Google OAuth routes
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
        ->name('google.login');

    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('google.callback');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Prevent "419 Page Expired" if someone visits /logout directly (GET).
    // Logout is intentionally POST-only.
    Route::get('logout', function () {
        return redirect('/');
    });

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// 2FA (email OTP) routes
Route::get('2fa/verify', [TwoFactorController::class, 'showVerifyForm'])
    ->name('two_factor.verify.form');

Route::post('2fa/verify', [TwoFactorController::class, 'verify'])
    ->name('two_factor.verify.post');

Route::middleware('auth')->group(function () {
    Route::post('2fa/setup/start', [TwoFactorController::class, 'startSetup'])
        ->name('two_factor.setup.start');

    Route::post('2fa/disable', [TwoFactorController::class, 'disable'])
        ->name('two_factor.disable');
});
