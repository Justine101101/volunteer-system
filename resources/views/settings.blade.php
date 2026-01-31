<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-lions-green leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-lions-green-lighter border border-lions-green text-lions-green px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="bg-lions-green-lighter border-b border-lions-green px-6 py-8">
                    <h1 class="text-2xl font-bold text-lions-green">Account Settings</h1>
                    <p class="text-gray-600 mt-2">Manage your profile and preferences</p>
                </div>

                <!-- Settings Form -->
                <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-8">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                    <div x-data="{init(){ $el.scrollIntoView({behavior:'smooth', block:'start'}); $el.focus(); }}" tabindex="-1" class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="assertive">
                        <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
                        <ul class="list-disc ms-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Profile Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-lions-green mb-4">Profile Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', auth()->user()->name) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('name') border-red-500 @enderror"
                                       required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('email') border-red-500 @enderror"
                                       required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div>
                        <h2 class="text-lg font-semibold text-lions-green mb-4">Change Password</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('current_password') border-red-500 @enderror" aria-invalid="{{ $errors->has('current_password') ? 'true' : 'false' }}">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" 
                                           id="password" 
                                           name="password"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('password') border-red-500 @enderror" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent" aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences -->
                    <div>
                        <h2 class="text-lg font-semibold text-lions-green mb-4">Preferences</h2>
                        <div class="space-y-4">
                            <!-- Notification Preference -->
                            <div class="flex items-center justify-between p-4 bg-lions-green-lighter rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Email Notifications</h3>
                                    <p class="text-sm text-gray-600">Receive notifications about events and updates</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="notification_pref" 
                                           value="1"
                                           {{ old('notification_pref', auth()->user()->notification_pref) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-lions-green rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-lions-green"></div>
                                </label>
                            </div>

                            <!-- Dark Mode Preference -->
                            <div class="flex items-center justify-between p-4 bg-lions-green-lighter rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Dark Mode</h3>
                                    <p class="text-sm text-gray-600">Switch to dark theme for better viewing</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           name="dark_mode" 
                                           value="1"
                                           {{ old('dark_mode', auth()->user()->dark_mode) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-lions-green rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-lions-green"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-lions-green mb-4">Account Information</h2>
                        <div class="bg-lions-green-lighter p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Role</span>
                                    <p class="text-sm text-lions-green font-semibold capitalize">{{ auth()->user()->role }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Member Since</span>
                                    <p class="text-sm text-lions-green font-semibold">{{ auth()->user()->created_at->format('F j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-6 border-t border-lions-green">
                        <button type="submit" 
                                class="bg-lions-green text-white px-8 py-3 rounded-lg hover:bg-lions-green-light transition duration-300 font-semibold">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
