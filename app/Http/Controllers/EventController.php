<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DatabaseQueryService;

class EventController extends Controller
{
    public function __construct(private DatabaseQueryService $queryService)
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     * Primary Database: Supabase
     */
    public function index()
    {
        $result = $this->queryService->getEvents(1, 1000); // Get all events
        
        if (isset($result['error'])) {
            Log::error('Failed to fetch events: ' . $result['error']);
            $events = [];
        } else {
            $events = is_array($result) ? $result : [];
            // Transform Supabase response to match expected format
            $events = array_map(function($event) {
                return (object) [
                    'id' => $event['id'] ?? null,
                    'title' => $event['title'] ?? '',
                    'description' => $event['description'] ?? '',
                    'date' => isset($event['event_date']) ? \Carbon\Carbon::parse($event['event_date']) : null,
                    'time' => $event['event_time'] ?? '',
                    'location' => $event['location'] ?? '',
                    'photo_url' => $event['photo_url'] ?? null,
                    'created_by' => $event['created_by'] ?? null,
                    'event_status' => $event['event_status'] ?? 'active',
                ];
            }, $events);
        }
        
        return view('events.index', compact('events'));
    }

    /**
     * Display the calendar view.
     * Primary Database: Supabase
     */
    public function calendar(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Validate year and month
        $year = max(2020, min(2100, (int)$year));
        $month = max(1, min(12, (int)$month));
        
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Get events for the month from Supabase
        $result = $this->queryService->getEvents(1, 1000, [
            'date_from' => $startDate->format('Y-m-d'),
            'date_to' => $endDate->format('Y-m-d'),
        ]);
        
        $events = [];
        if (!isset($result['error']) && is_array($result)) {
            // Transform and group events by date
            $events = collect($result)->map(function($event) {
                return (object) [
                    'id' => $event['id'] ?? null,
                    'title' => $event['title'] ?? '',
                    'description' => $event['description'] ?? '',
                    'date' => isset($event['event_date']) ? \Carbon\Carbon::parse($event['event_date']) : null,
                    'time' => $event['event_time'] ?? '',
                    'location' => $event['location'] ?? '',
                    'photo_url' => $event['photo_url'] ?? null,
                ];
            })->filter(function($event) {
                return $event->date !== null;
            })->sortBy(function($event) {
                return $event->date->format('Y-m-d') . ' ' . ($event->time ?? '00:00:00');
            })->groupBy(function($event) {
                return $event->date->format('Y-m-d');
            });
        }
        
        // Get previous and next month
        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();
        
        // Get today's date
        $today = now();
        
        return view('events.calendar', compact('events', 'startDate', 'prevMonth', 'nextMonth', 'today'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     * Single Source of Truth: Supabase only
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'date.after_or_equal' => 'The event date must be today or a future date.',
        ]);

        $photoUrl = null;
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = Str::slug($request->title) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('events', $filename, 'public');
            $photoUrl = Storage::url($path);
        }

