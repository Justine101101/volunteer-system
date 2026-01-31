<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-lions-purple leading-tight">
                {{ __('Create New Event') }}
            </h2>
            <a href="{{ route('events.index') }}" 
               class="text-lions-purple hover:text-gray-900">
                ← Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('title') border-red-500 @enderror"
                                   placeholder="Enter event title"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Describe the event details, what volunteers will be doing, etc."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                                <input type="date" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('date') border-red-500 @enderror"
                                       required>
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Event Time</label>
                                <input type="time" 
                                       id="time" 
                                       name="time" 
                                       value="{{ old('time') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('time') border-red-500 @enderror"
                                       required>
                                @error('time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lions-green focus:border-transparent @error('location') border-red-500 @enderror"
                                   placeholder="Enter event location"
                                   required>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Photo -->
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Event Photo</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-lions-green-lighter file:text-lions-green
                                              hover:file:bg-lions-green-light
                                              @error('photo') border-red-500 @enderror"
                                       onchange="handleImageSelect(event)">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Upload an event photo (JPG, PNG, GIF - Max 2MB). You can crop the image after selecting.</p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                                <img id="preview" src="" alt="Preview" class="w-full max-w-md h-64 object-cover rounded-lg border-4 border-lions-purple shadow-lg">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('events.index') }}" 
                               class="px-6 py-3 border border-lions-purple text-lions-purple rounded-lg hover:bg-gray-50 transition duration-300">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-lions-green text-white rounded-lg hover:bg-lions-green-light transition duration-300 font-semibold">
                                Create Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    
    <!-- Cropper Modal -->
    <div id="cropModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full m-4 max-h-[90vh] overflow-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Crop Event Photo</h3>
                    <button type="button" onclick="closeCropModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <img id="cropImage" src="" alt="Crop" style="max-width: 100%; max-height: 500px;">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeCropModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="cropImage()" class="px-4 py-2 bg-lions-green text-white rounded-lg hover:bg-lions-green-light">
                        Crop & Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    
    <script>
        let cropper = null;
        let originalFileInput = null;

        function handleImageSelect(event) {
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

            originalFileInput = input;
            
            // Read the file and show crop modal
            const reader = new FileReader();
            reader.onload = function(e) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = e.target.result;
                
                // Show modal
                document.getElementById('cropModal').classList.remove('hidden');
                document.getElementById('cropModal').classList.add('flex');
                
                // Initialize cropper
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    aspectRatio: 16 / 9,
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
            document.getElementById('cropModal').classList.remove('flex');
            
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            
            // Reset file input
            if (originalFileInput) {
                originalFileInput.value = '';
            }
        }

        function cropImage() {
            if (!cropper) {
                return;
            }

            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 1200,
                height: 675,
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
                const file = new File([blob], originalFileInput.files[0].name, {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                // Create a new DataTransfer object and add the file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Set the file input files to the cropped file
                originalFileInput.files = dataTransfer.files;

                // Update preview
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                preview.src = canvas.toDataURL('image/jpeg');
                previewContainer.classList.remove('hidden');

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
</x-app-layout>
