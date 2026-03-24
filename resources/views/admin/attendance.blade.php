<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="color: #1f2937;">
            {{ __('Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50" x-data="attendanceProfiles()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <div class="bg-white rounded-2xl shadow-sm border border-rose-200 p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm text-slate-600 font-medium">Rejected</p>
                    <p class="text-2xl font-bold text-rose-700 mt-2">{{ $summary['rejected'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Event Registrations</h3>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="submitBulkAction('{{ route('supabase.registrations.bulk-approve') }}','Approve selected registrations?')" class="px-3 py-2 rounded-md bg-emerald-600 text-white text-sm hover:bg-emerald-700" style="background-color: #059669; color: #ffffff;">
                            <span style="color: #ffffff;">Approve Selected</span>
                        </button>
                        <button type="button" onclick="submitBulkAction('{{ route('supabase.registrations.bulk-reject') }}','Decline selected registrations?')" class="px-3 py-2 rounded-md bg-rose-600 text-white text-sm hover:bg-rose-700" style="background-color: #e11d48; color: #ffffff;">
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
                                    @php
                                        $status = strtolower(trim($reg->registration_status ?? 'pending'));
                                        $selectable = $status === 'pending';
                                        $registrationId = $reg->id ?? null;
                                    @endphp
                                    <td class="px-6 py-4">
                                        <input
                                            type="checkbox"
                                            value="{{ $registrationId }}"
                                            class="reg-check rounded border-gray-300"
                                            {{ $selectable && !empty($registrationId) ? '' : 'disabled' }}
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->user->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->event->title ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($reg->registration_status === 'approved') bg-green-100 text-green-800
                                            @elseif($reg->registration_status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif"
                                            style="@if($reg->registration_status === 'approved') background-color: #dcfce7; color: #166534;
                                            @elseif($reg->registration_status === 'rejected') background-color: #fee2e2; color: #991b1b;
                                            @else background-color: #fef3c7; color: #854d0e; @endif">
                                            {{ ucfirst($reg->registration_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="color: #111827;">{{ $reg->created_at ? $reg->created_at->format('M j, Y') : '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php $isPending = $status === 'pending'; @endphp
                                        <div class="flex items-center justify-end space-x-3">
                                            @if(!empty($reg->profile))
                                                <button
                                                    type="button"
                                                    @click='openProfile(@js($reg->profile))'
                                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50"
                                                >
                                                    View Profile
                                                </button>
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
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500" style="color: #6b7280;">No registrations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">{{ $registrations->links() }}</div>
            </div>

            <!-- Profile Modal -->
            <div
                x-show="openModal"
                x-cloak
                x-transition.opacity
                class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                @click.self="closeProfile()"
            >
                <div
                    x-show="openModal"
                    x-transition.scale
                    class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden"
                >
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-slate-400">User Profile</p>
                            <h3 class="text-sm font-semibold text-slate-900" x-text="profile?.name ?? 'User details'"></h3>
                        </div>
                        <button type="button" class="inline-flex items-center justify-center rounded-full p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100" @click="closeProfile()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4 text-sm text-slate-700">
                        <template x-if="profile">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full overflow-hidden bg-emerald-600 text-white flex items-center justify-center font-semibold text-lg">
                                        <template x-if="profile.photo_url">
                                            <img :src="profile.photo_url" :alt="profile.name || 'User'" class="h-full w-full object-cover" x-on:error="$el.style.display='none'">
                                        </template>
                                        <span x-show="!profile.photo_url" x-text="(profile.name || 'U').charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-slate-900" x-text="profile.name || 'Unknown'"></p>
                                        <p class="text-xs text-slate-500" x-text="profile.email || 'No email'"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900" x-text="profile.role || 'Volunteer'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone Number</p>
                                        <p class="mt-1 text-sm text-slate-700" x-text="profile.phone || 'Not provided'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Member Since</p>
                                        <p class="mt-1 text-sm text-slate-700" x-text="profile.created_at || 'Not available'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Preferences</p>
                                        <p class="mt-1 text-sm text-slate-700">
                                            <span x-text="profile.notification_pref ? 'Email notifications: On' : 'Email notifications: Off'"></span><br>
                                            <span x-text="profile.dark_mode ? 'Dark mode: Enabled' : 'Dark mode: Disabled'"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                        <button type="button" class="text-sm font-medium text-slate-600 hover:text-slate-800" @click="closeProfile()">Close</button>
                    </div>
                </div>
            </div>

            <script>
                function attendanceProfiles() {
                    return {
                        openModal: false,
                        profile: null,
                        openProfile(payload) {
                            this.profile = payload;
                            this.openModal = true;
                        },
                        closeProfile() {
                            this.openModal = false;
                            this.profile = null;
                        },
                    }
                }

                const checkAll = document.getElementById('check-all');
                if (checkAll) {
                    checkAll.addEventListener('change', () => {
                        document.querySelectorAll('.reg-check:not(:disabled)').forEach(cb => { cb.checked = checkAll.checked; });
                    });
                }

                function submitBulkAction(actionUrl, confirmText) {
                    const ids = Array.from(document.querySelectorAll('.reg-check:checked')).map(cb => cb.value);
                    if (ids.length === 0) {
                        window.dispatchEvent(new CustomEvent('confirm-dialog', {
                            detail: {
                                title: 'Nothing selected',
                                message: 'Select at least one pending registration.',
                                confirmLabel: 'OK',
                                cancelLabel: 'Close',
                                tone: 'safe',
                                formId: null,
                            }
                        }));
                        return;
                    }

                    const isDecline = /decline|reject/i.test(confirmText || '');
                    window.dispatchEvent(new CustomEvent('confirm-dialog', {
                        detail: {
                            title: 'Confirm bulk action',
                            message: confirmText,
                            confirmLabel: isDecline ? 'Decline selected' : 'Approve selected',
                            cancelLabel: 'Cancel',
                            tone: isDecline ? 'danger' : 'safe',
                            formId: null,
                            onConfirmEvent: 'vms-bulk-registration-action',
                            onConfirmDetail: { actionUrl, ids },
                        }
                    }));
                }

                // Receive confirmation from the global modal and submit the bulk form
                window.addEventListener('vms-bulk-registration-action', (e) => {
                    const { actionUrl, ids } = (e?.detail || {});
                    if (!actionUrl || !Array.isArray(ids) || ids.length === 0) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;

                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = '{{ csrf_token() }}';
                    form.appendChild(token);

                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                });
            </script>
        </div>
    </div>
</x-app-layout>


