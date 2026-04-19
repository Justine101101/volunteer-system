<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->title }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('events.index') }}"
                   class="text-gray-600 hover:text-gray-900">
                    ← Back to Events
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex flex-col items-start mr-2 hidden sm:flex">
                                    <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                    <span class="text-xs text-gray-600">{{ auth()->user()->isAdminOrSuperAdmin() ? 'Admin' : 'Volunteer' }}</span>
                                </div>
                                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
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
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Event Header -->
                <div class="bg-white border-b border-gray-200 p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                        <div class="min-w-0">
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight line-clamp-2">{{ $event->title }}</h1>
                            <div class="mt-3 flex flex-wrap gap-2 text-gray-600">
                                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-700 whitespace-nowrap">{{ $event->date->format('F j, Y') }}</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 border border-indigo-100">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-700 whitespace-nowrap">{{ $event->time }}</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-100 max-w-full">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-xs sm:text-sm font-semibold text-slate-700 line-clamp-1">{{ $event->location }}</span>
                                </div>
                            </div>
                        </div>
                        @auth
                            @if(auth()->user()->isAdminOrSuperAdmin() && isset($event->id) && $event->id)
                                <div class="flex items-center gap-2 sm:pt-1">
                                    <a href="{{ route('events.edit', $event->id) }}" 
                                       class="border border-blue-600 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-50 transition duration-300 text-sm font-semibold">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('events.destroy', $event->id) }}" 
                                          class="inline" 
                                          data-confirm="Delete this event? This action cannot be undone.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition duration-300 text-sm font-semibold">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Event Photo -->
                @if($event->photo_url)
                    <div class="w-full h-96 bg-gray-100 overflow-hidden">
                        <img src="{{ $event->photo_url }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.parentElement.style.display='none';">
                    </div>
                @endif

                <!-- Event Content -->
                <div class="p-8">
                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Event Description</h2>
                        <div class="prose max-w-none text-gray-600">
                            {{ $event->description }}
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ $event->date->format('F j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ $event->time }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium">{{ $event->location }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Venue:</span>
                                    <span class="font-medium">{{ $event->venue ?: 'Not specified' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Organizer:</span>
                                    <span class="font-medium">{{ $event->organizer ?: ($event->creator?->name ?? 'Organizer') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Created by:</span>
                                    <span class="font-medium">{{ $event->creator?->name ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Volunteers:</span>
                                    <a href="{{ route('events.show', ['eventId' => $event->id, 'show_volunteers' => 1]) }}#joined-volunteers"
                                       class="font-medium text-blue-700 hover:text-blue-800 hover:underline cursor-pointer">
                                        {{ (int) ($event->approved_registrations_count ?? 0) }}
                                        @if(!empty($event->max_participants))
                                            / {{ (int) $event->max_participants }}
                                        @endif
                                    </a>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Joined Volunteers</h4>
                                @if(!empty($event->approved_volunteers) && count($event->approved_volunteers) > 0)
                                    <div class="max-h-56 overflow-y-auto space-y-2">
                                        @foreach($event->approved_volunteers as $volunteer)
                                            <div class="rounded-md border border-gray-200 bg-white px-3 py-2">
                                                <p class="text-sm font-medium text-gray-900">{{ $volunteer['name'] ?? 'Volunteer' }}</p>
                                                @if(!empty($volunteer['email']))
                                                    <p class="text-xs text-gray-600">{{ $volunteer['email'] }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600">No approved volunteers yet.</p>
                                @endif
                            </div>

                            <div id="joined-volunteers"></div>
                        </div>

                        <!-- Registration Status -->
                        @auth
                            @php
                                // Supabase uses UUIDs for user_id; the controller passes the resolved value
                                $userRegistration = (isset($currentUserSupabaseId) && $currentUserSupabaseId && $event->registrations)
                                    ? $event->registrations->firstWhere('user_id', $currentUserSupabaseId)
                                    : null;

                                $rawStatus = strtolower((string) ($event->event_status ?? ''));
                                $eventIsPast = ($event->date instanceof \Carbon\Carbon) ? $event->date->isPast() && !$event->date->isToday() : false;
                                $canJoin = !$eventIsPast && $rawStatus !== 'completed';
                            @endphp
                            
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Registration</h3>
                                
                                @if($userRegistration)
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                                @if($userRegistration->registration_status === 'approved') bg-green-100 text-green-800
                                                @elseif($userRegistration->registration_status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                @if($userRegistration->registration_status === 'approved')
                                                    ✓ Approved
                                                @elseif($userRegistration->registration_status === 'rejected')
                                                    ✗ Rejected
                                                @else
                                                    ⏳ Pending Approval
                                                @endif
                                            </span>
                                        </div>
                                        
                                        <form id="leave-event-{{ $event->id }}" method="POST" action="{{ route('events.leave', ['eventId' => $event->id]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 font-medium"
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
                                    <div class="text-center">
                                        @if($canJoin)
                                            <p class="text-gray-600 mb-4">You haven't registered for this event yet.</p>
                                            <form method="POST" action="{{ route('events.join', ['eventId' => $event->id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                                    Join Event
                                                </button>
                                            </form>
                                        @else
                                            <p class="text-gray-600 mb-4">This event has ended. Registration is closed.</p>
                                            <button type="button" disabled
                                                    class="bg-gray-300 text-gray-600 px-6 py-2 rounded-lg cursor-not-allowed">
                                                Join Closed
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Join This Event</h3>
                                <p class="text-gray-600 mb-4">Please log in to register for this event.</p>
                                <a href="{{ route('login') }}" 
                                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Login to Join
                                </a>
                            </div>
                        @endauth
                    </div>

                    @if(!empty($event->requirements))
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-3">Requirements</h2>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-gray-700">
                                {{ $event->requirements }}
                            </div>
                        </div>
                    @endif

                    <!-- Registered Volunteers (for admins) -->
                    @auth
                        @if(auth()->user()->isAdminOrSuperAdmin() && $event->registrations->count() > 0)
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    Registered Volunteers ({{ $event->registrations_count ?? $event->registrations->count() }})
                                    <span class="text-sm font-medium text-gray-600">
                                        • Approved: {{ $event->approved_registrations_count ?? $event->registrations->where('registration_status', 'approved')->count() }}
                                    </span>
                                </h3>
                                <div class="space-y-3">
                                    @foreach($event->registrations as $registration)
                                        @php
                                            $regUserName = $registration->user?->name ?? 'Volunteer';
                                            $regUserEmail = $registration->user?->email ?? '—';
                                            $regInitial = strtoupper(substr((string) $regUserName, 0, 1));
                                        @endphp
                                        <div class="flex items-center justify-between bg-white p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-blue-600 font-semibold">{{ $regInitial }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $regUserName }}</p>
                                                    <p class="text-sm text-gray-600">{{ $regUserEmail }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($registration->registration_status === 'approved') bg-green-100 text-green-800
                                                    @elseif($registration->registration_status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($registration->registration_status) }}
                                                </span>
                                                
                                                @if($registration->registration_status === 'pending')
                                                    <form method="POST" action="{{ route('supabase.registrations.approve', ['registrationId' => $registration->id]) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('supabase.registrations.reject', ['registrationId' => $registration->id]) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
