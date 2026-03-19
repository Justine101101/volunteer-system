<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DatabaseQueryService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $redirectRoute = $user->isAdminOrSuperAdmin() ? 'admin.dashboard' : 'dashboard';
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route($redirectRoute, absolute: false).'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Mirror verification timestamp to Supabase users table (best-effort)
            try {
                $existing = $this->queryService->getUserByEmail($user->email);
                if (is_array($existing) && !isset($existing['error'])) {
                    $this->queryService->upsertUser([
                        'name' => $existing['name'] ?? $user->name,
                        'email' => $existing['email'] ?? $user->email,
                        'password' => $existing['password'] ?? '',
                        'role' => $existing['role'] ?? $user->role,
                        'phone' => $existing['phone'] ?? $user->phone,
                        'google_id' => $existing['google_id'] ?? $user->google_id,
                        'photo_url' => $existing['photo_url'] ?? $user->photo_url,
                        'notification_pref' => $existing['notification_pref'] ?? true,
                        'dark_mode' => $existing['dark_mode'] ?? false,
                        'email_verified_at' => $user->email_verified_at?->toISOString(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed syncing email_verified_at to Supabase after email verification', [
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->intended(route($redirectRoute, absolute: false).'?verified=1');
    }
}
