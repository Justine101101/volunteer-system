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
        $this->middleware('role:superadmin|officer')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::orderBy('date', 'asc')->get();
        return view('events.index', compact('events'));
    }

    /**
     * Display the calendar view.
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
        
        // Get events for the month
        $events = Event::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy(function($event) {
                return $event->date->format('Y-m-d');
            });
        
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

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
            'created_by' => Auth::id(),
        ]);

        // Write-through to Supabase events
        $this->queryService->createEvent([
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->date->format('Y-m-d'),
            'event_time' => $event->time,
            'location' => $event->location,
            'created_by' => $event->created_by,
            'event_status' => 'active',
            'max_participants' => $event->max_participants ?? null,
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('creator', 'registrations.user');
        return view('events.show', compact('event'));
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

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
        ]);

        // Refresh the model to ensure date is cast properly
        $event->refresh();

        // Update in Supabase (best-effort, non-blocking)
        $this->queryService->updateEvent($event->id, [
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->date->format('Y-m-d'),
            'event_time' => $event->time,
            'location' => $event->location,
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $eventId = $event->id;
        
        // Delete photo if exists
        if ($event->photo_url) {
            $oldPath = str_replace('/storage/', '', $event->photo_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        // Delete from Supabase (best-effort, non-blocking)
        // This won't throw exceptions - it handles errors internally
        $this->queryService->deleteEvent($eventId);
        
        // Delete from local database
        $event->delete();
        
        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }
}
