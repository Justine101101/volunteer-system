<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700 transition-colors"></a>
                            </li>
                            <li class="text-slate-400"></li>
                            <li class="text-slate-700 font-medium"></li>
                        </ol>
                    </nav>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                        Reports
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Organization analytics, event activity, and volunteer participation insights.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <select
                            class="appearance-none w-44 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                            aria-label="Date range"
                        >
                            <option>This Month</option>
                            <option>This Year</option>
                            <option>All Time</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.reports.export') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition-all duration-300"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.19L6.28 7.97a.75.75 0 10-1.06 1.06l4.25 4.25c.3.3.77.3 1.06 0l4.25-4.25a.75.75 0 10-1.06-1.06l-2.97 2.97V2.75z" />
                            <path d="M4 13.5a.75.75 0 01.75.75v1c0 .414.336.75.75.75h9a.75.75 0 00.75-.75v-1a.75.75 0 011.5 0v1A2.25 2.25 0 0114.5 18h-9A2.25 2.25 0 013.25 15.25v-1A.75.75 0 014 13.5z" />
                        </svg>
                        Export
                    </a>

                    <button
                        type="button"
                        onclick="window.location.reload()"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white transition-all duration-300"
                        aria-label="Refresh"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466.75.75 0 10-1.06 1.06 7 7 0 0011.683-3.127.75.75 0 10-1.422-.399z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M4.688 8.576a5.5 5.5 0 019.201-2.466.75.75 0 101.06-1.06A7 7 0 003.266 8.11a.75.75 0 101.422.399z" clip-rule="evenodd" />
                            <path d="M3.5 4.75a.75.75 0 01.75-.75h3a.75.75 0 010 1.5H5v2.25a.75.75 0 01-1.5 0v-3zM16.5 15.25a.75.75 0 01-.75.75h-3a.75.75 0 010-1.5H15v-2.25a.75.75 0 011.5 0v3z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-10 lg:py-12 space-y-8">
            <!-- Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 flex items-center justify-between hover:-translate-y-1 transition-all duration-300">
                    <div>
                        <p class="text-sm text-slate-500">Users</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $kpis['users'] }}</p>
                    </div>
                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 flex items-center justify-between hover:-translate-y-1 transition-all duration-300">
                    <div>
                        <p class="text-sm text-slate-500">Events</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $kpis['events'] }}</p>
                    </div>
                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 flex items-center justify-between hover:-translate-y-1 transition-all duration-300">
                    <div>
                        <p class="text-sm text-slate-500">Registrations</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $kpis['registrations'] }}</p>
                    </div>
                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 flex items-center justify-between hover:-translate-y-1 transition-all duration-300">
                    <div>
                        <p class="text-sm text-slate-500">Approved</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $kpis['approved'] }}</p>
                    </div>
                    <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Registrations Over Time (line chart) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8 lg:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Registrations Over Time</h3>
                            <p class="text-sm text-slate-500">Monthly registrations (last 8 months)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Total</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $kpis['registrations'] }}</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="regsOverTimeChart"></canvas>
                    </div>
                </div>

                <!-- Registrations by Status (doughnut) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8 lg:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Registrations by Status</h3>
                            <p class="text-sm text-slate-500">Distribution of volunteer registrations</p>
                        </div>
                        <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" aria-label="Chart filter">
                            <option>This Month</option>
                            <option>This Year</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-6 items-center">
                        <div>
                            <div class="h-72 flex items-center justify-center">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                                        <span class="text-sm font-medium text-slate-700">Pending</span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $byStatus['pending'] }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Approved</span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $byStatus['approved'] }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Rejected</span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $byStatus['rejected'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events per Month (NEW bar chart) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8 lg:col-span-2">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Events per Month</h3>
                            <p class="text-sm text-slate-500">Scheduled events (last 8 months)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Total</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $kpis['events'] }}</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="eventsPerMonthChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming Events Panel -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8 lg:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Upcoming Events</h3>
                            <p class="text-sm text-slate-500">Next scheduled activities</p>
                        </div>
                        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">
                            View all
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100 rounded-2xl border border-slate-100 overflow-hidden">
                        @forelse($events as $e)
                            <div class="flex items-center justify-between px-4 py-4 hover:bg-slate-50 transition-all duration-300">
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800 truncate">{{ $e->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $e->date->format('l, M j, Y') }}</p>
                                </div>
                                <span class="ml-4 shrink-0 bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $e->date->format('M j') }}
                                </span>
                            </div>
                        @empty
                            <div class="px-4 py-10 text-center">
                                <p class="text-sm text-slate-500">No events to show.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Global minimal chart styling
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

        const ctx = document.getElementById('statusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Approved', 'Rejected'],
                    datasets: [{
                        data: [{{ $byStatus['pending'] }}, {{ $byStatus['approved'] }}, {{ $byStatus['rejected'] }}],
                        backgroundColor: ['#FBBF24', '#10B981', '#F43F5E'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.92)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(148, 163, 184, 0.25)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                        },
                    },
                }
            });
        }

        // Line chart: Registrations over time
        const regsEl = document.getElementById('regsOverTimeChart');
        if (regsEl) {
            const regKeys = @json(array_keys($registrationsOverTime ?? []));
            const regLabels = regKeys.map(k => {
                const [y, m] = k.split('-');
                const d = new Date(parseInt(y, 10), parseInt(m, 10) - 1, 1);
                return d.toLocaleString('en-US', { month: 'short' });
            });
            const regValues = @json(array_values($registrationsOverTime ?? []));

            new Chart(regsEl, {
                type: 'line',
                data: {
                    labels: regLabels,
                    datasets: [{
                        label: 'Registrations',
                        data: regValues,
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

        // Bar chart: Events per month
        const eventsEl = document.getElementById('eventsPerMonthChart');
        if (eventsEl) {
            const evtKeys = @json(array_keys($eventsPerMonth ?? []));
            const evtLabels = evtKeys.map(k => {
                const [y, m] = k.split('-');
                const d = new Date(parseInt(y, 10), parseInt(m, 10) - 1, 1);
                return d.toLocaleString('en-US', { month: 'short' });
            });
            const evtValues = @json(array_values($eventsPerMonth ?? []));

            new Chart(eventsEl, {
                type: 'bar',
                data: {
                    labels: evtLabels,
                    datasets: [{
                        label: 'Events',
                        data: evtValues,
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
    </script>
</x-app-layout>
