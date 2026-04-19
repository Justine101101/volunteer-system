<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="color: #1f2937;">
            {{ __('Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <form method="GET" action="{{ route('admin.attendance.event') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div class="w-full sm:max-w-xl">
                        <label for="event_id" class="block text-sm font-semibold text-slate-700 mb-2">Select event</label>
                        <select
                            id="event_id"
                            name="event_id"
                            class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                            onchange="this.form.submit()"
                        >
                            <option value="">Choose an event…</option>
                            @foreach($events as $e)
                                <option value="{{ $e->id }}" @selected(($selectedEventId ?? '') === $e->id)>
                                    {{ $e->title }}
                                    @if($e->date)
                                        — {{ $e->date->format('M j, Y') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(!empty($selectedEventId))
                        <a href="{{ route('admin.attendance.event') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                            Clear selection
                        </a>
                    @endif
                </form>

                @if(!empty($selectedEventId) && empty($selectedEvent))
                    <p class="mt-4 text-sm text-rose-700">That event could not be found in the current list.</p>
                @endif
            </div>

            @if(!empty($selectedEventId) && !empty($selectedEvent))
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Selected event</p>
                    <p class="text-lg font-bold text-slate-900">{{ $selectedEvent->title }}</p>
                    @if($selectedEvent->date)
                        <p class="text-sm text-slate-600">{{ $selectedEvent->date->format('M j, Y') }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow">
                        <p class="text-sm text-slate-600 font-medium">Total Registrations</p>
                        <p class="text-2xl font-bold text-slate-900 mt-2">{{ $summary['total'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-5 hover:shadow-md transition-shadow">
                        <p class="text-sm text-slate-600 font-medium">Pending</p>
                        <p class="text-2xl font-bold text-amber-700 mt-2">{{ $summary['pending'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5 hover:shadow-md transition-shadow">
                        <p class="text-sm text-slate-600 font-medium">Approved</p>
                        <p class="text-2xl font-bold text-emerald-700 mt-2">{{ $summary['approved'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-sky-200 p-5 hover:shadow-md transition-shadow">
                        <p class="text-sm text-slate-600 font-medium">Attendance approved</p>
                        <p class="text-2xl font-bold text-sky-700 mt-2">{{ $summary['present_onsite'] }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-rose-200 p-5 hover:shadow-md transition-shadow">
                        <p class="text-sm text-slate-600 font-medium">Rejected</p>
                        <p class="text-2xl font-bold text-rose-700 mt-2">{{ $summary['rejected'] }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Participants (this event only)</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Attendance</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Registered</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider" style="color: #1f2937; background-color: #f9fafb;">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($registrations as $reg)
                                    @php $profileUrl = !empty($reg->local_user_id) ? route('admin.users.show', $reg->local_user_id) : null; @endphp
                                    <tr
                                        @if($profileUrl)
                                            data-profile-url="{{ $profileUrl }}"
                                            class="hover:bg-slate-50 cursor-pointer"
                                            title="Open user profile"
                                        @endif
                                    >
                                        @php
                                            $status = strtolower(trim($reg->registration_status ?? 'pending'));
                                            $registrationId = $reg->id ?? null;
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">
                                            @if($profileUrl)
                                                <a href="{{ $profileUrl }}" class="font-medium text-slate-900 hover:text-emerald-700" data-row-ignore-click>
                                                    {{ $reg->user->name ?? 'Unknown' }}
                                                </a>
                                            @else
                                                {{ $reg->user->name ?? 'Unknown' }}
                                            @endif
                                            @if(!empty($reg->user->email))
                                                <div class="text-xs text-slate-500">{{ $reg->user->email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($reg->registration_status === 'approved') bg-green-100 text-green-800
                                                @elseif($reg->registration_status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($reg->registration_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($status === 'approved' && !empty($registrationId))
                                                @if($reg->attended_at)
                                                    <div class="flex flex-col gap-1">
                                                        <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-900">Approved</span>
                                                        <span class="text-xs text-slate-600">{{ $reg->attended_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</span>
                                                        <form method="POST" action="{{ route('supabase.registrations.absent', ['registrationId' => $registrationId]) }}" class="w-fit" data-confirm="Revoke attendance approval for this volunteer?">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Revoke</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <form method="POST" action="{{ route('supabase.registrations.present', ['registrationId' => $registrationId]) }}" class="w-fit">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-sky-600 text-white hover:bg-sky-700">Approve attendance</button>
                                                    </form>
                                                @endif
                                            @elseif($status === 'approved')
                                                <span class="text-xs text-amber-600">Missing registration id</span>
                                            @else
                                                <span class="text-xs text-slate-400">Approve registration first</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->created_at ? $reg->created_at->format('M j, Y') : '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php $isPending = $status === 'pending'; @endphp
                                            <div class="flex items-center justify-end space-x-3">
                                                @if(!empty($reg->local_user_id))
                                                    <a
                                                        href="{{ route('admin.users.show', $reg->local_user_id) }}"
                                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50"
                                                    >
                                                        View Profile
                                                    </a>
                                                @endif
                                                @if(!empty($registrationId))
                                                    <form method="POST" action="{{ route('supabase.registrations.approve', ['registrationId' => $registrationId]) }}" data-confirm="Approve this registration?">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                                                            class="inline-flex items-center px-3 py-1.5 rounded-md text-white text-xs font-semibold shadow-sm focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                                                            style="{{ $isPending ? 'background-color:#059669;border:none;color:#ffffff;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                                                            title="{{ $isPending ? 'Approve registration' : 'Already processed' }}">
                                                            <span style="{{ $isPending ? 'color:#ffffff;' : 'color:#6B7280;' }}">Approve</span>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('supabase.registrations.reject', ['registrationId' => $registrationId]) }}" data-confirm="Decline this registration?">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" {{ $isPending ? '' : 'disabled aria-disabled=true' }}
                                                            class="inline-flex items-center px-3 py-1.5 rounded-md text-white text-xs font-semibold shadow-sm focus:outline-none {{ $isPending ? '' : 'cursor-not-allowed' }}"
                                                            style="{{ $isPending ? 'background-color:#E11D48;border:none;color:#ffffff;' : 'background-color:#E5E7EB;color:#6B7280;border:none;' }}"
                                                            title="{{ $isPending ? 'Decline registration' : 'Already processed' }}">
                                                            <span style="{{ $isPending ? 'color:#ffffff;' : 'color:#6B7280;' }}">Decline</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400">No ID</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500" style="color: #6b7280;">No participants found for this event.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('tr[data-profile-url]').forEach((row) => {
            row.addEventListener('click', (event) => {
                if (event.target.closest('a, button, input, form, [data-row-ignore-click]')) {
                    return;
                }
                const profileUrl = row.getAttribute('data-profile-url');
                if (profileUrl) {
                    window.location.href = profileUrl;
                }
            });
        });
    </script>
</x-app-layout>
