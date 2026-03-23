<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ __('User Profile') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">View user details</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600 transition">
                    Back
                </a>
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition">
                    Edit User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 dark:bg-slate-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="h-14 w-14 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold text-xl">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-50 truncate">{{ $user->name }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 truncate">{{ $user->email }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user->role === 'admin') bg-indigo-100 text-indigo-800
                                    @elseif($user->role === 'president') bg-purple-100 text-purple-800
                                    @else bg-emerald-100 text-emerald-800
                                    @endif">
                                    {{ $user->role === 'superadmin' ? 'Admin' : ucfirst($user->role) }}
                                </span>
                                <span class="text-xs text-slate-500">Created {{ optional($user->created_at)->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</p>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-50">
                                {{ $user->phone ?: 'Not provided' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Last Login</p>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-50">
                                {{ optional($user->last_login_at)->format('M j, Y g:i A') ?? 'Not available' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Notifications</p>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-50">
                                {{ $user->notification_pref ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Dark Mode</p>
                            <p class="mt-1 text-sm text-slate-900 dark:text-slate-50">
                                {{ $user->dark_mode ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>
                    </div>

                    <!-- Event participation -->
                    <div class="mt-10 border-t border-slate-100 dark:border-slate-700 pt-6">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-3">Event Participation</h4>
                        @if(!empty($participation))
                            <div class="space-y-3">
                                @foreach($participation as $item)
                                    <div class="flex items-start justify-between rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-3 bg-slate-50/60 dark:bg-slate-700/40">
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-50">
                                                {{ $item['title'] }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                                                @if(!empty($item['event_date']))
                                                    {{ \Carbon\Carbon::parse($item['event_date'])->format('M j, Y') }}
                                                @else
                                                    Date not available
                                                @endif
                                                @if(!empty($item['created_at']))
                                                    · Joined {{ \Carbon\Carbon::parse($item['created_at'])->format('M j, Y g:i A') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item['registration_status'] === 'approved') bg-emerald-100 text-emerald-800
                                            @elseif($item['registration_status'] === 'rejected') bg-red-100 text-red-800
                                            @else bg-amber-100 text-amber-800
                                            @endif">
                                            {{ ucfirst($item['registration_status']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-300">This user has not joined any events yet.</p>
                        @endif
                    </div>

                    <div class="mt-8 flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-6">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-50">
                            ← Back to users
                        </a>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium text-emerald-700 dark:text-emerald-300 hover:text-emerald-800 dark:hover:text-emerald-200">
                                Edit user →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

