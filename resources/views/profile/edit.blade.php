<x-app-layout>
    <div class="min-h-screen bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- User Overview Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    @if($user->photo_url)
                        <img src="{{ asset($user->photo_url) }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover shadow-lg border-4 border-emerald-200">
                    @else
                        <div class="w-24 h-24 bg-emerald-500 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    <!-- User Info -->
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-lg text-gray-600 mt-1">Role: {{ ucfirst($user->role ?? 'User') }}</p>
                        @if($user->phone)
                            <div class="flex items-center gap-2 mt-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <p class="text-base text-gray-700">{{ $user->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile & Contact Information Card -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Profile & Contact Information
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">Update your account's profile information and email address.</p>
                    
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Security & Settings Card -->
                <div class="bg-green-50 rounded-lg shadow-md p-6 border border-green-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Security & Settings
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-6">Change your password and update security settings.</p>
                    
                    @include('profile.partials.update-password-form')
                </div>

                @if(isset($participationStats) && $participationStats)
                <!-- Volunteer Participation / Statistics Card -->
                @php
                    $ps = $participationStats;
                    $stats = $ps['stats'] ?? [];
                    $analytics = $ps['analytics'] ?? [];
                    $quick = $ps['quickStats'] ?? [];
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            My Participation
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        A clean overview of your volunteer engagement and event activity.
                    </p>

                    <!-- Key metrics -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-3">
                            <p class="text-xs font-medium text-emerald-700 uppercase tracking-wide">Approved Events</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-900">
                                {{ $stats['approved_registrations'] ?? 0 }}
                            </p>
                            <p class="mt-1 text-xs text-emerald-800/80">Confirmed registrations</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                            <p class="text-xs font-medium text-slate-700 uppercase tracking-wide">Participation Rate</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $analytics['participation_rate'] ?? 0 }}%
                            </p>
                            <p class="mt-1 text-xs text-slate-600">Of available events</p>
                        </div>
                    </div>

                    <!-- Secondary stats -->
                    <dl class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                        <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide">Total Registrations</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $stats['total_registrations'] ?? 0 }}</dd>
                        </div>
                        <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide">Pending Decisions</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $stats['pending_registrations'] ?? 0 }}</dd>
                        </div>
                        <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide">Events This Month</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $quick['events_this_month'] ?? 0 }}</dd>
                        </div>
                        <div class="flex flex-col rounded-lg bg-slate-50 px-3 py-2">
                            <dt class="text-xs text-gray-500 uppercase tracking-wide">My Registrations (Month)</dt>
                            <dd class="mt-1 text-base font-semibold">{{ $quick['my_registrations_this_month'] ?? 0 }}</dd>
                        </div>
                    </dl>
                </div>
                @endif
            </div>

            <!-- Delete User Form (if exists) -->
            @if(file_exists(resource_path('views/profile/partials/delete-user-form.blade.php')))
                <div class="mt-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
