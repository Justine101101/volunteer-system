<x-app-layout>
    <div class="min-h-screen bg-gray-100">
        <!-- Profile Header -->
        <div class="bg-purple-900 text-white py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h1 class="text-xl font-semibold">Profile</h1>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- User Overview Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    @if($user->photo_url)
                        <img src="{{ asset($user->photo_url) }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover shadow-lg border-4 border-purple-200">
                    @else
                        <div class="w-24 h-24 bg-purple-500 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    <!-- User Info -->
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-lg text-gray-600 mt-1">Role: {{ ucfirst($user->role ?? 'User') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Profile & Contact Information Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
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
