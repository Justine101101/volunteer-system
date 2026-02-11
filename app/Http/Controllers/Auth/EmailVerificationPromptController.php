<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            $redirectRoute = $user->isAdminOrSuperAdmin() ? 'admin.dashboard' : 'dashboard';
            return redirect()->intended(route($redirectRoute, absolute: false));
        }
        
        return view('auth.verify-email');
    }
}
