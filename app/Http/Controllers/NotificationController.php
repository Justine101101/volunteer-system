<?php

namespace App\Http\Controllers;

use App\Services\DatabaseQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private const NOTIFICATIONS_CACHE_TTL_SECONDS = 30;

    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $supabaseUser = $user && $user->email ? $this->queryService->getUserByEmail($user->email) : null;
        $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;

        // If the user isn't mirrored yet, create them in Supabase so notifications can resolve correctly.
        if (!$supabaseUserId && $user && $user->email) {
            $upsert = $this->queryService->upsertUser([
                'name' => $user->name ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? 'volunteer',
                'email_verified_at' => $user->email_verified_at?->toISOString(),
            ]);
            if (is_array($upsert) && !isset($upsert['error'])) {
                $row = isset($upsert[0]) && is_array($upsert[0]) ? $upsert[0] : $upsert;
                $supabaseUserId = is_array($row) ? ($row['id'] ?? null) : null;
            }
        }

        $notifications = [];
        if ($supabaseUserId) {
            $cacheKey = 'notifications:user:v1:' . $supabaseUserId;
            $notifications = Cache::remember($cacheKey, self::NOTIFICATIONS_CACHE_TTL_SECONDS, function () use ($supabaseUserId) {
                $result = $this->queryService->getNotificationsForUser($supabaseUserId, 50);
                return (is_array($result) && !isset($result['error'])) ? $result : [];
            });
        }

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        $this->queryService->markNotificationRead($notificationId);
        $user = Auth::user();
        if ($user && $user->email) {
            $supabaseUser = $this->queryService->getUserByEmail($user->email);
            $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;
            if ($supabaseUserId) {
                Cache::forget('notifications:user:v1:' . $supabaseUserId);
            }
        }
        return redirect()->back();
    }
}

