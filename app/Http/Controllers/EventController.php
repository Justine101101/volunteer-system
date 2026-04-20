<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Services\DatabaseQueryService;
use App\Services\SupabaseService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    private const EVENTS_INDEX_CACHE_TTL_SECONDS = 180;
    private const USER_REG_CACHE_TTL_SECONDS = 60;

    public function __construct(
        private DatabaseQueryService $queryService,
        private SupabaseService $supabaseService
    )
    {
        // Allow public/volunteer access to the event list, detail page, and calendar.
        // Restrict creation and management actions to authenticated admins.
        $this->middleware('auth')->except(['index', 'show', 'calendar']);
        $this->middleware('role:admin')->except(['index', 'show', 'calendar']);
    }

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

        // If Supabase ever returns something unexpected, fall back to the raw value.
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

    private function normalizePhotoUrl(?string $photoUrl): ?string
    {
        if (!$photoUrl || $photoUrl === 'null' || trim($photoUrl) === '') {
            return null;
        }

        $photoUrl = trim($photoUrl);

        // Convert expiring signed Supabase URLs into stable public URLs for public buckets.
        // Example:
        // /storage/v1/object/sign/<bucket>/<path>?token=...  -> /storage/v1/object/public/<bucket>/<path>
        if (preg_match('#/storage/v1/object/sign/([^/]+)/([^?]+)#', $photoUrl, $m)) {
            $bucket = $m[1];
            $path = $m[2];
            $base = rtrim((string) config('supabase.url'), '/');
            if ($base !== '') {
                return $base . '/storage/v1/object/public/' . $bucket . '/' . $path;
            }
        }

        // Keep absolute URLs as-is.
        if (str_starts_with($photoUrl, 'http://') || str_starts_with($photoUrl, 'https://')) {
            return $photoUrl;
        }

        // Prefer origin-relative paths on Cloud to avoid APP_URL/mixed-content issues.
        if (str_starts_with($photoUrl, '/storage/')) {
            return $photoUrl;
        }

        if (str_starts_with($photoUrl, 'storage/')) {
            return '/' . $photoUrl;
        }

        return '/storage/' . ltrim($photoUrl, '/');
    }

    private function resolveCheckInExpiry(array $event): \Carbon\Carbon
    {
        $eventDateRaw = (string) ($event['event_date'] ?? '');
        $endTimeRaw = trim((string) ($event['event_end_time'] ?? ''));

        try {
            $eventDate = \Carbon\Carbon::parse($eventDateRaw);
        } catch (\Throwable $e) {
            return now()->addMinutes(30);
        }

        // Default: event day end with small grace period.
        $expiresAt = $eventDate->copy()->endOfDay()->addMinutes(90);

        if ($endTimeRaw !== '') {
            try {
                $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $endTimeRaw);
                $expiresAt = $eventDate->copy()->setTime($endTime->hour, $endTime->minute, $endTime->second)->addMinutes(90);
            } catch (\Throwable $e) {
                try {
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $endTimeRaw);
                    $expiresAt = $eventDate->copy()->setTime($endTime->hour, $endTime->minute, 0)->addMinutes(90);
                } catch (\Throwable $e2) {
                    // Keep end-of-day fallback.
                }
            }
        }

        if ($expiresAt->isPast()) {
            return now()->addMinutes(5);
        }

        return $expiresAt;
    }

    private function uploadEventPhoto(\Illuminate\Http\UploadedFile $photo, string $title): ?string
    {
        $filename = Str::slug($title) . '-' . time() . '.' . $photo->getClientOriginalExtension();
        $bucket = (string) config('supabase.bucket_name', 'volunteer-portal');
        $path = 'events/' . $filename;

        try {
            $result = $this->supabaseService->uploadFile($bucket, $path, $photo->getContent());
            if (is_array($result) && isset($result['error'])) {
                throw new \RuntimeException((string) $result['error']);
            }

            return $this->supabaseService->getFileUrl($bucket, $path);
        } catch (\Throwable $e) {
            Log::warning('Supabase photo upload failed, falling back to local storage', [
                'message' => $e->getMessage(),
                'bucket' => $bucket,
                'path' => $path,
            ]);

            // Fallback for local dev if Supabase Storage config/policy is unavailable.
            $localPath = $photo->storeAs('events', $filename, 'public');
            return Storage::url($localPath);
        }
    }

    /**
     * Display a listing of the resource.
     * Primary Database: Supabase
     */
    public function index()
    {
        $result = Cache::remember('events:index:v1', self::EVENTS_INDEX_CACHE_TTL_SECONDS, function () {
            return $this->queryService->getEvents(1, 1000); // Get all events
        });
        
        if (isset($result['error'])) {
            Log::error('Failed to fetch events: ' . $result['error']);
            $events = [];
        } else {
            $events = is_array($result) ? $result : [];

            // Approved volunteer counts (safe server-side aggregate; avoids RLS hiding data)
            $eventIds = array_values(array_filter(array_map(static fn ($e) => is_array($e) ? ($e['id'] ?? null) : null, $events)));
            $approvedCounts = $this->queryService->getApprovedRegistrationCountsForEventsPrivileged($eventIds);
            $approvedVolunteersByEvent = $this->queryService->getApprovedVolunteersForEventsPrivileged($eventIds);

            // Transform Supabase response to match expected format
            $events = array_map(function($event) use ($approvedCounts, $approvedVolunteersByEvent) {
                $photoUrl = $event['photo_url'] ?? null;
                
                // Log original photo_url for debugging
                Log::debug('Event photo URL processing', [
                    'event_id' => $event['id'] ?? null,
                    'event_title' => $event['title'] ?? null,
                    'original_photo_url' => $photoUrl,
                    'photo_url_type' => gettype($photoUrl),
                    'photo_url_empty' => empty($photoUrl),
                ]);
                
                $photoUrl = $this->normalizePhotoUrl($photoUrl);

                if ($photoUrl) {
                    Log::info('Event photo URL converted', [
                        'event_id' => $event['id'] ?? null,
                        'event_title' => $event['title'] ?? null,
                        'final_photo_url' => $photoUrl,
                    ]);
                } else {
                    $photoUrl = null;
                    Log::warning('Event has no photo_url', [
                        'event_id' => $event['id'] ?? null,
                        'event_title' => $event['title'] ?? null,
                    ]);
                }
                
                $id = (string) ($event['id'] ?? '');
                $approved = $id !== '' ? (int) ($approvedCounts[$id] ?? 0) : 0;
                $approvedVolunteers = $id !== '' ? ($approvedVolunteersByEvent[$id] ?? []) : [];

                return (object) [
                    'id' => $event['id'] ?? null,
                    'title' => $event['title'] ?? '',
                    'description' => $event['description'] ?? '',
                    'organizer' => $event['organizer'] ?? '',
                    'requirements' => $event['requirements'] ?? '',
                    'venue' => $event['venue'] ?? '',
                    'date' => isset($event['event_date']) ? \Carbon\Carbon::parse($event['event_date']) : null,
                    // For backward compatibility, keep a display string in ->time
                    'start_time' => $event['event_time'] ?? '',
                    'end_time' => $event['event_end_time'] ?? null,
                    // Display: 12-hour time with AM/PM (no Supabase changes needed)
                    'time' => $this->formatTimeRange($event['event_time'] ?? null, $event['event_end_time'] ?? null),
                    'location' => $event['location'] ?? '',
                    'photo_url' => $photoUrl,
                    'created_by' => $event['created_by'] ?? null,
                    'event_status' => $event['event_status'] ?? 'active',
                    // Used in the event modal ("Volunteers")
                    'current_volunteers' => $approved,
                    'approved_volunteers' => $approvedVolunteers,
                    'max_participants' => $event['max_participants'] ?? null,
                ];
            }, $events);

            // Sort events so the most recent/future events appear first for the user
            usort($events, function ($a, $b) {
                // Handle null dates safely
                if ($a->date === null && $b->date === null) {
                    return 0;
                }
                if ($a->date === null) {
                    return 1;
                }
                if ($b->date === null) {
                    return -1;
                }
                // Newer dates first
                return $b->date->timestamp <=> $a->date->timestamp;
            });
        }

        // Build a map of the current user's registrations by event ID so we can show "Pending" etc.
        $userRegistrationsByEvent = [];
        if (Auth::check() && isset(Auth::user()->email)) {
            try {
                $user = Auth::user();
                $cacheKey = 'events:user-reg-map:v1:' . sha1((string) $user->email);
                $userRegistrationsByEvent = Cache::remember($cacheKey, self::USER_REG_CACHE_TTL_SECONDS, function () use ($user) {
                    $map = [];
                    $supabaseUser = $this->queryService->getUserByEmail($user->email);
                    $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;

                    if ($supabaseUserId) {
                        // Use a lightweight, user-scoped query so we always see this volunteer's registrations
                        $regs = $this->queryService->getEventRegistrationsForUser($supabaseUserId);
                        if (is_array($regs) && !isset($regs['error'])) {
                            foreach ($regs as $reg) {
                                $eventId = $reg['event_id'] ?? null;
                                if ($eventId) {
                                    $map[(string) $eventId] = strtolower($reg['registration_status'] ?? 'pending');
                                }
                            }
                        }
                    }

                    return $map;
                });
            } catch (\Throwable $e) {
                Log::error('Error building user event registration map', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $showVolunteerEventSections = Auth::check() && Auth::user()->isVolunteer();
        $volunteerMyEvents = [];
        $volunteerBrowseEvents = [];
        if ($showVolunteerEventSections) {
            foreach ($events as $event) {
                $eid = isset($event->id) ? (string) $event->id : '';
                if ($eid !== '' && isset($userRegistrationsByEvent[$eid])) {
                    $volunteerMyEvents[] = $event;
                } else {
                    $volunteerBrowseEvents[] = $event;
                }
            }
            usort($volunteerMyEvents, function ($a, $b) {
                $da = $a->date ?? null;
                $db = $b->date ?? null;
                if (!$da instanceof \Carbon\Carbon && !$db instanceof \Carbon\Carbon) {
                    return 0;
                }
                if (!$da instanceof \Carbon\Carbon) {
                    return 1;
                }
                if (!$db instanceof \Carbon\Carbon) {
                    return -1;
                }

                return $da->timestamp <=> $db->timestamp;
            });
        }

        return view('events.index', [
            'events' => $events,
            'userRegistrationsByEvent' => $userRegistrationsByEvent,
            'showVolunteerEventSections' => $showVolunteerEventSections,
            'volunteerMyEvents' => $volunteerMyEvents,
            'volunteerBrowseEvents' => $volunteerBrowseEvents,
        ]);
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
                    'start_time' => $event['event_time'] ?? '',
                    'end_time' => $event['event_end_time'] ?? null,
                    'time' => $this->formatTimeRange($event['event_time'] ?? null, $event['event_end_time'] ?? null),
                    'location' => $event['location'] ?? '',
                    'photo_url' => $event['photo_url'] ?? null,
                ];
            })->filter(function($event) {
                return $event->date !== null;
            })->sortBy(function($event) {
                // Sort by raw start_time to keep correct ordering (time display is 12-hour).
                return $event->date->format('Y-m-d') . ' ' . ($event->start_time ?? '00:00:00');
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
            'organizer' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'date.after_or_equal' => 'The event date must be today or a future date.',
        ]);

        $photoUrl = null;
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoUrl = $this->uploadEventPhoto($photo, $request->title);
        }

        // Write only to Supabase (Single Source of Truth)
        $result = $this->queryService->createEvent([
            'title' => $request->title,
            'description' => $request->description,
            'organizer' => $request->organizer,
            'requirements' => $request->requirements,
            'venue' => $request->venue,
            'event_date' => $request->date,
            // Supabase schema stores TIME; save start time as event_time
            'event_time' => $request->start_time,
            // Store end time in a separate column (see database/supabase/ALTER_EVENTS_ADD_END_TIME.sql)
            'event_end_time' => $request->end_time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
            'created_by' => Auth::id(),
            'event_status' => 'active',
            'max_participants' => $request->max_participants ?? null,
        ]);

        if (isset($result['error'])) {
            $errorMessage = $result['error'] ?? 'Unknown error';
            $errorDetails = $result['details'] ?? null;
            
            Log::error('Failed to create event in Supabase', [
                'error' => $errorMessage,
                'status' => $result['status'] ?? null,
                'details' => $errorDetails,
                'request_data' => $request->all(),
            ]);
            
            // Provide more helpful error message
            $userMessage = 'Failed to create event. ';
            if (str_contains($errorMessage, 'permission') || str_contains($errorMessage, 'policy')) {
                $userMessage .= 'Database permission error. Please check Supabase configuration.';
            } elseif (str_contains($errorMessage, 'column') || str_contains($errorMessage, 'field')) {
                $userMessage .= 'Database schema error. Please check if all required columns exist.';
            } elseif (str_contains($errorMessage, 'foreign key') || str_contains($errorMessage, 'constraint')) {
                $userMessage .= 'Data validation error. Please check your input data.';
            } else {
                $userMessage .= 'Please try again or contact support.';
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $userMessage);
        }

        Cache::forget('events:index:v1');
        Cache::forget('home:events:v1');
        Cache::forget('admin:dashboard:v1');
        if (Auth::check() && !empty(Auth::user()->email)) {
            Cache::forget('events:user-reg-map:v1:' . sha1((string) Auth::user()->email));
            Cache::forget('dashboard:volunteer:v1:' . sha1((string) Auth::user()->email));
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     * Primary Database: Supabase
     * @param string $eventId UUID of the event in Supabase
     */
    public function show(string $eventId)
    {
        $showVolunteers = request()->boolean('show_volunteers');

        // Get event from Supabase by UUID.
        // Use privileged access for admin views so RLS doesn't hide registrations.
        $isAdminViewer = auth()->check() && method_exists(auth()->user(), 'isAdminOrSuperAdmin') && auth()->user()->isAdminOrSuperAdmin();
        $supabaseEvent = $isAdminViewer
            ? $this->queryService->getEventByIdWithRegistrationsPrivileged($eventId)
            : $this->queryService->getEventById($eventId);

        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            abort(404, 'Event not found');
        }

        // Transform Supabase response to object for view compatibility
        $photoUrl = $supabaseEvent['photo_url'] ?? null;
        $photoUrl = $this->normalizePhotoUrl($photoUrl);
        
        // Debug logging
        Log::debug('Event show photo URL', [
            'event_id' => $supabaseEvent['id'] ?? null,
            'original_url' => $supabaseEvent['photo_url'] ?? null,
            'converted_url' => $photoUrl,
        ]);
        
        $registrations = collect($supabaseEvent['registrations'] ?? [])->map(function ($reg) {
            return (object) [
                'id' => $reg['id'] ?? null,
                'registration_status' => $reg['registration_status'] ?? ($reg['status'] ?? 'pending'),
                'user' => isset($reg['user']) ? (object) $reg['user'] : null,
                'created_at' => isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at']) : null,
            ];
        });

        // Always compute approved count via privileged aggregate so it works even when RLS hides registrations.
        $approvedRegistrationsCount = $this->queryService->getApprovedRegistrationCountForEventPrivileged($eventId);
        $approvedVolunteers = $this->queryService->getApprovedVolunteersForEventPrivileged($eventId);

        $eventData = (object) [
            'id' => $supabaseEvent['id'] ?? null,
            'title' => $supabaseEvent['title'] ?? '',
            'description' => $supabaseEvent['description'] ?? '',
            'organizer' => $supabaseEvent['organizer'] ?? '',
            'requirements' => $supabaseEvent['requirements'] ?? '',
            'venue' => $supabaseEvent['venue'] ?? '',
            'date' => isset($supabaseEvent['event_date']) ? \Carbon\Carbon::parse($supabaseEvent['event_date']) : null,
            'start_time' => $supabaseEvent['event_time'] ?? '',
            'end_time' => $supabaseEvent['event_end_time'] ?? null,
            'time' => $this->formatTimeRange($supabaseEvent['event_time'] ?? null, $supabaseEvent['event_end_time'] ?? null),
            'location' => $supabaseEvent['location'] ?? '',
            'photo_url' => $photoUrl,
            'created_by' => $supabaseEvent['created_by'] ?? null,
            'event_status' => $supabaseEvent['event_status'] ?? 'active',
            'creator' => isset($supabaseEvent['creator']) ? (object) $supabaseEvent['creator'] : null,
            'registrations' => $registrations,
            'registrations_count' => $registrations->count(),
            'approved_registrations_count' => $approvedRegistrationsCount,
            'approved_volunteers' => $approvedVolunteers,
            'max_participants' => $supabaseEvent['max_participants'] ?? null,
        ];

        // Resolve current user's Supabase UUID for registration lookups
        $currentUserSupabaseId = null;
        if (auth()->check() && isset(auth()->user()->email)) {
            $supabaseUser = $this->queryService->getUserByEmail(auth()->user()->email);
            if ($supabaseUser && !isset($supabaseUser['error'])) {
                $currentUserSupabaseId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;
            }
        }

        $checkInUrl = null;
        $checkInQrSvg = null;
        if ($isAdminViewer) {
            $checkInUrl = URL::temporarySignedRoute(
                'events.checkin.show',
                $this->resolveCheckInExpiry($supabaseEvent),
                ['eventId' => $eventId]
            );

            try {
                $checkInQrSvg = QrCode::format('svg')->size(260)->margin(1)->generate($checkInUrl);
            } catch (\Throwable $e) {
                Log::warning('Failed to generate event check-in QR SVG', [
                    'event_id' => $eventId,
                    'message' => $e->getMessage(),
                ]);
                $checkInQrSvg = null;
            }
        }

        $feedbackSummary = $this->queryService->getEventFeedbackSummaryForEventPrivileged($eventId);
        $currentUserFeedback = null;
        if (!$isAdminViewer && $currentUserSupabaseId) {
            $currentUserFeedback = $this->queryService->getEventFeedbackForUser($eventId, (string) $currentUserSupabaseId);
        }

        return view('events.show', [
            'event' => $eventData,
            'currentUserSupabaseId' => $currentUserSupabaseId,
            'showVolunteers' => $showVolunteers,
            'checkInUrl' => $checkInUrl,
            'checkInQrSvg' => $checkInQrSvg,
            'feedbackSummary' => $feedbackSummary,
            'currentUserFeedback' => $currentUserFeedback,
        ]);
    }

    /**
     * Submit a 1-5 star feedback rating for an event (volunteers only).
     */
    public function submitFeedback(Request $request, string $eventId): RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !$user->isVolunteer() || empty($user->email)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $event = $this->queryService->getEventByIdPrivileged($eventId);
        if (!is_array($event) || isset($event['error'])) {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'Event not found.');
        }

        $eventStatus = strtolower((string) ($event['event_status'] ?? 'active'));
        $eventDate = null;
        if (!empty($event['event_date'])) {
            try {
                $eventDate = \Carbon\Carbon::parse((string) $event['event_date']);
            } catch (\Throwable $e) {
                $eventDate = null;
            }
        }
        $eventIsOver = ($eventStatus === 'completed') || ($eventDate instanceof \Carbon\Carbon && $eventDate->isPast() && !$eventDate->isToday());
        if (!$eventIsOver) {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'Feedback is available after the event ends.');
        }

        $supabaseUser = $this->queryService->getUserByEmail((string) $user->email);
        $supabaseUserId = is_array($supabaseUser) ? ($supabaseUser['id'] ?? null) : null;
        if (!is_string($supabaseUserId) || $supabaseUserId === '') {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'Could not identify your event account.');
        }

        $registration = $this->queryService->getEventRegistration($supabaseUserId, $eventId);
        if (!is_array($registration) || isset($registration['error'])) {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'You must be registered to leave feedback.');
        }

        $status = strtolower((string) ($registration['registration_status'] ?? 'pending'));
        if ($status !== 'approved') {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'Only approved volunteers can leave feedback.');
        }

        $result = $this->queryService->upsertEventFeedback(
            eventId: $eventId,
            userId: $supabaseUserId,
            rating: (int) $validated['rating'],
            comment: isset($validated['comment']) ? trim((string) $validated['comment']) : null
        );

        if (is_array($result) && isset($result['error'])) {
            return redirect()->route('events.show', ['eventId' => $eventId])->with('error', 'Could not save feedback. Please try again.');
        }

        return redirect()->route('events.show', ['eventId' => $eventId])->with('success', 'Thanks for your feedback!');
    }

    /**
     * Show the form for editing the specified resource.
     * Primary Database: Supabase
     * @param string $eventId UUID of the event in Supabase
     */
    public function edit(string $eventId)
    {
        // Admin edit must use privileged access to avoid RLS returning partial/empty data.
        $supabaseEvent = $this->queryService->getEventByIdWithRegistrationsPrivileged($eventId);
        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            // Fallback to non-privileged lookup for environments with permissive policies.
            $supabaseEvent = $this->queryService->getEventById($eventId);
        }

        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            abort(404, 'Event not found');
        }

        // Some environments can return partial event payloads on single-row fetch.
        // If critical fields are missing, merge from the privileged events list.
        $criticalFields = ['title', 'description', 'location', 'event_date', 'event_time'];
        $missingCriticalField = false;
        foreach ($criticalFields as $field) {
            if (!array_key_exists($field, $supabaseEvent) || $supabaseEvent[$field] === null || $supabaseEvent[$field] === '') {
                $missingCriticalField = true;
                break;
            }
        }

        if ($missingCriticalField) {
            $allEvents = $this->queryService->getEventsPrivileged(1, 1000);
            if (is_array($allEvents) && !isset($allEvents['error'])) {
                foreach ($allEvents as $candidate) {
                    if (!is_array($candidate)) {
                        continue;
                    }
                    if ((string) ($candidate['id'] ?? '') !== $eventId) {
                        continue;
                    }
                    $supabaseEvent = array_merge($candidate, $supabaseEvent);
                    break;
                }
            }
        }

        // Transform Supabase response to object for view compatibility
        $photoUrl = $supabaseEvent['photo_url'] ?? null;
        $photoUrl = $this->normalizePhotoUrl($photoUrl);
        
        $eventDate = null;
        if (!empty($supabaseEvent['event_date'])) {
            try {
                $eventDate = \Carbon\Carbon::parse((string) $supabaseEvent['event_date']);
            } catch (\Throwable $e) {
                $eventDate = null;
            }
        }

        $startTime = (string) ($supabaseEvent['event_time'] ?? '');
        $endTime = (string) ($supabaseEvent['event_end_time'] ?? '');
        $startTimeForInput = '';
        $endTimeForInput = '';

        if ($startTime !== '') {
            try {
                $startTimeForInput = \Carbon\Carbon::createFromFormat('H:i:s', $startTime)->format('H:i');
            } catch (\Throwable $e) {
                try {
                    $startTimeForInput = \Carbon\Carbon::createFromFormat('H:i', $startTime)->format('H:i');
                } catch (\Throwable $e2) {
                    $startTimeForInput = '';
                }
            }
        }

        if ($endTime !== '') {
            try {
                $endTimeForInput = \Carbon\Carbon::createFromFormat('H:i:s', $endTime)->format('H:i');
            } catch (\Throwable $e) {
                try {
                    $endTimeForInput = \Carbon\Carbon::createFromFormat('H:i', $endTime)->format('H:i');
                } catch (\Throwable $e2) {
                    $endTimeForInput = '';
                }
            }
        }

        $eventData = (object) [
            'id' => $supabaseEvent['id'] ?? null,
            'title' => $supabaseEvent['title'] ?? '',
            'description' => $supabaseEvent['description'] ?? '',
            'organizer' => $supabaseEvent['organizer'] ?? '',
            'requirements' => $supabaseEvent['requirements'] ?? '',
            'venue' => $supabaseEvent['venue'] ?? '',
            'date' => $eventDate,
            'start_time' => $startTimeForInput,
            'end_time' => $endTimeForInput,
            'time' => trim(($startTimeForInput !== '' ? $startTimeForInput : '') . ($endTimeForInput !== '' ? (' - ' . $endTimeForInput) : '')),
            'location' => $supabaseEvent['location'] ?? '',
            'photo_url' => $photoUrl,
            'created_by' => $supabaseEvent['created_by'] ?? null,
            'event_status' => $supabaseEvent['event_status'] ?? 'active',
            'max_participants' => $supabaseEvent['max_participants'] ?? null,
        ];

        // Pass both the hydrated event object and the raw Supabase UUID explicitly
        return view('events.edit', [
            'event' => $eventData,
            'eventId' => $eventId,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Single Source of Truth: Supabase only
     * @param string $eventId UUID of the event in Supabase
     */
    public function update(Request $request, string $eventId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organizer' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'date.after_or_equal' => 'The event date must be today or a future date.',
        ]);

        // Get the current event from Supabase to get the photo_url
        $supabaseEvent = $this->queryService->getEventById($eventId);

        if (!$supabaseEvent || isset($supabaseEvent['error'])) {
            Log::error('Could not find Supabase event to update: ' . $eventId);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Event not found in database. Please refresh and try again.');
        }

        // Get current photo_url from Supabase event
        $photoUrl = is_array($supabaseEvent) ? ($supabaseEvent['photo_url'] ?? null) : null;
        
        if ($request->hasFile('photo')) {
            // Upload replacement photo; keeping old file cleanup out-of-band avoids cloud storage path mismatches.
            $photo = $request->file('photo');
            $photoUrl = $this->uploadEventPhoto($photo, $request->title);
        }

        // Update only in Supabase (Single Source of Truth)
        $result = $this->queryService->updateEvent($eventId, [
            'title' => $request->title,
            'description' => $request->description,
            'organizer' => $request->organizer,
            'requirements' => $request->requirements,
            'venue' => $request->venue,
            'event_date' => $request->date,
            // Supabase schema stores TIME; save start time as event_time
            'event_time' => $request->start_time,
            // Store end time in a separate column (see database/supabase/ALTER_EVENTS_ADD_END_TIME.sql)
            'event_end_time' => $request->end_time,
            'location' => $request->location,
            'photo_url' => $photoUrl,
        ]);

        if (isset($result['error'])) {
            Log::error('Failed to update event in Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update event. Please try again.');
        }

        Cache::forget('events:index:v1');
        Cache::forget('home:events:v1');
        Cache::forget('admin:dashboard:v1');
        if (Auth::check() && !empty(Auth::user()->email)) {
            Cache::forget('dashboard:volunteer:v1:' . sha1((string) Auth::user()->email));
        }

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * Single Source of Truth: Supabase only
     * @param string $eventId UUID of the event in Supabase
     */
    public function destroy(string $eventId)
    {
        // Get the event from Supabase to get photo_url for deletion
        $supabaseEvent = $this->queryService->getEventById($eventId);
        
        // Delete from Supabase (Single Source of Truth)
        $result = $this->queryService->deleteEvent($eventId);
        if (isset($result['error'])) {
            Log::error('Failed to delete event from Supabase: ' . ($result['error'] ?? 'Unknown error'));
            return redirect()->back()->with('error', 'Failed to delete event. Please try again.');
        }

        Cache::forget('events:index:v1');
        Cache::forget('home:events:v1');
        Cache::forget('admin:dashboard:v1');
        if (Auth::check() && !empty(Auth::user()->email)) {
            Cache::forget('dashboard:volunteer:v1:' . sha1((string) Auth::user()->email));
        }
        
        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }
}
