<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">System Activity</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Security and accountability trail for critical actions.
                        </p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                            <input type="text" name="action" id="action" value="{{ request('action') }}"
                                   placeholder="e.g. event.created"
                                   class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label for="resource_type" class="block text-sm font-medium text-gray-700">Resource Type</label>
                            <input type="text" name="resource_type" id="resource_type" value="{{ request('resource_type') }}"
                                   placeholder="e.g. Event, User"
                                   class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Filter
                            </button>
                            <a href="{{ route('admin.audit-logs.index') }}"
                               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Logs table -->
                <div class="px-6 py-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resource ID</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                        {{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                        @if($log->user)
                                            {{ $log->user->name }} <span class="text-gray-400 text-xs">({{ $log->user->email }})</span>
                                        @else
                                            <span class="text-gray-400 italic">System</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                        {{ $log->resource_type }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                        <code class="text-xs text-gray-500">{{ $log->resource_id ?? '—' }}</code>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 align-top">
                                        @php
                                            $payload = $log->payload ?? [];
                                            $new = $payload['new'] ?? null;
                                            $old = $payload['old'] ?? null;
                                        @endphp
                                        @if($old || $new)
                                            <details class="text-xs">
                                                <summary class="cursor-pointer text-emerald-700 hover:underline">
                                                    View
                                                </summary>
                                                <div class="mt-1 space-y-1">
                                                    @if($old)
                                                        <div>
                                                            <div class="font-semibold text-gray-600">Old:</div>
                                                            <pre class="bg-gray-100 rounded p-2 overflow-x-auto">{{ json_encode($old, JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    @endif
                                                    @if($new)
                                                        <div>
                                                            <div class="font-semibold text-gray-600">New:</div>
                                                            <pre class="bg-gray-100 rounded p-2 overflow-x-auto">{{ json_encode($new, JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    @endif
                                                </div>
                                            </details>
                                        @elseif($payload)
                                            <details class="text-xs">
                                                <summary class="cursor-pointer text-emerald-700 hover:underline">
                                                    View
                                                </summary>
                                                <pre class="mt-1 bg-gray-100 rounded p-2 overflow-x-auto">{{ json_encode($payload, JSON_PRETTY_PRINT) }}</pre>
                                            </details>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                        No audit logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

