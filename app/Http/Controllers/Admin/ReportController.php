<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DatabaseQueryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(DatabaseQueryService $queryService)
    {
        // Users are still counted from the local SQLite mirror
        $userCount = User::count();
        $volunteerCount = User::where('role', 'volunteer')->count();

        // Events + registrations come from Supabase (single source of truth)
        $supabaseEvents = $queryService->getEventsPrivileged(1, 500, ['status' => 'active']);
        $supabaseRegistrations = $queryService->getEventRegistrations(1, 2000, []);

        $eventsArray = (is_array($supabaseEvents) && !isset($supabaseEvents['error'])) ? $supabaseEvents : [];
        $regsArray = (is_array($supabaseRegistrations) && !isset($supabaseRegistrations['error'])) ? $supabaseRegistrations : [];

        // High-level KPIs
        $kpis = [
            'users' => $userCount,
            'volunteers' => $volunteerCount,
            'events' => count($eventsArray),
            'registrations' => count($regsArray),
            'approved' => collect($regsArray)->where('registration_status', 'approved')->count(),
        ];

        // Build upcoming events list (next 6 upcoming by Supabase event_date)
        $today = now()->startOfDay();
        $events = collect($eventsArray)
            ->map(function (array $event) {
                $date = isset($event['event_date']) ? Carbon::parse($event['event_date']) : null;

                return (object) [
                    'title' => $event['title'] ?? 'Untitled event',
                    'date' => $date,
                ];
            })
            ->filter(function ($event) {
                return $event->date !== null;
            })
            ->filter(function ($event) use ($today) {
                return $event->date->gte($today);
            })
            ->sortBy('date')
            ->take(6);

        // Registrations by status
        $byStatus = [
            'pending' => collect($regsArray)->where('registration_status', 'pending')->count(),
            'approved' => collect($regsArray)->where('registration_status', 'approved')->count(),
            'rejected' => collect($regsArray)->where('registration_status', 'rejected')->count(),
        ];

        // Registrations over time (last 8 months) based on Supabase created_at
        $months = collect(range(7, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $registrationsOverTime = $months->mapWithKeys(function (Carbon $m) use ($regsArray) {
            $key = $m->format('Y-m');
            $count = collect($regsArray)->filter(function ($r) use ($m) {
                if (empty($r['created_at'])) return false;
                try {
                    $c = Carbon::parse((string) $r['created_at']);
                    return $c->year === $m->year && $c->month === $m->month;
                } catch (\Throwable $e) {
                    return false;
                }
            })->count();

            return [$key => $count];
        })->all();

        // Events per month (last 8 months) based on Supabase event_date
        $eventsPerMonth = $months->mapWithKeys(function (Carbon $m) use ($eventsArray) {
            $key = $m->format('Y-m');
            $count = collect($eventsArray)->filter(function ($e) use ($m) {
                if (empty($e['event_date'])) return false;
                try {
                    $d = Carbon::parse((string) $e['event_date']);
                    return $d->year === $m->year && $d->month === $m->month;
                } catch (\Throwable $e) {
                    return false;
                }
            })->count();

            return [$key => $count];
        })->all();

        return view('admin.reports', compact('kpis', 'events', 'byStatus', 'registrationsOverTime', 'eventsPerMonth'));
    }

    public function export(Request $request, DatabaseQueryService $queryService): StreamedResponse
    {
        // Simple CSV export of registrations + event info (best-effort).
        // This uses privileged Supabase queries and is restricted by middleware role:admin.
        $status = $request->query('status'); // optional: pending|approved|rejected|cancelled

        $supabaseRegistrations = $queryService->getEventRegistrations(1, 5000, []);
        $regsArray = (is_array($supabaseRegistrations) && !isset($supabaseRegistrations['error'])) ? $supabaseRegistrations : [];

        if ($status) {
            $regsArray = collect($regsArray)->where('registration_status', $status)->values()->all();
        }

        $filename = 'reports_registrations_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($regsArray) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'registration_id',
                'event_id',
                'event_title',
                'event_date',
                'user_id',
                'user_name',
                'user_email',
                'registration_status',
                'created_at',
            ]);

            foreach ($regsArray as $r) {
                // Many Supabase queries return nested objects when selecting foreign tables.
                $event = is_array($r['events'] ?? null) ? ($r['events'] ?? []) : [];
                $user = is_array($r['users'] ?? null) ? ($r['users'] ?? []) : [];

                fputcsv($out, [
                    $r['id'] ?? '',
                    $r['event_id'] ?? '',
                    $event['title'] ?? '',
                    $event['event_date'] ?? '',
                    $r['user_id'] ?? '',
                    $user['name'] ?? '',
                    $user['email'] ?? '',
                    $r['registration_status'] ?? '',
                    $r['created_at'] ?? '',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}


