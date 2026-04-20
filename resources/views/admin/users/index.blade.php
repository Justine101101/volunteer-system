<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                {{ __('Manage Users') }}
            </h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add New User</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900" x-data="usersModal()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-600">Total Users</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-600">Admins</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ $stats['total_admins'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-600">Volunteers</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ $stats['total_volunteers'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="w-full md:w-48">
                        <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="president" {{ request('role') == 'president' ? 'selected' : '' }}>President</option>
                            <option value="volunteer" {{ request('role') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                        <span>Search</span>
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}"
                           class="px-6 py-2 bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600 transition">
                            <span>Clear</span>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">User</th>
                                <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Email</th>
                                <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Phone</th>
                                <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Role</th>
                                <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Created</th>
                                <th class="hidden md:table-cell px-6 py-3 text-right text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($users as $user)
                                @php $profileUrl = route('admin.users.show', $user); @endphp
                                <tr
                                    class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer"
                                    data-profile-url="{{ $profileUrl }}"
                                    title="Open user profile"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full overflow-hidden bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                                    @if($user->photo_url)
                                                        <img
                                                            src="{{ (is_string($user->photo_url) && str_starts_with($user->photo_url, 'http')) ? $user->photo_url : asset($user->photo_url) }}"
                                                            alt="{{ $user->name }}"
                                                            class="h-full w-full object-cover"
                                                        >
                                                    @else
                                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ $profileUrl }}" class="text-sm font-medium text-slate-900 dark:text-slate-50 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors" data-row-ignore-click>
                                                    {{ $user->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <a href="{{ $profileUrl }}" class="text-sm text-slate-900 dark:text-slate-50 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors" data-row-ignore-click>
                                            {{ $user->email }}
                                        </a>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 dark:text-slate-50">
                                            @if($user->phone)
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    <span>{{ $user->phone }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">Not provided</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($user->role === 'admin') bg-indigo-100 text-indigo-800
                                            @elseif($user->role === 'president') bg-purple-100 text-purple-800
                                            @else bg-emerald-100 text-emerald-800
                                            @endif">
                                            {{ $user->role === 'superadmin' ? 'Admin' : ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ $profileUrl }}"
                                                class="inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-700 px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 transition"
                                                data-row-ignore-click
                                            >
                                                View Profile
                                            </a>

                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-emerald-600 hover:text-emerald-700 transition-colors" title="Edit User" data-row-ignore-click>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm="Delete this user? This cannot be undone." data-row-ignore-click>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900" title="Delete User">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-slate-400">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="bg-slate-50 dark:bg-slate-800 px-4 py-3 border-t border-slate-200 dark:border-slate-700 sm:px-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- User Profile Modal -->
        <div
            x-show="openModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm"
            @click.self="close()"
        >
            <div
                x-show="openModal"
                x-transition.scale
                class="relative w-full max-w-lg mx-4 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-400">User Profile</p>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50" x-text="user?.name ?? 'User details'"></h3>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-slate-300"
                        @click="close()"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto">
                    <template x-if="user">
                        <div class="px-6 py-5 space-y-4 text-sm text-slate-700 dark:text-slate-300">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full overflow-hidden bg-emerald-600 text-white flex items-center justify-center font-semibold text-lg">
                                    <template x-if="user.photo_url">
                                        <img
                                            :src="user.photo_url"
                                            :alt="user.name || 'User'"
                                            class="h-full w-full object-cover"
                                            x-on:error="$el.style.display='none'"
                                        >
                                    </template>
                                    <span x-show="!user.photo_url" x-text="(user.name || 'U').charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-slate-900 dark:text-slate-50" x-text="user.name"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300" x-text="user.email"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-50" x-text="user.role"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone Number</p>
                                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="user.phone || 'Not provided'"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Member Since</p>
                                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="user.created_at"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Login</p>
                                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="user.last_login"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Preferences</p>
                                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                                        <span x-text="user.notification_pref ? 'Email notifications: On' : 'Email notifications: Off'"></span><br>
                                        <span x-text="user.dark_mode ? 'Dark mode: Enabled' : 'Dark mode: Disabled'"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex items-center justify-between">
                    <button
                        type="button"
                        class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300 dark:hover:text-slate-200 hover:text-slate-800"
                        @click="close()"
                    >
                        Close
                    </button>
                    <template x-if="user">
                        <a
                            :href="`{{ url('/admin/users') }}/${user.id}/edit`"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
                        >
                            Edit User
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function usersModal() {
            return {
                openModal: false,
                user: null,
                open(payload) {
                    this.user = payload;
                    this.openModal = true;
                },
                close() {
                    this.openModal = false;
                    this.user = null;
                }
            }
        }

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

