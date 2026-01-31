<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="color: #1f2937;">
            {{ __('Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600" style="color: #4b5563;">Total Registrations</p>
                    <p class="text-2xl font-semibold text-gray-900" style="color: #111827;">{{ $summary['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600" style="color: #4b5563;">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-700" style="color: #b45309;">{{ $summary['pending'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600" style="color: #4b5563;">Approved</p>
                    <p class="text-2xl font-semibold text-green-700" style="color: #15803d;">{{ $summary['approved'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600" style="color: #4b5563;">Rejected</p>
                    <p class="text-2xl font-semibold text-red-700" style="color: #b91c1c;">{{ $summary['rejected'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900" style="color: #111827;">Event Registrations</h3>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="submitBulkAction('{{ route('registrations.bulk-approve') }}','Approve selected registrations?')" class="px-3 py-2 rounded-md bg-emerald-600 text-white text-sm hover:bg-emerald-700" style="background-color: #059669; color: #ffffff;">
                            <span style="color: #ffffff;">Approve Selected</span>
                        </button>
                        <button type="button" onclick="submitBulkAction('{{ route('registrations.bulk-reject') }}','Decline selected registrations?')" class="px-3 py-2 rounded-md bg-rose-600 text-white text-sm hover:bg-rose-700" style="background-color: #e11d48; color: #ffffff;">
                            <span style="color: #ffffff;">Decline Selected</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;"><input id="check-all" type="checkbox" class="rounded border-gray-400" /></th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">User</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Registered</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($registrations as $reg)
                                <tr>
                                    @php $status = strtolower(trim($reg->status ?? '')); $selectable = $status === 'pending'; @endphp
                                    <td class="px-6 py-4"><input type="checkbox" value="{{ $reg->id }}" class="reg-check rounded border-gray-300" {{ $selectable ? '' : 'disabled' }} /></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->event->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($reg->status === 'approved') bg-green-100 text-green-800
                                            @elseif($reg->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif"
                                            style="@if($reg->status === 'approved') background-color: #dcfce7; color: #166534;
                                            @elseif($reg->status === 'rejected') background-color: #fee2e2; color: #991b1b;
                                            @else background-color: #fef3c7; color: #854d0e; @endif">
                                            {{ ucfirst($reg->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->created_at->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php $isPending = $status === 'pending'; @endphp
                                        <div class="flex items-center justify-end space-x-3">
                                            <form method="POST" action="{{ route('registrations.approve', $reg) }}" onsubmit="return confirm('Approve this registration?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-white text-xs font-semibold shadow-sm focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                                                    style="{{ $isPending ? 'background-color:#059669;border:none;color:#ffffff;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                                                    title="{{ $isPending ? 'Approve registration' : 'Already processed' }}">
                                                    <span style="{{ $isPending ? 'color:#ffffff;' : 'color:#6B7280;' }}">Approve</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('registrations.reject', $reg) }}" onsubmit="return confirm('Decline this registration?')">
            @csrf
            @method('PATCH')
            <button type="submit" {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                class="inline-flex items-center px-3 py-1.5 rounded-md text-white text-xs font-semibold shadow-sm focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                style="{{ $isPending ? 'background-color:#E11D48;border:none;color:#ffffff;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                title="{{ $isPending ? 'Decline registration' : 'Already processed' }}">
                <span style="{{ $isPending ? 'color:#ffffff;' : 'color:#6B7280;' }}">Decline</span>
            </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500" style="color: #6b7280;">No registrations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">{{ $registrations->links() }}</div>
            </div>
            <script>
                const checkAll = document.getElementById('check-all');
                if (checkAll) {
                    checkAll.addEventListener('change', () => {
                        document.querySelectorAll('.reg-check:not(:disabled)').forEach(cb => { cb.checked = checkAll.checked; });
                    });
                }
                function submitBulkAction(actionUrl, confirmText) {
                    const ids = Array.from(document.querySelectorAll('.reg-check:checked')).map(cb => cb.value);
                    if (ids.length === 0) { alert('Select at least one pending registration.'); return; }
                    if (!confirm(confirmText)) return;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;
                    const token = document.createElement('input');
                    token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
                    form.appendChild(token);
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                        form.appendChild(input);
                    });
                    document.body.appendChild(form);
                    form.submit();
                }
            </script>
        </div>
    </div>
</x-app-layout>


