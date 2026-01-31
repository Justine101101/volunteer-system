<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain" />
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Officer Dashboard</h2>
            </div>
            <div class="flex items-center space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-start mr-2 hidden sm:flex">
                                <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                                <span class="text-xs text-gray-600">Officer</span>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-amber-600 text-white flex items-center justify-center font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}
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
                    <p class="text-xs text-gray-500 mt-1">Approved registrations growth</p>
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
                    <p class="text-xs text-gray-500 mt-1">Registrations completed successfully</p>
                </div>
            </div>

            <!-- Top Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
                <div class="relative overflow-hidden rounded-2xl p-6 border border-emerald-200 bg-emerald-100 text-gray-900 hover:shadow-lg transition">
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
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 shadow border border-gray-200 bg-white text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Total Events</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_events'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Scheduled & past</p>
                        </div>
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 border border-amber-200 bg-amber-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Approved Registrations</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['approved_registrations'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">Confirmed participation</p>
                        </div>
                        <div class="p-3 rounded-full bg-orange-100 text-orange-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 border border-purple-200 bg-purple-100 text-gray-900 hover:shadow-lg transition">
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
                </div>

                <div class="relative overflow-hidden rounded-2xl p-6 border border-purple-200 bg-purple-100 text-gray-900 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm leading-5 text-gray-700">Total Registrations</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_registrations'] }}</p>
                            <p class="mt-2 text-xs text-gray-500">All-time</p>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-700">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                            <p class="text-sm text-gray-600">Events This Month</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $quickStats['events_this_month'] }}</p>
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
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Events</h3>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg border border-amber-100">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $event->date->format('M j, Y') }} at {{ $event->time }}</p>
                                </div>
                                <a href="{{ route('events.show', $event) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-md bg-amber-600 text-white text-xs font-medium hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    View
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No upcoming events</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Registrations</h3>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500">View events</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentRegistrations as $registration)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $registration->user->name ?? 'User' }}</p>
                                    <p class="text-sm text-gray-600">{{ $registration->event->title ?? 'Event' }}</p>
                                    <p class="text-xs text-gray-500">{{ $registration->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($registration->status === 'approved') bg-green-100 text-green-800
                                    @elseif($registration->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">{{ ucfirst($registration->status) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent registrations</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Trend Chart -->
            @php
                $trendValues = array_values($analytics['trend'] ?? []);
                $hasTrendData = array_sum($trendValues) > 0;
            @endphp
            @if($hasTrendData)
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Approved Registrations Trend</h3>
                <canvas id="officerTrendChart" height="80"></canvas>
            </div>
            @endif

            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
            <script>
                const officerCtx = document.getElementById('officerTrendChart');
                if (officerCtx) {
                    const labels = {!! json_encode(array_keys($analytics['trend'] ?? [])) !!};
                    const data = {!! json_encode(array_values($analytics['trend'] ?? [])) !!};
                    if (data.some(v => Number(v) > 0)) {
                        new Chart(officerCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Approved registrations',
                                    data,
                                    borderColor: '#f59e0b',
                                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                }]
                            },
                            options: {
                                scales: { y: { beginAtZero: true } },
                                plugins: { legend: { display: true } },
                                responsive: true,
                                maintainAspectRatio: false,
                            }
                        });
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>

