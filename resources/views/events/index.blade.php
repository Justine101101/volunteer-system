<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(!empty($showVolunteerEventSections))
                    {{ __('Events') }}
                @else
                    {{ __('Upcoming Events') }}
                @endif
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
    <div class="py-20 bg-light-gray" data-animate="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-left">
                @if(!empty($showVolunteerEventSections))
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-dark mb-3">Volunteer events</h1>
                    <p class="text-lg text-gray-600 leading-relaxed max-w-3xl">
                        <a href="#my-events" class="font-semibold text-emerald-700 hover:text-emerald-800">My events</a>
                        lists programs you have joined or applied for.
                        <a href="#browse-events" class="font-semibold text-emerald-700 hover:text-emerald-800">Browse events</a>
                        shows programs you can still join. Search and filters apply to both lists.
                    </p>
                @else
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-dark mb-3">Upcoming Events</h1>
                    <p class="text-lg text-gray-600 leading-relaxed max-w-3xl">Discover opportunities to make a difference in your community through our volunteer events.</p>
                @endif
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
    <div class="py-20 bg-white" data-animate="slide-right">
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

            @if(!empty($showVolunteerEventSections))
                <section id="my-events" class="mb-16 scroll-mt-28">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">My events</h2>
                        <p class="mt-1 text-sm text-slate-600 max-w-3xl">Events you have joined or requested to join (pending, approved, or rejected). Use Leave event to withdraw when allowed.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6" data-animate="fade-up">
                        @forelse($volunteerMyEvents as $event)
                            @include('events.partials.event-card', [
                                'event' => $event,
                                'userRegistrationsByEvent' => $userRegistrationsByEvent,
                                'cardIndex' => $loop->index,
                            ])
                        @empty
                            <div class="col-span-full rounded-2xl border border-slate-200 bg-slate-50 px-6 py-12 text-center">
                                <p class="text-slate-800 font-medium">You are not signed up for any events yet.</p>
                                <p class="mt-2 text-sm text-slate-600">Open <a href="#browse-events" class="font-semibold text-emerald-700 hover:text-emerald-800">Browse events</a> below and tap Join on an event.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section id="browse-events" class="scroll-mt-28">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">Browse events</h2>
                        <p class="mt-1 text-sm text-slate-600 max-w-3xl">Programs you are not registered for yet. Join to move one into My events.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6" data-animate="fade-up">
                        @php $browseOffset = count($volunteerMyEvents); @endphp
                        @forelse($volunteerBrowseEvents as $event)
                            @include('events.partials.event-card', [
                                'event' => $event,
                                'userRegistrationsByEvent' => $userRegistrationsByEvent,
                                'cardIndex' => $browseOffset + $loop->index,
                            ])
                        @empty
                            <div class="col-span-full rounded-2xl border border-emerald-100 bg-emerald-50/60 px-6 py-12 text-center">
                                <p class="text-slate-800 font-medium">You have joined every published event, or there are no other events to show.</p>
                                <p class="mt-2 text-sm text-slate-600">See <a href="#my-events" class="font-semibold text-emerald-700 hover:text-emerald-800">My events</a> above for your registrations.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @else
            <div id="events-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6" data-animate="fade-up">
            @forelse($events as $event)
                @include('events.partials.event-card', [
                    'event' => $event,
                    'userRegistrationsByEvent' => $userRegistrationsByEvent,
                    'cardIndex' => $loop->index,
                ])
            @empty
                <div class="text-center py-12 col-span-full">
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
            @endif

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

                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Joined Volunteers</p>
                                    <template x-if="event.approved_volunteers && event.approved_volunteers.length > 0">
                                        <div class="mt-2 max-h-40 overflow-y-auto space-y-2">
                                            <template x-for="(volunteer, idx) in event.approved_volunteers" :key="idx">
                                                <div class="rounded-md border border-slate-200 bg-slate-50 px-2 py-1.5">
                                                    <p class="text-sm font-medium text-slate-900" x-text="volunteer.name || 'Volunteer'"></p>
                                                    <p class="text-xs text-slate-600" x-text="volunteer.email || ''"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!event.approved_volunteers || event.approved_volunteers.length === 0">
                                        <p class="mt-2 text-sm text-slate-600">No approved volunteers yet.</p>
                                    </template>
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
