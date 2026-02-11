<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;
        $requiredRoles = explode('|', $role);

        $allow = false;

        foreach ($requiredRoles as $required) {
            $required = trim($required);
            if ($required === 'superadmin') {
                if ($userRole === 'superadmin') {
                    $allow = true;
                    break;
                }
            } elseif ($required === 'admin') {
                if (in_array($userRole, ['admin', 'superadmin'], true)) {
                    $allow = true;
                    break;
                }
            } elseif ($userRole === $required) {
                $allow = true;
                break;
            }
        }

        if (!$allow) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
