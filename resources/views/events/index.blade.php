<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upcoming Events') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <div class="py-12 bg-gradient-to-r from-emerald-500 via-emerald-600 to-indigo-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Join Our Events</h1>
                <p class="text-lg text-emerald-50">Make a difference in your community by participating in our volunteer events</p>
            </div>
            <div class="mt-6 text-center flex items-center justify-center gap-4">
                <a href="{{ route('events.calendar') }}" 
                   class="inline-flex items-center px-6 py-3 border border-white/30 rounded-xl font-semibold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition ease-in-out duration-150 shadow-lg bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    View Calendar
                </a>
                @auth
                    @if(auth()->user()->isAdminOrSuperAdmin())
                        <a href="{{ route('events.create') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition ease-in-out duration-150 shadow-lg bg-white hover:bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Event
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg border-2 px-4 py-3 text-sm font-medium" style="background-color: #d4edda; border-color: #1a5f3f; color: #1a5f3f;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($events as $event)
                @php /** @var \stdClass|\App\Models\Event $event */ @endphp
                @php $isEven = ($loop->iteration % 2) === 0; @endphp
                <div class="event-card bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden cursor-pointer hover:shadow-lg transition-all">
                    <div class="md:flex">
                        <!-- Event Image/Icon -->
                        <div class="event-image-container md:w-1/3 h-36 flex items-center justify-center p-4 border-b-2 md:border-b-0 md:border-r-2 border-slate-200 relative overflow-hidden transition-all duration-300"
                             style="background: linear-gradient(135deg, {{ $isEven ? '#10b981' : '#6366f1' }}, {{ $isEven ? '#059669' : '#4f46e5' }});">
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
                                <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300 bg-emerald-50">
                                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-slate-700 font-medium">{{ e($event->location ?: 'Location TBA') }}</span>
                                </div>
                                <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300 bg-indigo-50">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-700 font-medium">{{ e($event->time ?: 'Time TBA') }}</span>
                                </div>
                                @php
                                    // Only attempt to read creator when this is an Eloquent Event model
                                    $creatorName = $event instanceof \App\Models\Event
                                        ? optional($event->creator)->name
                                        : null;
                                @endphp
                                @if($creatorName)
                                    <div class="flex items-center px-3 py-2 rounded-lg transition-colors duration-300" style="background-color: rgba(26, 95, 63, 0.1);">
                                        <svg class="w-5 h-5 mr-2" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            $statusStyle = 'background: linear-gradient(to right, #1a5f3f, #2d7a5a); color: white;';
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
                                                    class="px-6 py-2 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                                                    style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                                                Join Event
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <div class="text-center">
                                    <p class="text-gray-600 mb-4">Please log in to join this event</p>
                                    <a href="{{ route('login') }}" 
                                       class="inline-block px-6 py-2 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                                       style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                                        Login to Join
                                    </a>
                                </div>
                            @endauth
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
                               class="inline-block px-6 py-2 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                               style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                                Create First Event
                            </a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>
    </div>

    <style>
        /* Hover animations for event cards */
        .event-card {
            transition: all 0.3s ease-in-out;
        }
        .event-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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
</x-app-layout>
