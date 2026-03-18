<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Users</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">{{ $kpis['users'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Events</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">{{ $kpis['events'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Registrations</p>
                    <p class="text-2xl font-bold text-slate-900 mt-2">{{ $kpis['registrations'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Approved</p>
                    <p class="text-2xl font-bold text-emerald-700 mt-2">{{ $kpis['approved'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Registrations by Status</h3>
                    <canvas id="statusChart" height="140"></canvas>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Upcoming Events</h3>
                    <ul class="space-y-3">
                        @forelse($events as $e)
                            <li class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                                <span class="font-medium text-slate-900">{{ $e->title }}</span>
                                <span class="text-sm text-slate-600">{{ $e->date->format('M j, Y') }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500">No events to show.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="text-right">
                <a href="#" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl shadow-sm hover:bg-indigo-700 transition-colors font-semibold">
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('statusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Approved', 'Rejected'],
                    datasets: [{
                        data: [{{ $byStatus['pending'] }}, {{ $byStatus['approved'] }}, {{ $byStatus['rejected'] }}],
                        backgroundColor: ['#FBBF24', '#34D399', '#F87171'],
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        }
    </script>
</x-app-layout>
