<x-app-layout>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ section: 'profile' }">
            <!-- Header / User summary -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($user->isAdmin() || !$user->photo_url)
                        <div class="w-14 h-14 bg-emerald-600 rounded-full flex items-center justify-center text-white text-2xl font-semibold shadow-md">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @else
                        <img src="{{ (is_string($user->photo_url) && str_starts_with($user->photo_url, 'http')) ? $user->photo_url : asset($user->photo_url) }}"
                             alt="{{ $user->name }}"
                             class="w-14 h-14 rounded-full object-cover shadow-md border-2 border-emerald-200">
                    @endif
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Volunteer Management System</p>
                        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $user->isAdmin() ? 'Admin Settings' : 'Volunteer Settings' }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Signed in as <span class="font-medium text-slate-800 dark:text-slate-200">{{ $user->name }}</span> · {{ ucfirst($user->role ?? 'user') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 border border-emerald-100">
                        System status: Operational
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-400 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left settings navigation -->
                <aside class="lg:col-span-3">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 space-y-1">
                        <p class="px-2 text-xs font-semibold tracking-wide text-slate-400 uppercase">Settings</p>
                        <button type="button"
                                @click="section = 'profile'"
                                :class="section === 'profile' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
                                </svg>
                                Profile
                            </span>
                        </button>

                        <button type="button"
                                @click="section = 'security'"
                                :class="section === 'security' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3V5a3 3 0 10-6 0v3c0 1.657 1.343 3 3 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11h14v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8z" />
                                </svg>
                                Security & 2FA
                            </span>
                        </button>

                        @if($user->isAdmin())
                        <button type="button"
                                @click="section = 'system'"
                                :class="section === 'system' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3a1 1 0 00-.96.73L8.03 7H4a1 1 0 100 2h3.528l-.6 3H4a1 1 0 100 2h2.528l-.778 3.889A1 1 0 006.73 19h2.52a1 1 0 00.97-.757L11.97 9h4.03a1 1 0 100-2h-3.528l.6-3H20a1 1 0 100-2H9.75z" />
                                </svg>
                                System & Website
                            </span>
                        </button>

                        <button type="button"
                                @click="section = 'smtp'"
                                :class="section === 'smtp' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                SMTP & Notifications
                            </span>
                        </button>

                        <button type="button"
                                @click="section = 'backup'"
                                :class="section === 'backup' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l3-3m-3 3l-3-3M5 10a7 7 0 0114 0v4a5 5 0 01-5 5H9a5 5 0 01-5-5v-4z" />
                                </svg>
                                Backup & Recovery
                            </span>
                        </button>

                        @endif

                        <button type="button"
                                @click="section = 'appearance'"
                                :class="section === 'appearance' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-700 hover:bg-slate-50 border-transparent'"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-xl border transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M8.05 17.95l-1.414 1.414M18.364 18.364l-1.414-1.414M8.05 6.05L6.636 4.636" />
                                </svg>
                                Appearance & Preferences
                            </span>
                        </button>
                    </div>
                </aside>

                <!-- Right content area -->
                <div class="lg:col-span-9 space-y-6">
                    <!-- Profile Settings -->
                    <section x-show="section === 'profile'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Profile Settings</h2>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your personal information and sign-in details.</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="mt-4 space-y-6">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                        <div class="rounded-md border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-800 dark:text-red-300" role="alert">
                            <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
                            <ul class="list-disc ms-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Photo Upload (Only for Volunteers) -->
                        @if($user->isVolunteer())
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Photo</label>
                            <div class="flex items-center gap-4">
                                @if($user->photo_url)
                                    <img src="{{ (is_string($user->photo_url) && str_starts_with($user->photo_url, 'http')) ? $user->photo_url : asset($user->photo_url) }}" 
                                         alt="Current photo" 
                                         id="currentPhoto"
                                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 shadow-md">
                                @else
                                    <div id="currentPhoto" class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-gray-300 shadow-md">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" 
                                           id="photo" 
                                           name="photo" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-lg file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-emerald-50 file:text-emerald-700
                                                  hover:file:bg-emerald-100
                                                  @error('photo') border-red-500 @enderror"
                                           onchange="handleProfilePhotoSelect(event)">
                                    <p class="mt-2 text-sm text-gray-500">Upload a photo (JPG, PNG, GIF - Max 2MB). You can crop the image after selecting.</p>
                                    @error('photo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <!-- Preview -->
                            <div id="photoPreview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <img id="preview" src="" alt="Preview" class="w-20 h-20 rounded-full object-cover border-2 border-emerald-300 shadow-md">
                            </div>
                        </div>
                        @else
                        <!-- Default Avatar Info for Admins -->
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 bg-emerald-500 dark:bg-emerald-600 rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-gray-300 dark:border-slate-600 shadow-md">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile Avatar</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Admins use default avatar with initials</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                   value="{{ old('name', $user->name) }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name" />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                   value="{{ old('email', $user->email) }}" 
                                   required 
                                   autocomplete="username" />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-sm text-gray-800">
                                        {{ __('Your email address is unverified.') }}
                                        <a href="{{ route('verification.send') }}" class="underline text-sm text-emerald-600 hover:text-emerald-900">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                            <input id="phone"
                                   name="phone"
                                   type="text"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                   value="{{ old('phone', $user->phone ?? '') }}"
                                   autocomplete="tel" />
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Example: +63 912 345 6789</p>
                        </div>

                        <!-- Password Section -->
                        <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mb-4">Change Password</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                                    <input type="password" 
                                           id="current_password" 
                                           name="current_password"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('current_password') border-red-500 @enderror" />
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                                        <input type="password" 
                                               id="password" 
                                               name="password"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('password') border-red-500 @enderror" />
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                        <input type="password" 
                                               id="password_confirmation" 
                                               name="password_confirmation"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mb-4">Account Information</h3>
                            <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Role</span>
                                        <p class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold capitalize mt-1">{{ $user->role }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Member Since</span>
                                        <p class="text-sm text-emerald-600 dark:text-emerald-400 font-semibold mt-1">{{ $user->created_at->format('F j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                            <button type="submit" 
                                    class="px-6 py-3 bg-emerald-600 dark:bg-emerald-700 text-white font-semibold rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition duration-300 flex items-center gap-2 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                SAVE CHANGES
                            </button>
                        </div>
                            </form>
                        </div>

                        @if(isset($participationStats) && $participationStats)
                            <!-- Volunteer Participation / Statistics Card -->
                            @php
                                $ps = $participationStats;
                                $stats = $ps['stats'] ?? [];
                                $analytics = $ps['analytics'] ?? [];
                                $quick = $ps['quickStats'] ?? [];
                            @endphp
                            <div class="mt-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        My Participation
                                    </h3>
                                </div>
                                <p class="text-xs text-slate-500 mb-4">
                                    Overview of your volunteer engagement and approvals across events.
                                </p>

                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-3">
                                        <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wide">Approved Events</p>
                                        <p class="mt-1 text-2xl font-bold text-emerald-900">
                                            {{ $stats['approved_registrations'] ?? 0 }}
                                        </p>
                                        <p class="mt-1 text-[11px] text-emerald-800/80">Confirmed registrations</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                                        <p class="text-[11px] font-semibold text-slate-700 uppercase tracking-wide">Participation Rate</p>
                                        <p class="mt-1 text-2xl font-bold text-slate-900">
                                            {{ $analytics['participation_rate'] ?? 0 }}%
                                        </p>
                                        <p class="mt-1 text-[11px] text-slate-600">Of available events</p>
                                    </div>
                                </div>

                                <dl class="grid grid-cols-2 gap-3 text-sm text-slate-700">
                                    <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-[11px] text-slate-500 uppercase tracking-wide">Total Registrations</dt>
                                        <dd class="mt-1 text-base font-semibold">{{ $stats['total_registrations'] ?? 0 }}</dd>
                                    </div>
                                    <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-[11px] text-slate-500 uppercase tracking-wide">Pending Decisions</dt>
                                        <dd class="mt-1 text-base font-semibold">{{ $stats['pending_registrations'] ?? 0 }}</dd>
                                    </div>
                                    <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-[11px] text-slate-500 uppercase tracking-wide">Events This Month</dt>
                                        <dd class="mt-1 text-base font-semibold">{{ $quick['events_this_month'] ?? 0 }}</dd>
                                    </div>
                                    <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                                        <dt class="text-[11px] text-slate-500 uppercase tracking-wide">My Registrations (Month)</dt>
                                        <dd class="mt-1 text-base font-semibold">{{ $quick['my_registrations_this_month'] ?? 0 }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @endif
                    </section>

                    <!-- Security & 2FA -->
                    <section x-show="section === 'security'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Security Settings</h2>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Strengthen account protection for admins and volunteers.</p>
                                </div>
                            </div>

                            <!-- 2FA card -->
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Two-Factor Authentication (2FA)</h3>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Adds an extra verification step using a 6-digit OTP sent to your email.
                                    </p>
                                    @if($errors->has('two_factor'))
                                        <p class="mt-2 text-[12px] text-red-600 dark:text-red-400 font-medium">
                                            {{ $errors->first('two_factor') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            disabled
                                            class="sr-only peer"
                                            {{ ($user->two_factor_enabled ?? false) ? 'checked' : '' }}
                                        >
                                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full peer peer-checked:bg-emerald-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500/40 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow after:transition-all peer-checked:after:translate-x-full"></div>
                                    </label>
                                    @if(!($user->two_factor_enabled ?? false))
                                        <form method="POST" action="{{ route('two_factor.setup.start') }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                                                Configure 2FA
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('two_factor.disable') }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                                                Disable 2FA
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Active sessions (placeholder) -->
                            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Active Sessions</h3>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Monitor recent sign-ins and sign out devices you do not recognize.</p>
                                    </div>
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                                        Sign out all sessions
                                    </button>
                                </div>
                                <p class="text-xs text-slate-500">
                                    <!-- TODO: Populate table from a security_sessions table or Laravel session store. -->
                                    Session tracking is not yet connected. Implement in backend before using in production.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- System & Website Configuration (Admin only) -->
                    @if($user->isAdmin())
                    <section x-show="section === 'system'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Website & System Configuration</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">High-level settings that affect the entire Volunteer Management System.</p>
                            </div>

                            <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">System Name</label>
                                        <input type="text" name="system_name" value="{{ old('system_name', config('app.name', 'Volunteer Management System')) }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                        <p class="mt-1 text-xs text-slate-500">Displayed in navigation bars, browser title, and email templates.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Organization Name</label>
                                        <input type="text" name="organization_name" value="{{ old('organization_name') }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                        <p class="mt-1 text-xs text-slate-500">Used in documents, SMS, and public pages. (Requires backend persistence.)</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Default Timezone</label>
                                        <select name="timezone" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <!-- TODO: Populate from config('app.timezone') and list of PHP timezones. -->
                                            <option value="Asia/Manila">Asia/Manila (UTC+8)</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                        <p class="mt-1 text-xs text-slate-500">Affects event times, reports and logs.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Registration Mode</label>
                                        <select name="registration_mode" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <option value="open">Open – anyone can sign up</option>
                                            <option value="approval">Admin approval required</option>
                                            <option value="invite">Invite only</option>
                                        </select>
                                        <p class="mt-1 text-xs text-slate-500">Controls how new volunteers can register.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Maintenance Mode</label>
                                        <select name="maintenance_mode" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                            <option value="off">Off (live)</option>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="on">Force maintenance</option>
                                        </select>
                                        <p class="mt-1 text-xs text-slate-500">UI-only toggle. Hook into `php artisan down` / `up` for full control.</p>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2 border-t border-slate-200 dark:border-slate-700 mt-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                                        Save system settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- SMTP & Notifications (Admin only) -->
                    <section x-show="section === 'smtp'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">SMTP & Email Delivery</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure outbound email for notifications, password resets, and reports.</p>
                            </div>

                            <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">SMTP Host</label>
                                        <input type="text" name="mail_host" value="{{ old('mail_host', env('MAIL_HOST')) }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Port</label>
                                            <input type="number" name="mail_port" value="{{ old('mail_port', env('MAIL_PORT')) }}"
                                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Encryption</label>
                                            <select name="mail_encryption" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                                <option value="tls">TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="">None</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Username</label>
                                        <input type="text" name="mail_username" value="{{ old('mail_username', env('MAIL_USERNAME')) }}"
                                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Password</label>
                                        <input type="password" name="mail_password" value=""
                                               class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                               placeholder="••••••••">
                                        <p class="mt-1 text-xs text-slate-500">Leave blank to keep the existing password.</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">From Address</label>
                                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', env('MAIL_FROM_ADDRESS')) }}"
                                           class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-slate-500">Address used as sender for all system emails.</p>
                                </div>

                                <p class="text-[11px] text-slate-400">
                                    <!-- TODO: Persist SMTP changes securely (e.g., in encrypted settings table or config wizard instead of writing .env directly). -->
                                    For a production deployment, never write credentials directly from the UI into the <code>.env</code> file without audit and encryption.
                                </p>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 mt-4">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                                        Send test email
                                    </button>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                                        Save SMTP settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Backup & Recovery (Admin only) -->
                    <section x-show="section === 'backup'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Backup & Recovery</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Plan for data protection and disaster recovery.</p>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-5 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Manual backup</h3>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Download a snapshot of critical configuration and event data.
                                        </p>
                                        <p class="mt-2 text-[11px] text-slate-400">
                                            <!-- TODO: Hook this button to an Artisan command that exports database backups to secure storage. -->
                                            This button is a UI placeholder until a backup job is implemented.
                                        </p>
                                    </div>
                                    <div class="mt-4 flex flex-wrap gap-3">
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                                            Run backup now
                                        </button>
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50">
                                            View backup history
                                        </button>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Automatic backups</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        Configure your preferred backup frequency and retention.
                                    </p>

                                    <form class="mt-4 space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-200 mb-1">Frequency</label>
                                            <select class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                                <option>Daily at 02:00 AM</option>
                                                <option>Weekly (Sunday)</option>
                                                <option>Monthly (1st of month)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-200 mb-1">Retention</label>
                                            <select class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                                <option>Keep last 7 backups</option>
                                                <option>Keep last 30 backups</option>
                                                <option>Keep last 90 backups</option>
                                            </select>
                                        </div>
                                        <p class="text-[11px] text-slate-400">
                                            <!-- TODO: Persist schedule into a jobs/cron configuration or queue-based backup scheduler. -->
                                            Scheduling logic is not wired yet – implement a queued job or cron entry in production.
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endif

                    <!-- Appearance & Preferences -->
                    <section x-show="section === 'appearance'" x-cloak>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Appearance & Preferences</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Customize how the Volunteer Management System looks and feels.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Notifications -->
                                <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-emerald-50/60 dark:bg-emerald-900/20 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Email notifications</h3>
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                                                Receive updates about event approvals, reminders, and announcements.
                                            </p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="notification_pref"
                                                   value="1"
                                                   form=""
                                                   {{ old('notification_pref', $user->notification_pref) ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full peer peer-checked:bg-emerald-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500/40 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow after:transition-all peer-checked:after:translate-x-full"></div>
                                        </label>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        This toggle reflects your current preference; updates are handled in the main Profile form.
                                    </p>
                                </div>

                                <!-- Dark mode -->
                                <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Dark mode</h3>
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                                                Toggle a low-light friendly interface across the dashboard.
                                            </p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="dark_mode"
                                                   value="1"
                                                   id="darkModeToggleSecondary"
                                                   {{ old('dark_mode', $user->dark_mode) ? 'checked' : '' }}
                                                   class="sr-only peer"
                                                   onchange="window.DarkMode?.apply(this.checked)">
                                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 rounded-full peer peer-checked:bg-emerald-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500/40 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow after:transition-all peer-checked:after:translate-x-full"></div>
                                        </label>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        This control mirrors your preference stored in profile settings.
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-2">Theme density & layout</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Choose how compact tables and cards should appear.</p>
                                <div class="flex flex-wrap gap-3">
                                    <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-500 bg-emerald-50 text-emerald-700">
                                        Comfortable
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
                                        Compact
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
                                        Spacious
                                    </button>
                                </div>
                                <p class="mt-3 text-[11px] text-slate-400">
                                    <!-- TODO: Persist layout preferences in user meta table and apply in Blade components. -->
                                    Layout presets are visual only for now; wire them to a user preferences store to make them persistent.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @if($user->isVolunteer())
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">

    <!-- Cropper Modal -->
    <div id="cropModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full m-4 max-h-[90vh] overflow-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Crop Profile Photo</h3>
                    <button type="button" onclick="closeCropModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <img id="cropImage" src="" alt="Crop" style="max-width: 100%; max-height: 400px;">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeCropModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="cropProfileImage()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Crop & Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

    <script>
        let profileCropper = null;
        let originalProfileFileInput = null;

        function handleProfilePhotoSelect(event) {
            const input = event.target;
            const file = input.files[0];
            
            if (!file) {
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                input.value = '';
                return;
            }

            originalProfileFileInput = input;
            
            // Read the file and show crop modal
            const reader = new FileReader();
            reader.onload = function(e) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = e.target.result;
                
                // Show modal
                document.getElementById('cropModal').classList.remove('hidden');
                
                // Initialize cropper
                if (profileCropper) {
                    profileCropper.destroy();
                }
                
                profileCropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            };
            
            reader.readAsDataURL(file);
        }

        function closeCropModal(resetInput = true) {
            document.getElementById('cropModal').classList.add('hidden');
            
            if (profileCropper) {
                profileCropper.destroy();
                profileCropper = null;
            }
            
            // Reset file input
            if (resetInput && originalProfileFileInput) {
                originalProfileFileInput.value = '';
            }
        }

        function cropProfileImage() {
            if (!profileCropper) {
                return;
            }

            // Get cropped canvas (square for profile photo)
            const canvas = profileCropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert canvas to blob
            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert('Failed to crop image');
                    return;
                }

                // Create a new File object from the blob
                const file = new File([blob], originalProfileFileInput.files[0].name, {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                // Create a new DataTransfer object and add the file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Set the file input files to the cropped file
                originalProfileFileInput.files = dataTransfer.files;

                // Update preview
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('photoPreview');
                const currentPhoto = document.getElementById('currentPhoto');
                
                preview.src = canvas.toDataURL('image/jpeg');
                previewContainer.classList.remove('hidden');
                
                // Update current photo preview
                if (currentPhoto.tagName === 'IMG') {
                    currentPhoto.src = canvas.toDataURL('image/jpeg');
                } else {
                    // Replace div with img
                    const newImg = document.createElement('img');
                    newImg.src = canvas.toDataURL('image/jpeg');
                    newImg.alt = 'Current photo';
                    newImg.id = 'currentPhoto';
                    newImg.className = 'w-20 h-20 rounded-full object-cover border-2 border-gray-300 shadow-md';
                    currentPhoto.parentNode.replaceChild(newImg, currentPhoto);
                }

                // Close modal
                // Keep the cropped file in the input so the form submits it.
                closeCropModal(false);
            }, 'image/jpeg', 0.9);
        }

        // Close modal on outside click
        document.getElementById('cropModal').addEventListener('click', function(e) {
            if (e.target === this) {
                // Outside click = cancel, clear the selected file
                closeCropModal(true);
            }
        });
    </script>
    @endif
</x-app-layout>
