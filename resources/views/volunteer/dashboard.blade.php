<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain" />
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Volunteer Dashboard</h2>
            </div>
            <div class="flex items-center space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="hidden sm:flex flex-col items-start mr-1">
                                <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-600">Volunteer</span>
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

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="mb-8 rounded-2xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-indigo-600 text-white p-8 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                        <p class="text-emerald-50 text-base">Here's an overview of your volunteer activities and upcoming events.</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Header Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="rounded-2xl p-6 border border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100/50 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Participation Growth</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-bold text-indigo-700">{{ $analytics['growth_rate_pct'] }}%</p>
                        <span class="inline-flex items-center text-emerald-600 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l4-4 4 4m0 0l4-4m-4 4v10"/></svg>
                            QoQ
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">My participation growth this period</p>
                </div>
                <div class="rounded-2xl p-6 border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100/50 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Participation Rate</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-bold text-emerald-700">{{ $analytics['participation_rate'] }}%</p>
                        <span class="text-sm text-slate-500">of events</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Based on my approved registrations</p>
                </div>
                <div class="rounded-2xl p-6 border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100/50 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Approval Rate</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-bold text-slate-800">{{ $analytics['event_success_pct'] }}%</p>
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">My approved registrations</p>
                </div>
            </div>

            <!-- Top Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Registrations -->
                <a href="{{ route('events.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-sm border border-indigo-200 bg-white text-gray-900 hover:shadow-lg hover:border-indigo-300 transition-all">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-400 via-indigo-500 to-emerald-500"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-slate-600 font-medium">My Registrations</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['total_registrations'] }}</p>
                            <p class="mt-2 text-xs text-slate-500">All time</p>
                        </div>
                        <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Approved Events -->
                <a href="{{ route('events.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-sm border border-emerald-200 bg-white text-gray-900 hover:shadow-lg hover:border-emerald-300 transition-all">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-indigo-500"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-slate-600 font-medium">Approved Events</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['approved_registrations'] }}</p>
                            <p class="mt-2 text-xs text-slate-500">Confirmed participation</p>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Pending Applications -->
                <a href="{{ route('events.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-sm border border-amber-200 bg-white text-gray-900 hover:shadow-lg hover:border-amber-300 transition-all">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-slate-600 font-medium">Pending</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['pending_registrations'] }}</p>
                            <p class="mt-2 text-xs text-slate-500">Awaiting review</p>
                        </div>
                        <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Upcoming Events -->
                <a href="{{ route('events.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow-sm border border-slate-200 bg-white text-gray-900 hover:shadow-lg hover:border-slate-300 transition-all">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-slate-400 via-slate-500 to-slate-600"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-slate-600 font-medium">Upcoming Events</p>
                            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['upcoming_registered_events'] }}</p>
                            <p class="mt-2 text-xs text-slate-500">I'm registered for</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-100 text-slate-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Snapshot Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-600 font-medium">Upcoming Events</p>
                            <p class="text-xl font-bold text-slate-900">{{ $quickStats['upcoming_events'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-600 font-medium">Registrations Today</p>
                            <p class="text-xl font-bold text-slate-900">{{ $quickStats['registrations_today'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-600 font-medium">Events This Month</p>
                            <p class="text-xl font-bold text-slate-900">{{ $quickStats['events_this_month'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-slate-100 text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-slate-600 font-medium">My Registrations (Month)</p>
                            <p class="text-xl font-bold text-slate-900">{{ $quickStats['my_registrations_this_month'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('events.index') }}" 
                       class="flex items-center p-4 bg-emerald-50 rounded-xl border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-all duration-200">
                        <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-emerald-700 font-semibold">Browse Events</span>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center p-4 bg-indigo-50 rounded-xl border border-indigo-200 hover:bg-indigo-100 hover:border-indigo-300 transition-all duration-200">
                        <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-7.414a2 2 0 112.828 2.828L12 20l-4 1 1-4 8.586-8.586z"/>
                        </svg>
                        <span class="text-indigo-700 font-semibold">Update Profile</span>
                    </a>
                    
                    <a href="{{ route('members.index') }}" 
                       class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100 hover:border-slate-300 transition-all duration-200">
                        <svg class="w-6 h-6 text-slate-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-slate-700 font-semibold">View Members</span>
                    </a>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- My Upcoming Events -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900">My Upcoming Events</h3>
                        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($upcomingRegisteredEvents as $event)
                            <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $event->date->format('M j, Y') }} at {{ $event->time }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->location }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Approved
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No upcoming registered events</p>
                                <a href="{{ route('events.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-700">
                                    Browse available events →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Available Events to Join -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900">Available Events</h3>
                        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($availableEvents as $event)
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $event->date->format('M j, Y') }} at {{ $event->time }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->location }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('events.show', ['eventId' => $event->id]) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                        Join
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No available events at the moment</p>
                                <p class="mt-1 text-xs text-gray-400">Check back later for new opportunities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- My Recent Registrations -->
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900">My Recent Registrations</h3>
                    <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">View all events</a>
                </div>
                <div class="space-y-4">
                    @forelse($stats['recent_registrations'] as $registration)
                        @php
                            $eventTitle = isset($registration->event) && is_object($registration->event)
                                ? ($registration->event->title ?? 'Unknown event')
                                : 'Unknown event';
                            $createdAt = $registration->created_at ?? null;
                            $status = strtolower($registration->status ?? 'pending');
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $eventTitle }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    @if($createdAt)
                                        Registered on {{ $createdAt->format('M j, Y g:i A') }}
                                    @else
                                        Registration date unavailable
                                    @endif
                                </p>
                            </div>
                            <div class="ml-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($status === 'approved') bg-emerald-100 text-emerald-800
                                    @elseif($status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No registrations yet</p>
                            <a href="{{ route('events.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-700">
                                Start volunteering →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Simple Participation Trend Summary -->
            @php
                $trend = $analytics['trend'] ?? [];
                $trendValues = array_values($trend);
                $hasTrendData = array_sum($trendValues) > 0;
            @endphp
            @if($hasTrendData)
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">My Participation Trend</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Summary of my approved registrations over recent periods.
                </p>
                <div class="space-y-2">
                    @foreach($trend as $label => $value)
                        @if($value > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ $label }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                {{ $value }} approved
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
