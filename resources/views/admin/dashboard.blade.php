<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-600/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300 flex items-center justify-center ring-1 ring-emerald-600/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 118 0v2m-8 4h8a2 2 0 002-2v-2a6 6 0 10-12 0v2a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-semibold text-slate-900 dark:text-slate-100">Admin Dashboard</h2>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">Volunteer Management System</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('notifications.index') }}"
                   class="relative inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                   title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"/>
                    </svg>
                    <span class="sr-only">Notifications</span>
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Admin</div>
                            </div>
                            <x-user-avatar :user="auth()->user()" :size="36" class="shadow-sm" />
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">{{ __('Home') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('settings')">{{ __('Settings') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </x-slot>

    @php
        $trend = $analytics['trend'] ?? [];
        $trendLabels = array_keys($trend);
        $trendValues = array_values($trend);
        $hasTrend = array_sum($trendValues) > 0;

        $statusBreakdown = $analytics['by_status'] ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0];

        $eventsTrend = $eventsPerMonth ?? [];
        $eventsTrendLabels = array_keys($eventsTrend);
        $eventsTrendValues = array_values($eventsTrend);

        $totalUsers = (int) ($stats['total_users'] ?? 0);
        $totalVolunteers = (int) ($stats['total_volunteers'] ?? 0);
        $nonVolunteerUsers = max(0, $totalUsers - $totalVolunteers);

        $upcomingEvents = collect($stats['recent_events'] ?? [])
            ->filter(function ($e) {
                return $e?->date && $e->date->gte(now()->startOfDay());
            })
            ->sortBy(fn ($e) => $e->date?->timestamp ?? 0)
            ->take(6)
            ->values();
    @endphp

    <div class="py-10 bg-slate-50 dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Hero -->
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8 dark:border-slate-700 dark:bg-slate-900">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                            Welcome back, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-2 text-sm sm:text-base text-slate-600 dark:text-slate-300">
                            Monitor activity, approvals, and upcoming events at a glance.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('events.create') }}"
                           class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition dark:focus:ring-offset-slate-900">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create event
                        </a>
                        <a href="{{ route('admin.attendance') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900">
                            Review approvals
                        </a>
                    </div>
                </div>
            </section>

            <!-- Top stats -->
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
                <a href="{{ route('members.index') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Volunteers</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ $stats['total_volunteers'] }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Active volunteer accounts</p>
                        </div>
                        <div class="shrink-0 h-11 w-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300 flex items-center justify-center ring-1 ring-emerald-600/10 group-hover:scale-105 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('members.index') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Members</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ $stats['total_members'] }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Club member directory</p>
                        </div>
                        <div class="shrink-0 h-11 w-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300 flex items-center justify-center ring-1 ring-emerald-600/10 group-hover:scale-105 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('events.index') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Approved Events</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ $stats['approved_registrations'] }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Approved registrations</p>
                        </div>
                        <div class="shrink-0 h-11 w-11 rounded-2xl bg-sky-500/10 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300 flex items-center justify-center ring-1 ring-sky-600/10 group-hover:scale-105 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.attendance') }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Pending Applications</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">{{ $stats['pending_registrations'] }}</p>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Awaiting review</p>
                        </div>
                        <div class="shrink-0 h-11 w-11 rounded-2xl bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 flex items-center justify-center ring-1 ring-amber-600/10 group-hover:scale-105 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </section>

            <!-- Charts -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm p-6 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Registrations Over Time</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Last 6 months of approved registrations</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Growth</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $analytics['growth_rate_pct'] }}%</p>
                        </div>
                    </div>
                    <div class="mt-6 h-72">
                        <canvas id="chartRegistrations"></canvas>
                    </div>
                    @if(!$hasTrend)
                        <p class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 dark:bg-amber-500/10 dark:text-amber-200 dark:border-amber-500/20">
                            No trend data yet — approve some registrations to populate this chart.
                        </p>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Registrations by Status</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pending, approved, and rejected</p>
                        </div>
                    </div>
                    <div class="mt-6 h-72">
                        <canvas id="chartUsers"></canvas>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pending</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $statusBreakdown['pending'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Approved</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $statusBreakdown['approved'] }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Rejected</p>
                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $statusBreakdown['rejected'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm p-6 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Events per Month</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Last 6 months of created events</p>
                        </div>
                        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-300 dark:hover:text-emerald-200 transition">
                            View events
                        </a>
                    </div>
                    <div class="mt-6 h-72">
                        <canvas id="chartEvents"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">KPIs</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Operational health</p>
                        </div>
                    </div>

                    <dl class="mt-6 space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <dt class="text-sm text-slate-600 dark:text-slate-300">Event success</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $analytics['event_success_pct'] }}%</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <dt class="text-sm text-slate-600 dark:text-slate-300">Avg. attendance</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $analytics['avg_attendance'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <dt class="text-sm text-slate-600 dark:text-slate-300">Upcoming events</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $quickStats['upcoming_events'] ?? 0 }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                            <dt class="text-sm text-slate-600 dark:text-slate-300">Registrations today</dt>
                            <dd class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $quickStats['registrations_today'] ?? 0 }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- Secondary widgets -->
            <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900 overflow-hidden">
                    <div class="p-6 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Recent Activity</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Latest registrations and contact messages</p>
                        </div>
                        <a href="{{ route('admin.attendance') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-300 dark:hover:text-emerald-200 transition">
                            Open approvals
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php
                            $recentRegs = collect($stats['recent_registrations'] ?? [])->take(5);
                            $recentContacts = collect($stats['recent_contacts'] ?? [])->take(5);
                        @endphp

                        @foreach($recentRegs as $r)
                            @php
                                $status = strtolower((string) ($r->registration_status ?? 'pending'));
                                $badge = match ($status) {
                                    'approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-200',
                                    default => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200',
                                };
                            @endphp
                            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $r->user->name ?? 'Unknown user' }}
                                        <span class="text-slate-400 dark:text-slate-500 font-medium">registered</span>
                                        {{ $r->event->title ?? 'an event' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ optional($r->created_at)->format('M j, Y g:i A') ?? '—' }}
                                    </p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        @endforeach

                        @foreach($recentContacts as $c)
                            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $c->name ?? 'Visitor' }}
                                        <span class="text-slate-400 dark:text-slate-500 font-medium">sent feedback</span>
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ \Illuminate\Support\Str::limit((string) ($c->message ?? ''), 90) }}
                                    </p>
                                </div>
                                <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">
                                    {{ optional($c->created_at)->format('M j') ?? '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900 overflow-hidden">
                    <div class="p-6 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Upcoming Events</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Next scheduled activities</p>
                        </div>
                        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-300 dark:hover:text-emerald-200 transition">
                            View all
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($upcomingEvents as $e)
                            <div class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $e->title ?: 'Untitled event' }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ optional($e->date)->format('l, M j, Y') ?? '—' }}
                                        @if(!empty($e->time))
                                            • {{ $e->time }}
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 inline-flex items-center rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-semibold dark:bg-slate-800 dark:text-slate-200">
                                    {{ optional($e->date)->format('M j') ?? '' }}
                                </span>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center">
                                <p class="text-sm text-slate-500 dark:text-slate-400">No upcoming events yet.</p>
                                <a href="{{ route('events.create') }}" class="mt-3 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                                    Create an event
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

                <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(148, 163, 184, 0.14)' : 'rgba(148, 163, 184, 0.20)';
                const mutedText = isDark ? '#94a3b8' : '#64748b';

                Chart.defaults.color = mutedText;
                Chart.defaults.font.family = "ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji','Segoe UI Emoji'";

                function baseScales() {
                    return {
                        x: { grid: { display: false }, ticks: { color: mutedText }, border: { display: false } },
                        y: { grid: { color: gridColor, drawBorder: false }, ticks: { color: mutedText, maxTicksLimit: 5 }, border: { display: false } }
                    };
                }

                const regEl = document.getElementById('chartRegistrations');
                if (regEl) {
                    new Chart(regEl, {
                        type: 'line',
                        data: {
                            labels: @json($trendLabels),
                            datasets: [{
                                label: 'Approved registrations',
                                data: @json($trendValues),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: baseScales(),
                        }
                    });
                }

                const usersEl = document.getElementById('chartUsers');
                if (usersEl) {
                    new Chart(usersEl, {
                        type: 'doughnut',
                        data: {
                            labels: ['Pending', 'Approved', 'Rejected'],
                            datasets: [{
                                data: [{{ $statusBreakdown['pending'] }}, {{ $statusBreakdown['approved'] }}, {{ $statusBreakdown['rejected'] }}],
                                backgroundColor: ['#fbbf24', '#10b981', '#f97373'],
                                borderWidth: 0,
                                hoverOffset: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: { legend: { display: false } },
                        }
                    });
                }

                const eventsEl = document.getElementById('chartEvents');
                if (eventsEl) {
                    const evtKeys = @json($eventsTrendLabels);
                    const months = evtKeys.map(k => {
                        const [y, m] = k.split('-');
                        const d = new Date(parseInt(y, 10), parseInt(m, 10) - 1, 1);
                        return d.toLocaleString('en-US', { month: 'short' });
                    });
                    const data = @json($eventsTrendValues);

                    new Chart(eventsEl, {
                        type: 'bar',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Events',
                                data,
                                backgroundColor: 'rgba(16, 185, 129, 0.18)',
                                borderColor: '#10b981',
                                borderWidth: 1,
                                borderRadius: 10,
                                maxBarThickness: 44,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: baseScales(),
                        }
                    });
                }
            })();
        </script>
    </div>
</x-app-layout>

