<x-app-layout>
    @php
        $eventDateLabel = ($event->date instanceof \Carbon\Carbon) ? $event->date->format('F j, Y') : 'Date TBA';
        $eventShowId = !empty($event->id) ? (string) $event->id : (string) request()->route('eventId', '');
    @endphp
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
                                    <span class="text-xs sm:text-sm font-semibold text-slate-700 whitespace-nowrap">{{ $eventDateLabel }}</span>
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
                            @if(auth()->user()->isAdminOrSuperAdmin() && $eventShowId !== '')
                                <div class="flex items-center gap-2 sm:pt-1">
                                    <a href="{{ route('events.edit', ['eventId' => $eventShowId]) }}" 
                                       class="border border-blue-600 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-50 transition duration-300 text-sm font-semibold">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('events.destroy', ['eventId' => $eventShowId]) }}" 
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
                            {{ !empty($event->description) ? $event->description : 'Details will be announced soon.' }}
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ $eventDateLabel }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ !empty($event->time) ? $event->time : 'Time TBA' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium">{{ !empty($event->location) ? $event->location : 'Location TBA' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Venue:</span>
                                    <span class="font-medium">{{ !empty($event->venue) ? $event->venue : 'Not specified' }}</span>
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
                                    @if($eventShowId !== '')
                                        <a href="{{ route('events.show', ['eventId' => $eventShowId, 'show_volunteers' => 1]) }}#joined-volunteers"
                                           class="font-medium text-blue-700 hover:text-blue-800 hover:underline cursor-pointer">
                                            {{ (int) ($event->approved_registrations_count ?? 0) }}
                                            @if(!empty($event->max_participants))
                                                / {{ (int) $event->max_participants }}
                                            @endif
                                        </a>
                                    @else
                                        <span class="font-medium text-slate-700">
                                            {{ (int) ($event->approved_registrations_count ?? 0) }}
                                            @if(!empty($event->max_participants))
                                                / {{ (int) $event->max_participants }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $avg = is_array($feedbackSummary ?? null) ? ($feedbackSummary['average'] ?? null) : null;
                                $cnt = is_array($feedbackSummary ?? null) ? (int) ($feedbackSummary['count'] ?? 0) : 0;
                            @endphp
                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Event Rating</h4>
                                <div class="flex items-center gap-2">
                                    @if($avg !== null)
                                        <span class="text-sm font-semibold text-slate-900">{{ number_format((float) $avg, 1) }}/5</span>
                                        <span class="text-xs text-slate-500">({{ $cnt }} rating{{ $cnt === 1 ? '' : 's' }})</span>
                                    @else
                                        <span class="text-sm text-slate-600">No ratings yet.</span>
                                    @endif
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
                                        
                                        @if($eventShowId !== '')
                                        <form id="leave-event-{{ $eventShowId }}" method="POST" action="{{ route('events.leave', ['eventId' => $eventShowId]) }}" class="inline">
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
                                                        formId: 'leave-event-{{ $eventShowId }}'
                                                    })">
                                                Leave Event
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center">
                                        @if($canJoin)
                                            <p class="text-gray-600 mb-4">You haven't registered for this event yet.</p>
                                            @if($eventShowId !== '')
                                            <form method="POST" action="{{ route('events.join', ['eventId' => $eventShowId]) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                                    Join Event
                                                </button>
                                            </form>
                                            @else
                                                <p class="text-sm text-amber-700">Event ID missing. Refresh and try again.</p>
                                            @endif
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

                    @auth
                        @if(auth()->user()->isVolunteer() && !empty($eventShowId))
                            @php
                                $userRating = is_array($currentUserFeedback ?? null) ? (int) ($currentUserFeedback['rating'] ?? 0) : 0;
                                $userComment = is_array($currentUserFeedback ?? null) ? (string) ($currentUserFeedback['comment'] ?? '') : '';
                                $rawStatusFeedback = strtolower((string) ($event->event_status ?? ''));
                                $eventIsPastFeedback = ($event->date instanceof \Carbon\Carbon) ? $event->date->isPast() && !$event->date->isToday() : false;
                                $canRate = $rawStatusFeedback === 'completed' || $eventIsPastFeedback;
                            @endphp
                            <div class="mb-8 bg-white border border-slate-200 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Leave Feedback</h3>
                                <p class="text-sm text-gray-600 mb-4">Rate your experience for this event (1–5 stars).</p>

                                @if(!$canRate)
                                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                        Feedback will be available after the event ends.
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('events.feedback.submit', ['eventId' => $eventShowId]) }}" class="space-y-4">
                                        @csrf

                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 mb-2">Your rating</p>
                                            <div class="flex items-center gap-1" role="radiogroup" aria-label="Event rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only" @checked((int) old('rating', $userRating) === $i)>
                                                        <svg class="h-7 w-7 transition"
                                                             viewBox="0 0 20 20"
                                                             fill="{{ (int) old('rating', $userRating) >= $i ? '#F59E0B' : 'none' }}"
                                                             stroke="{{ (int) old('rating', $userRating) >= $i ? '#F59E0B' : '#94A3B8' }}"
                                                             stroke-width="1.5">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.158c.969 0 1.371 1.24.588 1.81l-3.364 2.445a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.54 1.118l-3.364-2.445a1 1 0 00-1.176 0l-3.364 2.445c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L1.07 9.382c-.783-.57-.38-1.81.588-1.81h4.158a1 1 0 00.95-.69l1.286-3.955z"/>
                                                        </svg>
                                                    </label>
                                                @endfor
                                            </div>
                                            @error('rating')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="comment" class="block text-sm font-semibold text-slate-800 mb-2">Comment (optional)</label>
                                            <textarea id="comment" name="comment" rows="3" maxlength="500"
                                                      class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                      placeholder="Share what went well or what can improve...">{{ old('comment', $userComment) }}</textarea>
                                            @error('comment')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-slate-500">You can update your rating later.</p>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                                                Submit
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endif
                        @if(auth()->user()->isAdminOrSuperAdmin() && !empty($checkInUrl))
                            <div class="mb-8 bg-white border border-emerald-200 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">QR Check-In</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Let volunteers scan this code at the event. Their attendance is marked automatically after validation.
                                </p>
                                <div class="flex flex-col md:flex-row md:items-start gap-4">
                                    <div class="w-[260px] h-[260px] border border-gray-200 rounded-lg p-2 bg-white">
                                        @if(!empty($checkInQrSvg))
                                            {!! $checkInQrSvg !!}
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-500 text-center px-4">
                                                QR preview unavailable. Use the check-in URL.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Check-in URL</p>
                                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3 break-all text-sm text-gray-700">{{ $checkInUrl }}</div>
                                        <div class="mt-3">
                                            <a href="{{ $checkInUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                                Open check-in page
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth

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
