<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Upcoming Events') }}
            </h2>

            @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="hidden sm:flex flex-col items-start mr-1">
                                <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-600">{{ auth()->user()->isAdminOrSuperAdmin() ? 'Admin' : 'Volunteer' }}</span>
                            </div>
                            <x-user-avatar :user="auth()->user()" :size="36" class="shadow-sm" />
                            <svg class="ms-1 fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">
                            {{ __('Home') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('settings')">
                            {{ __('Settings') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            @endauth
        </div>
    </x-slot>

    <!-- Hero Section -->
    <div class="py-20 bg-light-gray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-left">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-dark mb-3">Upcoming Events</h1>
                <p class="text-lg text-gray-600 leading-relaxed max-w-3xl">Discover opportunities to make a difference in your community through our volunteer events.</p>
            </div>
            @auth
                @if(auth()->user()->isAdminOrSuperAdmin())
                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('events.create') }}"
                           class="inline-flex items-center px-8 py-4 rounded-2xl text-sm font-semibold tracking-wide uppercase shadow-soft bg-lions-green text-white hover:bg-lions-green/90 focus:outline-none focus:ring-2 focus:ring-lions-green/20 transition transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Event
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Events Section -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
             x-data="{ openModal: false, event: null,
                open(payload) { this.event = payload; this.openModal = true; },
                close() { this.openModal = false; this.event = null; }
             }">
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-lions-green bg-lions-green/10 px-4 py-3 text-sm font-semibold text-lions-green shadow-soft">
                    <svg class="mt-0.5 h-5 w-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-500 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 shadow-soft">
                    <svg class="mt-0.5 h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 110 14 7 7 0 010-14z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                    <div class="lg:col-span-7">
                        <label for="event-search" class="sr-only">Search events</label>
                        <input
                            id="event-search"
                            type="text"
                            placeholder="Search events"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                        >
                    </div>
                    <div class="lg:col-span-3">
                        <label for="event-category-filter" class="sr-only">Category</label>
                        <select id="event-category-filter" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="all">All Categories</option>
                            <option value="environment">Environment</option>
                            <option value="health">Health</option>
                            <option value="community">Community</option>
                            <option value="education">Education</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label for="event-date-filter" class="sr-only">Date range</label>
                        <select id="event-date-filter" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="all">Any Date</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="upcoming">Upcoming</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="events-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($events as $event)
                @php /** @var \stdClass|\App\Models\Event $event */ @endphp
                @php
                    $isEven = ($loop->iteration % 2) === 0;
                    $currentVolunteers = $event->current_volunteers ?? 0;
                    $maxVolunteers = $event->max_participants ?? null;

                    // Status display: Upcoming / Ongoing / Ended / Completed
                    // - If event_status is explicitly "completed", show Completed.
                    // - Otherwise derive from date/time (no Supabase schema changes needed).
                    $rawStatus = strtolower((string) ($event->event_status ?? ''));
                    $now = now();
                    $eventDate = $event->date ?? null; // Carbon (from controller) or null

                    $derived = 'Upcoming';
                    if ($rawStatus === 'completed') {
                        $derived = 'Completed';
                    } elseif ($eventDate instanceof \Carbon\Carbon) {
                        $start = null;
                        $end = null;
                        if (!empty($event->start_time)) {
                            try {
                                $start = \Carbon\Carbon::createFromFormat('H:i:s', (string) $event->start_time);
                            } catch (\Throwable $e) {
                                // ignore parse errors
                            }
                        }
                        if (!empty($event->end_time)) {
                            try {
                                $end = \Carbon\Carbon::createFromFormat('H:i:s', (string) $event->end_time);
                            } catch (\Throwable $e) {
                                // ignore parse errors
                            }
                        }

                        if ($eventDate->isToday()) {
                            if ($start && $end) {
                                $startAt = $eventDate->copy()->setTimeFrom($start);
                                $endAt = $eventDate->copy()->setTimeFrom($end);
                                $derived = ($now->between($startAt, $endAt)) ? 'Ongoing' : ($now->greaterThan($endAt) ? 'Ended' : 'Upcoming');
                            } else {
                                // If no time window, treat today as ongoing
                                $derived = 'Ongoing';
                            }
                        } elseif ($eventDate->isPast()) {
                            $derived = 'Ended';
                        } else {
                            $derived = 'Upcoming';
                        }
                    }

                    $statusLabel = $derived;
                    $canJoin = !in_array($statusLabel, ['Ended', 'Completed'], true);
                    $statusClasses = match ($statusLabel) {
                        'Ongoing' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                        'Ended' => 'bg-gray-50 text-gray-700 border border-gray-200',
                        'Completed' => 'bg-lions-green/10 text-lions-green border border-lions-green',
                        default => 'bg-amber-50 text-amber-700 border border-amber-200', // Upcoming
                    };
                    $category = strtolower(trim((string) ($event->category ?? 'general')));
                @endphp
                <div
                    class="event-card bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300"
                    data-event-card
                    data-event-title="{{ strtolower((string) ($event->title ?? '')) }}"
                    data-event-description="{{ strtolower((string) strip_tags($event->description ?? '')) }}"
                    data-event-location="{{ strtolower((string) ($event->location ?? '')) }}"
                    data-event-category="{{ $category }}"
                    data-event-date="{{ optional($event->date)->format('Y-m-d') ?? '' }}"
                >
                    <div class="relative h-44 bg-slate-100 overflow-hidden">
                        @if($event->photo_url)
                            <img src="{{ $event->photo_url }}"
                                 alt="{{ $event->title }}"
                                 class="event-image w-full h-full object-cover"
                                 loading="lazy" decoding="async"
                                 onerror="this.style.display='none';">
                        @endif
                        <span class="absolute top-3 left-3 inline-flex items-center h-7 px-3 rounded-full text-xs font-semibold bg-white/95 border border-emerald-100 text-emerald-700 capitalize">
                            {{ $category }}
                        </span>
                        <div class="absolute top-3 right-3 rounded-xl bg-white/95 px-3 py-1.5 text-center shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-emerald-700">{{ optional($event->date)->format('M') ?? 'TBA' }}</p>
                            <p class="text-2xl leading-none font-extrabold text-slate-800">{{ optional($event->date)->format('d') ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="mb-2">
                            <span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        @auth
                            @if(auth()->user()->isAdminOrSuperAdmin() && !empty($event->id))
                                <div class="mb-2 flex items-center gap-3">
                                    <a href="{{ route('events.edit', ['eventId' => $event->id]) }}"
                                       class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline transition">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('events.destroy', ['eventId' => $event->id]) }}"
                                          class="inline"
                                          data-confirm="Delete this event? This action cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <h3 class="event-title text-xl font-bold text-slate-900 leading-snug line-clamp-2 min-h-[3.25rem]">
                            {{ e($event->title ?: 'Untitled event') }}
                        </h3>

                        <p class="mt-2 text-sm text-slate-600 line-clamp-2 min-h-[2.5rem]">
                            {{ \Illuminate\Support\Str::limit($event->description ? strip_tags($event->description) : 'Details will be announced soon.', 90) }}
                        </p>

                        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-lg bg-slate-50 px-2.5 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-slate-400">Full location</p>
                                <p class="font-medium text-slate-700 line-clamp-1">{{ e($event->location ?: 'Address TBA') }}</p>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-2.5 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-slate-400">Time</p>
                                <p class="font-medium text-slate-700 line-clamp-1">{{ e($event->time ?: 'Time TBA') }}</p>
                            </div>

                            <!-- Registration Section -->
                            @auth
                                @if(!empty($event->id))
                                    @php
                                        // Map built in EventController@index from Supabase registrations
                                        $regStatus = $userRegistrationsByEvent[$event->id] ?? null;

                                        // Decide badge style based on status
                                        if ($regStatus === 'approved') {
                                            $statusStyle = 'background: linear-gradient(to right, #008751, #10b981); color: white;';
                                        } elseif ($regStatus === 'rejected') {
                                            $statusStyle = 'background-color: #fee2e2; color: #991b1b;';
                                        } else {
                                            $statusStyle = 'background: linear-gradient(to right, #6366f1, #8b5cf6); color: white;';
                                        }
                                    @endphp

                                    @if($regStatus)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold shadow-sm"
                                                      style="{{ $statusStyle }}">
                                                    @if($regStatus === 'approved')
                                                        ✓ Approved
                                                    @elseif($regStatus === 'rejected')
                                                        ✗ Rejected
                                                    @else
                                                        ⏳ Pending
                                                    @endif
                                                </span>
                                            </div>
                                            <form id="leave-event-{{ $event->id }}" method="POST" action="{{ route('events.leave', ['eventId' => $event->id]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-300"
                                                        @click.prevent="$dispatch('confirm-dialog', {
                                                            title: 'Leave this event?',
                                                            message: 'You will be removed from the participant list. You can join again later.',
                                                            confirmLabel: 'Leave Event',
                                                            cancelLabel: 'Cancel',
                                                            tone: 'danger',
                                                            formId: 'leave-event-{{ $event->id }}'
                                                        })">
                                                    Leave Event
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        @if($canJoin)
                                            <form method="POST" action="{{ route('events.join', ['eventId' => $event->id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                                                        style="background: linear-gradient(to right, #008751, #10b981);">
                                                    Join Event
                                                </button>
                                            </form>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm font-semibold">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Event ended
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @else
                                <div class="text-center">
                                    <p class="text-gray-600 mb-4">Please log in to join this event</p>
                                    <a href="{{ route('login') }}" 
                                       class="inline-block px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                                       style="background: linear-gradient(to right, #008751, #10b981);">
                                        Login to Join
                                    </a>
                                </div>
                            @endauth

                            <!-- View Details (modal trigger) -->
                            <div class="mt-4 flex justify-between items-center">
                                @php
                                    $creatorName = $event instanceof \App\Models\Event
                                        ? optional($event->creator)->name
                                        : null;
                                    $modalPayload = [
                                        'id' => (string) ($event->id ?? ''),
                                        'title' => (string) ($event->title ?? ''),
                                        'description' => (string) ($event->description ? strip_tags($event->description) : ''),
                                        'date' => (string) (optional($event->date)->format('M j, Y') ?? ''),
                                        'time' => (string) ($event->time ?? ''),
                                        'location' => (string) ($event->location ?? ''),
                                        'image' => (string) ($event->photo_url ?? ''),
                                        'status' => (string) ($statusLabel ?? 'Upcoming'),
                                        'current_volunteers' => (int) ($currentVolunteers ?? 0),
                                        'max_volunteers' => $maxVolunteers !== null ? (int) $maxVolunteers : null,
                                        'organizer' => (string) (($event->organizer ?? '') ?: ($creatorName ?? 'Organizer')),
                                        'venue' => (string) (($event->venue ?? '') ?: ($event->location ?? '')),
                                        'requirements' => (string) ($event->requirements ?? ''),
                                        'join_url' => (string) route('events.join', ['eventId' => $event->id]),
                                    ];
                                @endphp
                                <button
                                    type="button"
                                    class="inline-flex items-center text-base font-semibold text-slate-800 hover:text-emerald-700 transition"
                                    @click='open(@json($modalPayload))'
                                >
                                    View Details →
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Events Available</h3>
                    <p class="text-gray-600 mb-6">There are no upcoming events at the moment. Check back later!</p>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('events.create') }}" 
                               class="inline-block px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                               style="background: linear-gradient(to right, #008751, #10b981);">
                                Create First Event
                            </a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>

        <!-- Modal Popup -->
        <div
            x-show="openModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm"
            @click.self="close()"
        >
            <div
                x-show="openModal"
                x-transition.scale
                class="relative w-full max-w-2xl mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-400">Event</p>
                        <h3 class="text-sm font-semibold text-slate-900" x-text="(event && event.title) ? event.title : 'Event details'"></h3>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100"
                        @click="close()"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto">
                    <template x-if="event">
                        <div>
                            <div class="h-48 w-full bg-slate-100 overflow-hidden">
                                <template x-if="event.image">
                                    <img :src="event.image" :alt="event.title" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!event.image">
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-emerald-500 to-sky-500 text-white">
                                        <span class="text-2xl font-semibold" x-text="event.title"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="px-6 py-5 space-y-4 text-sm text-slate-700">
                                <p class="text-slate-600" x-text="event.description || 'Details will be announced soon.'"></p>

                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full font-semibold"
                                          :class="((event && event.status) === 'Completed')
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                            : ((event && event.status) === 'Ongoing')
                                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200'
                                                : ((event && event.status) === 'Ended')
                                                    ? 'bg-slate-50 text-slate-700 border border-slate-200'
                                                    : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                        <span x-text="(event && event.status) ? event.status : 'Upcoming'"></span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-slate-900">Date &amp; Time</p>
                                            <p class="text-slate-600">
                                                <span x-text="event.date || 'Date TBA'"></span>
                                                <template x-if="event.time">
                                                    <span> • <span x-text="event.time"></span></span>
                                                </template>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-slate-900">Location</p>
                                            <p class="text-slate-600" x-text="event.location || 'Location TBA'"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-slate-900">Venue</p>
                                            <p class="text-slate-600" x-text="event.venue || 'Venue TBA'"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 17v-2a4 4 0 118 0v2m-6 4h4a2 2 0 002-2v-2a6 6 0 10-12 0v2a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-slate-900">Organizer</p>
                                            <p class="text-slate-600" x-text="event.organizer || 'Organizer'"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 17a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4v10z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium text-slate-900">Volunteers</p>
                                            <p class="text-slate-600">
                                                <span x-text="event.current_volunteers || 0"></span>
                                                <template x-if="event.max_volunteers">
                                                    <span> / <span x-text="event.max_volunteers"></span></span>
                                                </template>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-amber-100 bg-amber-50/70 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Requirements</p>
                                    <p class="mt-1 text-sm text-slate-700" x-text="event.requirements || 'No special requirements listed.'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <button
                        type="button"
                        class="text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-800"
                        @click="close()"
                    >
                        Close
                    </button>

                    <template x-if="event && (event.status !== 'Ended' && event.status !== 'Completed')">
                        <form method="POST" :action="event.join_url">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
                            >
                                Join Event
                            </button>
                        </form>
                    </template>
                    <template x-if="event && (event.status === 'Ended' || event.status === 'Completed')">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs sm:text-sm font-semibold">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Joining closed
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* moved to resources/css/app.css */
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('event-search');
            const categorySelect = document.getElementById('event-category-filter');
            const dateSelect = document.getElementById('event-date-filter');
            const cards = document.querySelectorAll('[data-event-card]');

            function inDateRange(rawDate, range) {
                if (!rawDate) return range === 'all';
                if (range === 'all') return true;

                const eventDate = new Date(rawDate + 'T00:00:00');
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                if (range === 'upcoming') {
                    return eventDate >= today;
                }

                if (range === 'this_week') {
                    const day = today.getDay();
                    const mondayOffset = day === 0 ? -6 : 1 - day;
                    const start = new Date(today);
                    start.setDate(today.getDate() + mondayOffset);
                    const end = new Date(start);
                    end.setDate(start.getDate() + 6);
                    return eventDate >= start && eventDate <= end;
                }

                if (range === 'this_month') {
                    return eventDate.getFullYear() === today.getFullYear()
                        && eventDate.getMonth() === today.getMonth();
                }

                return true;
            }

            function applyFilters() {
                const term = (searchInput?.value || '').toLowerCase().trim();
                const category = categorySelect?.value || 'all';
                const dateRange = dateSelect?.value || 'all';

                cards.forEach((card) => {
                    const haystack = [
                        card.dataset.eventTitle || '',
                        card.dataset.eventDescription || '',
                        card.dataset.eventLocation || ''
                    ].join(' ');
                    const cardCategory = (card.dataset.eventCategory || '').toLowerCase();
                    const cardDate = card.dataset.eventDate || '';

                    const matchText = term === '' || haystack.includes(term);
                    const matchCategory = category === 'all' || cardCategory === category;
                    const matchDate = inDateRange(cardDate, dateRange);

                    card.style.display = (matchText && matchCategory && matchDate) ? '' : 'none';
                });
            }

            searchInput?.addEventListener('input', applyFilters);
            categorySelect?.addEventListener('change', applyFilters);
            dateSelect?.addEventListener('change', applyFilters);
        });
    </script>
</x-app-layout>
