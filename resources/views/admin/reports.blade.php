<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $kpis['users'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Events</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $kpis['events'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Registrations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $kpis['registrations'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Approved</p>
                    <p class="text-2xl font-semibold text-green-700">{{ $kpis['approved'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrations by Status</h3>
                    <canvas id="statusChart" height="140"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events</h3>
                    <ul class="space-y-3">
                        @forelse($events as $e)
                            <li class="flex justify-between items-center p-3 rounded bg-gray-50">
                                <span class="font-medium text-gray-900">{{ $e->title }}</span>
                                <span class="text-sm text-gray-600">{{ $e->date->format('M j, Y') }}</span>
                            </li>
                        @empty
                            <li class="text-gray-500">No events to show.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="text-right">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">
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


