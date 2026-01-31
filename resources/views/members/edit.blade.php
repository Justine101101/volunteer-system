<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Member') }}
            </h2>
            <a href="{{ route('members.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                ← Back to Members
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Current Photo Display -->
                        @if($member->photo_url)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
                                <div class="flex items-center gap-4">
                                    <img src="{{ $member->photo_url }}" 
                                         alt="{{ $member->name }}" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Current member photo</p>
                                        <p class="text-xs text-gray-500 mt-1">Upload a new photo below to replace it</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Member Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Member Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $member->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   placeholder="e.g., Ln Eugene P. Balway"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Member Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role/Position</label>
                            <input type="text" 
                                   id="role" 
                                   name="role" 
                                   value="{{ old('role', $member->role) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror"
                                   placeholder="e.g., President, Vice President, Secretary"
                                   required>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order/Position -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', $member->order ?? 0) }}"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('order') border-red-500 @enderror"
                                   placeholder="0">
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first. Default is 0.</p>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Upload -->
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $member->photo_url ? 'Update Photo' : 'Member Photo' }}
                            </label>
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100
                                              @error('photo') border-red-500 @enderror"
                                       onchange="previewImage(event)">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Upload a photo (JPG, PNG, GIF - Max 2MB). Leave empty to keep current photo.</p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">New photo preview:</p>
                                <img id="preview" src="" alt="Preview" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('members.index') }}" 
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Update Member
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

