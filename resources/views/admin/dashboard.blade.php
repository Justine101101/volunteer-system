<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain" />
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Volunteer Management System</h2>
            </div>
            <div class="flex items-center space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-start mr-2 hidden sm:flex">
                                <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-600">Admin</span>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
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
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
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

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="mb-8 rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                <h1 class="text-2xl font-semibold text-emerald-900">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="mt-1 text-sm text-emerald-800">Here’s a quick overview of what’s happening today.</p>
            </div>
            <!-- Analytics Header Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="rounded-xl p-6 border border-purple-200 bg-purple-100">
                    <p class="text-sm text-gray-600">Growth Rate</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-purple-800">{{ $analytics['growth_rate_pct'] }}%</p>
                        <span class="inline-flex items-center text-emerald-700 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l4-4 4 4m0 0l4-4m-4 4v10"/></svg>
                            QoQ
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Volunteer growth this period</p>
                </div>
                <div class="rounded-xl p-6 border border-emerald-200 bg-emerald-100">
                    <p class="text-sm text-gray-600">Avg. Attendance</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-emerald-800">{{ $analytics['avg_attendance'] }}</p>
                        <span class="text-sm text-gray-500">per event</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Based on approved registrations</p>
                </div>
                <div class="rounded-xl p-6 border border-purple-200 bg-purple-200">
                    <p class="text-sm text-gray-600">Event Success</p>
                    <div class="mt-2 flex items-end justify-between">
                        <p class="text-3xl font-semibold text-purple-800">{{ $analytics['event_success_pct'] }}%</p>
                        <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Events completed successfully</p>
                </div>
            </div>
            <!-- Top Metric Cards (Dark Green + White theme) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
                <!-- Total Volunteers (neutral card with blue accent) -->
                <a href="{{ route('members.index') }}" class="relative overflow-hidden rounded-2xl p-6 border border-emerald-200 bg-emerald-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Total Volunteers</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_volunteers'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Active volunteers</p>
                        </div>
                        <div class="p-3 rounded-full bg-blue-100 text-blue-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Total Clients (neutral card with green accent) -->
                <a href="{{ route('members.index') }}" class="relative overflow-hidden rounded-2xl p-6 shadow border border-gray-200 bg-white text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Total Members</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_members'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Club directory</p>
                        </div>
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8M7 16h6"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Approved Events (neutral card with orange accent) -->
                <a href="{{ route('events.index') }}" class="relative overflow-hidden rounded-2xl p-6 border border-purple-200 bg-purple-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Approved Events</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['approved_registrations'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">With approvals</p>
                        </div>
                        <div class="p-3 rounded-full bg-orange-100 text-orange-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-4A8.994 8.994 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Pending Applications (neutral card with red accent) -->
                <a href="{{ route('admin.attendance') }}" class="relative overflow-hidden rounded-2xl p-6 border border-purple-200 bg-purple-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Pending Applications</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['pending_registrations'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Awaiting review</p>
                        </div>
                        <div class="p-3 rounded-full bg-rose-100 text-rose-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Feedback Count (neutral card with purple accent) -->
                <a href="{{ route('contact') }}" class="relative overflow-hidden rounded-2xl p-6 border border-purple-200 bg-purple-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Feedback</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_contacts'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Messages</p>
                        </div>
                        <div class="p-3 rounded-full bg-violet-100 text-violet-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6-9 6-9-6z"/></svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Snapshot Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 21v-2a6 6 0 0112 0v2"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">New Volunteers (30d)</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $quickStats['new_volunteers_30d'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Upcoming Events</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $quickStats['upcoming_events'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Registrations Today</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $quickStats['registrations_today'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-violet-50 text-violet-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-6 9 6-9 6-9-6z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Contact Messages (7d)</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $quickStats['contacts_week'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Events -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Events</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_events'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Registrations -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Registrations</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_registrations'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Registrations -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_registrations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('events.create') }}" 
                       class="flex items-center p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition duration-300">
                        <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-emerald-800 font-medium">Create New Event</span>
                    </a>
                    
                    <a href="{{ route('events.index') }}" 
                       class="flex items-center p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition duration-300">
                        <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-emerald-800 font-medium">Manage Events</span>
                    </a>
                    
                    <a href="{{ route('members.index') }}" 
                       class="flex items-center p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition duration-300">
                        <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-emerald-800 font-medium">View Members</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Events -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Events</h3>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_events'] as $event)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $event->date->format('M j, Y') }} at {{ $event->time }}</p>
                                </div>
                                <a href="{{ route('events.show', $event) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    View
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No events found</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Registrations</h3>
                        <a href="{{ route('admin.attendance') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_registrations'] as $registration)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $registration->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $registration->event->title }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($registration->status === 'approved') bg-green-100 text-green-800
                                        @elseif($registration->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                    @php 
                                        $status = strtolower(trim($registration->status ?? ''));
                                        $isPending = $status === 'pending';
                                    @endphp
                                    <form method="POST" action="{{ route('registrations.approve', $registration) }}" onsubmit="return confirm('Approve this registration?')">
                                        @csrf
                                        @method('PATCH')
                                        <button {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                                            class="px-3 py-1.5 text-xs rounded-md text-white font-medium focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                                            style="{{ $isPending ? 'background-color:#059669;border:none;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                                            title="{{ $isPending ? 'Approve registration' : 'Already processed' }}">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('registrations.reject', $registration) }}" onsubmit="return confirm('Decline this registration?')">
                                        @csrf
                                        @method('PATCH')
                                        <button {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                                            class="px-3 py-1.5 text-xs rounded-md text-white font-medium focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                                            style="{{ $isPending ? 'background-color:#E11D48;border:none;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                                            title="{{ $isPending ? 'Decline registration' : 'Already processed' }}">Decline</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No registrations found</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Contact Messages -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Contact Messages</h3>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Open contact page</a>
                </div>
                <div class="space-y-4">
                    @forelse($stats['recent_contacts'] as $contact)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900">{{ $contact->name }}</h4>
                                <span class="text-sm text-gray-500">{{ $contact->created_at->format('M j, Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ Str::limit($contact->message, 100) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $contact->email }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No contact messages found</p>
                    @endforelse
                </div>
            </div>

            <!-- Charts / Lists Row -->
            @php
                $hasRoleData = !empty($roleBreakdown);
                $trendValues = array_values($analytics['trend'] ?? []);
                $hasTrendData = array_sum($trendValues) > 0;
            @endphp
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Volunteers by Skill (role proxy) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Volunteers by Skill</h3>
                    @if($hasRoleData)
                        <canvas id="skillsChart" height="140"></canvas>
                    @else
                        <p class="text-sm text-gray-500">Add member roles to visualize the distribution.</p>
                    @endif
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Volunteer Growth Trend</h4>
                        @if($hasTrendData)
                            <canvas id="growthChart" height="120"></canvas>
                        @else
                            <p class="text-sm text-gray-500">Approve registrations to build a trend line.</p>
                        @endif
                    </div>
                </div>

                <!-- Latest Announcements (Events) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Latest Announcements</h3>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">View all</a>
                    </div>
                    @php $latestEvents = $stats['recent_events']; @endphp
                    <div class="space-y-3">
                        @forelse($latestEvents as $e)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $e->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $e->date->format('M j, Y') }} • {{ $e->time }}</p>
                                </div>
                                <a href="{{ route('events.show', $e) }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">View</a>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 text-center py-8">No announcements available</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Activities (Registrations) -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                        <a href="{{ route('admin.attendance') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($stats['recent_registrations'] as $r)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $r->user->name }}</p>
                                    <p class="text-xs text-gray-600">Joined {{ $r->event->title }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($r->status === 'approved') bg-green-100 text-green-800
                                    @elseif($r->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">{{ ucfirst($r->status) }}</span>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500 text-center py-8">No recent activity</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
            <script>
                const skillsCtx = document.getElementById('skillsChart');
                if (skillsCtx) {
                    const labels = {!! json_encode(array_keys($roleBreakdown ?? [])) !!};
                    const values = {!! json_encode(array_values($roleBreakdown ?? [])) !!};
                    if (labels.length) {
                        new Chart(skillsCtx, {
                            type: 'doughnut',
                            data: { labels, datasets: [{ data: values, backgroundColor: ['#34d399', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa'] }] },
                            options: { plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                }

                const growthCtx = document.getElementById('growthChart');
                if (growthCtx) {
                    const labs = {!! json_encode(array_keys($analytics['trend'] ?? [])) !!};
                    const data = {!! json_encode(array_values($analytics['trend'] ?? [])) !!};
                    if (data.some(value => Number(value) > 0)) {
                        new Chart(growthCtx, {
                            type: 'bar',
                            data: {
                                labels: labs,
                                datasets: [{
                                    label: 'Approved registrations',
                                    data,
                                    backgroundColor: '#1d4ed8'
                                }]
                            },
                            options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                        });
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>
