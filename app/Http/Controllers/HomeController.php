<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Services\DatabaseQueryService;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
    }

    private const HOME_EVENTS_CACHE_TTL_SECONDS = 120;

    private function formatSupabaseTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        $time = trim($time);
        if ($time === '') {
            return null;
        }

        foreach (['H:i:s', 'H:i'] as $fmt) {
            try {
                return \Carbon\Carbon::createFromFormat($fmt, $time)->format('g:i A');
            } catch (\Throwable $e) {
                // try next format
            }
        }

        return $time;
    }

    private function formatTimeRange(?string $start, ?string $end): string
    {
        $startFormatted = $this->formatSupabaseTime($start);
        $endFormatted = $this->formatSupabaseTime($end);

        if ($startFormatted && $endFormatted) {
            return "{$startFormatted} – {$endFormatted}";
        }

        return $startFormatted ?? ($endFormatted ?? '');
    }

    public function index()
    {
        $members = Member::take(6)->get();

        // Upcoming events should come from Supabase (admins create events in Supabase)
        $eventsRaw = Cache::remember('home:events:v1', self::HOME_EVENTS_CACHE_TTL_SECONDS, function () {
            return $this->queryService->getEvents(1, 10, [
                'status' => 'active',
                'date_from' => now()->format('Y-m-d'),
            ]);
        });

        $events = collect(is_array($eventsRaw) && !isset($eventsRaw['error']) ? $eventsRaw : [])
            ->map(function (array $event) {
                return (object) [
                    'id' => $event['id'] ?? null,
                    'title' => $event['title'] ?? '',
                    'location' => $event['location'] ?? '',
                    'date' => isset($event['event_date']) ? \Carbon\Carbon::parse($event['event_date']) : null,
                    'time' => $this->formatTimeRange($event['event_time'] ?? null, $event['event_end_time'] ?? null),
                ];
            })
            ->filter(fn ($e) => $e->date instanceof \Carbon\Carbon)
            ->sortBy(fn ($e) => $e->date->timestamp)
            ->take(5)
            ->values();
        
        // Get officers for About section
        $officers = Member::whereIn('role', ['President', 'First Vice President', 'Second Vice President', 'Secretary', 'Treasurer'])
            ->orderBy('order')
            ->get();
        
        return view('home', compact('members', 'events', 'officers'));
    }
}
