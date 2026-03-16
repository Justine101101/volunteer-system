<x-app-layout>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- User Overview Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-6 transition-colors duration-200">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    @if(($user->isAdmin() || $user->isSuperAdmin()) || !$user->photo_url)
                        <div class="w-24 h-24 bg-emerald-500 dark:bg-emerald-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @else
                        <img src="{{ asset($user->photo_url) }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover shadow-lg border-4 border-emerald-200 dark:border-emerald-700">
                    @endif
                    <!-- User Info -->
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mt-1">Role: {{ ucfirst($user->role ?? 'User') }}</p>
                        @if($user->phone)
                            <div class="flex items-center gap-2 mt-2">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <p class="text-base text-gray-700 dark:text-gray-300">{{ $user->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-400 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile & Contact Information Card -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Profile & Contact Information
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Update your account's profile information and email address.</p>
                    
                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
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
                                    <img src="{{ asset($user->photo_url) }}" 
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

                        <!-- Preferences -->
                        <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                            <h3 class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mb-4">Preferences</h3>
                            <div class="space-y-4">
                                <!-- Notification Preference -->
                                <div class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
                                    <div>
                                        <h4 class="text-sm font-medium text-slate-900 dark:text-slate-100">Email Notifications</h4>
                                        <p class="text-sm text-slate-600 dark:text-slate-400">Receive notifications about events and updates</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="notification_pref" 
                                               value="1"
                                               {{ old('notification_pref', $user->notification_pref) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
                                </div>

                                <!-- Dark Mode Preference -->
                                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div>
                                        <h4 class="text-sm font-medium text-slate-900 dark:text-slate-100">Dark Mode</h4>
                                        <p class="text-sm text-slate-600 dark:text-slate-400">Switch to dark theme for better viewing</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="dark_mode" 
                                               value="1"
                                               id="darkModeToggle"
                                               {{ old('dark_mode', $user->dark_mode) ? 'checked' : '' }}
                                               class="sr-only peer"
                                               onchange="window.DarkMode?.apply(this.checked)">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
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

        function closeCropModal() {
            document.getElementById('cropModal').classList.add('hidden');
            
            if (profileCropper) {
                profileCropper.destroy();
                profileCropper = null;
            }
            
            // Reset file input
            if (originalProfileFileInput) {
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
                closeCropModal();
            }, 'image/jpeg', 0.9);
        }

        // Close modal on outside click
        document.getElementById('cropModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCropModal();
            }
        });
    </script>
    @endif
</x-app-layout>