        // Write only to Supabase (Single Source of Truth)
        $result = $this->queryService->createEvent([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->date,
            'event_time' => $request->time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
            'created_by' => Auth::id(),
            'event_status' => 'active',
            'max_participants' => $request->max_participants ?? null,
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to create event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create event. Please try again.');
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     * Primary Database: Supabase
     * Note: Route model binding still uses MySQL Event model for compatibility
     */
    public function show(Event $event)
    {
        // Find the Supabase event by matching fields
        $supabaseEvent = $this->queryService->findEventByFields(
            $event->title,
            $event->date->format('Y-m-d'),
            $event->location
        );

        if (!$supabaseEvent) {
            // Fallback: try to get by ID if it's a UUID
            if (is_string($event->id) && strlen($event->id) > 10) {
                $supabaseEvent = $this->queryService->getEventById($event->id);
            }
        }

        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            abort(404, 'Event not found');
        }

        // Transform Supabase response to object for view compatibility
        $eventData = (object) [
            'id' => $supabaseEvent['id'] ?? null,
            'title' => $supabaseEvent['title'] ?? '',
            'description' => $supabaseEvent['description'] ?? '',
            'date' => isset($supabaseEvent['event_date']) ? \Carbon\Carbon::parse($supabaseEvent['event_date']) : null,
            'time' => $supabaseEvent['event_time'] ?? '',
            'location' => $supabaseEvent['location'] ?? '',
            'photo_url' => $supabaseEvent['photo_url'] ?? null,
            'created_by' => $supabaseEvent['created_by'] ?? null,
            'event_status' => $supabaseEvent['event_status'] ?? 'active',
            'creator' => isset($supabaseEvent['creator']) ? (object) $supabaseEvent['creator'] : null,
            'registrations' => isset($supabaseEvent['registrations']) ? collect($supabaseEvent['registrations']) : collect(),
        ];

        return view('events.show', ['event' => $eventData]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     * Single Source of Truth: Supabase only
     * 
     * Note: This method still accepts Event model for route binding,
     * but we need to find the corresponding Supabase event by matching fields
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'date.after_or_equal' => 'The event date must be today or a future date.',
        ]);

        // Get the Supabase event ID by matching the local event
        // Since IDs don't match, we'll find by title+date+location
        $supabaseEvent = $this->queryService->getEventById($event->id);
        
        // If not found by ID, try to find by matching fields
        if (isset($supabaseEvent['error']) || !$supabaseEvent) {
            $events = $this->queryService->getEvents(1, 100, [
                'date_from' => $event->date->format('Y-m-d'),
                'date_to' => $event->date->format('Y-m-d'),
            ]);
            
            $supabaseEvent = null;
            if (is_array($events) && !isset($events['error'])) {
                foreach ($events as $evt) {
                    if (isset($evt['title']) && $evt['title'] === $event->title &&
                        isset($evt['location']) && $evt['location'] === $event->location) {
                        $supabaseEvent = $evt;
                        break;
                    }
                }
            }
        }

        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            Log::error('Could not find Supabase event to update for local event ID: ' . $event->id);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Event not found in database. Please refresh and try again.');
        }

        $supabaseEventId = is_array($supabaseEvent) ? ($supabaseEvent['id'] ?? null) : null;
        if (!$supabaseEventId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Could not identify event. Please refresh and try again.');
        }

        $photoUrl = $event->photo_url;
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($event->photo_url) {
                $oldPath = str_replace('/storage/', '', $event->photo_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            // Upload new photo
            $photo = $request->file('photo');
            $filename = Str::slug($request->title) . '-' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('events', $filename, 'public');
            $photoUrl = Storage::url($path);
        }

        // Update only in Supabase (Single Source of Truth)
        $result = $this->queryService->updateEvent($supabaseEventId, [
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->date,
            'event_time' => $request->time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to update event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update event. Please try again.');
        }

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * Single Source of Truth: Supabase only
     */
    public function destroy(Event $event)
    {
        // Get the Supabase event ID by matching the local event
        $supabaseEvent = $this->queryService->getEventById($event->id);
        
        // If not found by ID, try to find by matching fields
        if (isset($supabaseEvent['error']) || !$supabaseEvent) {
            $events = $this->queryService->getEvents(1, 100, [
                'date_from' => $event->date->format('Y-m-d'),
                'date_to' => $event->date->format('Y-m-d'),
            ]);
            
            $supabaseEvent = null;
            if (is_array($events) && !isset($events['error'])) {
                foreach ($events as $evt) {
                    if (isset($evt['title']) && $evt['title'] === $event->title &&
                        isset($evt['location']) && $evt['location'] === $event->location) {
                        $supabaseEvent = $evt;
                        break;
                    }
                }
            }
        }

        $supabaseEventId = null;
        if ($supabaseEvent && !isset($supabaseEvent['error'])) {
            $supabaseEventId = is_array($supabaseEvent) ? ($supabaseEvent['id'] ?? null) : null;
        }
        
        // Delete photo if exists
        if ($event->photo_url) {
            $oldPath = str_replace('/storage/', '', $event->photo_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        // Delete from Supabase (Single Source of Truth)
        if ($supabaseEventId) {
            $result = $this->queryService->deleteEvent($supabaseEventId);
            if (isset($result['error'])) {
                Log::error('Failed to delete event from Supabase: ' . ($result['error'] ?? 'Unknown error'));
                return redirect()->back()->with('error', 'Failed to delete event. Please try again.');
            }
        } else {
            Log::warning('Could not find Supabase event to delete for local event ID: ' . $event->id);
        }
        
        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }
}
