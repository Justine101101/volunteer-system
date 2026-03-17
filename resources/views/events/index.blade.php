<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upcoming Events') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <div class="py-20 bg-light-gray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">Upcoming Events</h1>
                <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">Discover opportunities to make a difference in your community through our volunteer events</p>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="eventsModal()">
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

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
                    $statusClasses = match ($statusLabel) {
                        'Ongoing' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                        'Ended' => 'bg-gray-50 text-gray-700 border border-gray-200',
                        'Completed' => 'bg-lions-green/10 text-lions-green border border-lions-green',
                        default => 'bg-amber-50 text-amber-700 border border-amber-200', // Upcoming
                    };
                @endphp
                <div class="event-card bg-white rounded-3xl shadow-soft-lg border border-gray-200 overflow-hidden cursor-pointer hover:shadow-soft-xl transition-all duration-300">
                    <div class="md:flex">
                        <!-- Event Image/Icon -->
                        <div class="event-image-container md:w-1/3 h-36 flex items-center justify-center p-4 border-b-2 md:border-b-0 md:border-r-2 border-gray-200 relative overflow-hidden transition-all duration-300"
                             style="background: linear-gradient(135deg, {{ $isEven ? '#f8faf9' : '#f3f4f6' }}, {{ $isEven ? '#e0e7ff' : '#ddd6fe' }});">
                            <span class="absolute top-3 left-3 inline-flex items-center h-7 px-3 rounded-full text-xs font-semibold {{ $statusClasses }} bg-white/90 backdrop-blur">
                                {{ $statusLabel }}
                            </span>
                            @if($event->photo_url)
                                <img src="{{ $event->photo_url }}" 
                                     alt="{{ $event->title }}" 
                                     class="event-image w-full h-full object-cover absolute inset-0 transition-transform duration-300"
                                     onerror="this.style.display='none';">
                                <div class="absolute inset-0 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.4);">
                                    <div class="text-center text-white">
                                        <div class="text-6xl font-bold mb-2 drop-shadow-lg event-date-day">
                                            {{ optional($event->date)->format('d') ?? '—' }}
                                        </div>
                                        <div class="text-lg font-semibold drop-shadow-lg">
                                            {{ optional($event->date)->format('M Y') ?? '' }}
                                        </div>
                                        <div class="text-sm drop-shadow-lg">
                                            {{ $event->time ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-white">
                                    <div class="text-6xl font-bold mb-2 event-date-day">
                                        {{ optional($event->date)->format('d') ?? '—' }}
                                    </div>
                                    <div class="text-lg font-semibold">
                                        {{ optional($event->date)->format('M Y') ?? '' }}
                                    </div>
                                    <div class="text-sm" style="color: #90EE90;">
                                        {{ $event->time ?? '' }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Event Details -->
                        <div class="md:w-2/3 p-4">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="event-title text-2xl font-bold transition-colors duration-300"
                                    style="color: {{ $isEven ? '#1a5f3f' : '#4a1a5f' }};">
                                    {{ e($event->title ?: 'Untitled event') }}
                                </h3>
                                @auth
                                    {{-- Show edit/delete for admin/superadmin if event has an ID (Supabase UUID) --}}
                                    @if(auth()->user()->isAdminOrSuperAdmin() && isset($event->id) && $event->id)
                                        <div class="flex space-x-2">
                                            <a href="{{ route('events.edit', ['eventId' => $event->id]) }}" 
                                               class="text-sm font-medium transition-colors duration-300 hover:underline" 
                                               style="color: #1a5f3f;">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('events.destroy', ['eventId' => $event->id]) }}" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-300 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            <p class="text-gray-700 mb-4 leading-relaxed">
                                {{ $event->description ? strip_tags($event->description) : 'Details will be announced soon.' }}
                            </p>

                            <div class="flex flex-wrap gap-4 mb-6">
                                <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300 bg-lions-green/10">
                                    <svg class="w-5 h-5 mr-2 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-gray-700 font-medium">{{ e($event->location ?: 'Location TBA') }}</span>
                                </div>
                                <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300 bg-indigo-50">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-700 font-medium">{{ e($event->time ?: 'Time TBA') }}</span>
                                </div>
                                @php
                                    // Only attempt to read creator when this is an Eloquent Event model
                                    $creatorName = $event instanceof \App\Models\Event
                                        ? optional($event->creator)->name
                                        : null;
                                @endphp
                                @if($creatorName)
                                    <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300 bg-lions-green/5">
                                        <svg class="w-5 h-5 mr-2" style="color: #008751;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="text-gray-700 font-medium">
                                            Created by {{ e($creatorName) }}
                                        </span>
                                    </div>
                                @endif
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
                                            <form method="POST" action="{{ route('events.leave', ['eventId' => $event->id]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-300"
                                                        onclick="return confirm('Are you sure you want to leave this event?')">
                                                    Leave Event
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('events.join', ['eventId' => $event->id]) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                                                    style="background: linear-gradient(to right, #008751, #10b981);">
                                                Join Event
                                            </button>
                                        </form>
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
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition"
                                    @click="open({
                                        id: '{{ $event->id }}',
                                        title: @js($event->title ?? ''),
                                        description: @js($event->description ? strip_tags($event->description) : ''),
                                        date: '{{ optional($event->date)->format('M j, Y') ?? '' }}',
                                        time: '{{ $event->time ?? '' }}',
                                        location: @js($event->location ?? ''),
                                        image: '{{ $event->photo_url ?? '' }}',
                                        status: '{{ $statusLabel }}',
                                        current_volunteers: {{ $currentVolunteers }},
                                        max_volunteers: {{ $maxVolunteers ?? 'null' }},
                                        organizer: @js($creatorName ?? 'Organizer'),
                                        join_url: '{{ route('events.join', ['eventId' => $event->id]) }}'
                                    })"
                                >
                                    View Details
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
                        @if(auth()->user()->isSuperAdmin())
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
                        <h3 class="text-sm font-semibold text-slate-900" x-text="event?.title ?? 'Event details'"></h3>
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
                                          :class="(event?.status === 'Completed')
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                            : (event?.status === 'Ongoing')
                                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200'
                                                : (event?.status === 'Ended')
                                                    ? 'bg-slate-50 text-slate-700 border border-slate-200'
                                                    : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                        <span x-text="event?.status || 'Upcoming'"></span>
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

                    <template x-if="event">
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
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hover animations for event cards */
        .event-card {
            transition: all 0.3s ease-in-out;
        }
        .event-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        .event-card:hover .event-image-container {
            transform: scale(1.05);
        }
        .event-card:hover .event-image {
            transform: scale(1.1);
        }
        .event-card:hover .event-date-day {
            transform: scale(1.1);
            transition: transform 0.3s ease-in-out;
        }
        .event-card:hover .event-title {
            transform: translateX(4px);
            transition: transform 0.3s ease-in-out;
        }
    </style>

    <script>
        function eventsModal() {
            return {
                openModal: false,
                event: null,
                open(payload) {
                    this.event = payload;
                    this.openModal = true;
                },
                close() {
                    this.openModal = false;
                    this.event = null;
                }
            }
        }
    </script>
</x-app-layout>
