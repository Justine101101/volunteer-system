<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('patch')

    <!-- Photo Upload -->
    <div>
        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
        <div class="flex items-center gap-4">
            @if($user->photo_url)
                <img src="{{ asset($user->photo_url) }}" 
                     alt="Current photo" 
                     id="currentPhoto"
                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 shadow-md">
            @else
                <div id="currentPhoto" class="w-20 h-20 bg-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold border-2 border-gray-300 shadow-md">
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
                              file:bg-purple-50 file:text-purple-700
                              hover:file:bg-purple-100
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
            <img id="preview" src="" alt="Preview" class="w-20 h-20 rounded-full object-cover border-2 border-purple-300 shadow-md">
        </div>
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
        <input id="name" 
               name="name" 
               type="text" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror" 
               value="{{ old('name', $user->name) }}" 
               required 
               autofocus 
               autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <input id="email" 
               name="email" 
               type="email" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror" 
               value="{{ old('email', $user->email) }}" 
               required 
               autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-sm text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif

        <p class="mt-2 text-sm text-green-600">
            Note: Contact support to change the primary email address
        </p>
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" 
                class="px-6 py-3 bg-purple-900 text-white font-semibold rounded-lg hover:bg-purple-800 transition duration-300 flex items-center gap-2 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            SAVE CHANGES
        </button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-600 font-medium"
            >{{ __('Saved.') }}</p>
        @endif
    </div>
</form>

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
                <button type="button" onclick="cropProfileImage()" class="px-4 py-2 bg-purple-900 text-white rounded-lg hover:bg-purple-800">
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
