<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Member') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <div class="py-8" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-10 h-10" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Add New Member</h1>
                <p class="text-lg" style="color: #90EE90;">Create a new member profile for the organization</p>
            </div>
        </div>
    </div>

    <div class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('members.index') }}" 
                   class="inline-flex items-center text-sm font-medium transition-colors duration-300"
                   style="color: #1a5f3f; hover:color: #2d7a5a;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Members
                </a>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;">
                <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Member Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Member Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-3 border-2 rounded-lg transition-all duration-300 @error('name') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                   placeholder="e.g., Ln Eugene P. Balway"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Member Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Role/Position</label>
                            <input type="text" 
                                   id="role" 
                                   name="role" 
                                   value="{{ old('role') }}"
                                   class="w-full px-4 py-3 border-2 rounded-lg transition-all duration-300 @error('role') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                   placeholder="e.g., President, First Vice President, Secretary"
                                   required>
                            <p class="mt-1 text-sm" style="color: #4a1a5f;">
                                <span class="font-medium">Note:</span> Some positions can only have one member (e.g., President, First Vice President, Secretary, Treasurer, etc.)
                            </p>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order/Position -->
                        <div>
                            <label for="order" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Display Order</label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', 0) }}"
                                   min="0"
                                   class="w-full px-4 py-3 border-2 rounded-lg transition-all duration-300 @error('order') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                   placeholder="0">
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first. Default is 0.</p>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Upload -->
                        <div>
                            <label for="photo" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Member Photo</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              @error('photo') border-red-500 @enderror"
                                       style="file:background-color: #1a5f3f; file:color: white; hover:file:background-color: #2d7a5a;"
                                       onchange="previewImage(event)">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Upload a photo (JPG, PNG, GIF - Max 2MB)</p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden">
                                <p class="text-sm font-medium mb-2" style="color: #1a5f3f;">Photo Preview:</p>
                                <div class="w-32 h-32 rounded-full bg-yellow-300 flex items-center justify-center border-4 border-white shadow-lg overflow-hidden">
                                    <img id="preview" src="" alt="Preview" class="w-full h-full rounded-full object-cover">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t-2" style="border-color: #1a5f3f;">
                            <a href="{{ route('members.index') }}" 
                               class="px-6 py-3 border-2 rounded-lg font-medium transition-all duration-300 hover:shadow-md"
                               style="border-color: #1a5f3f; color: #1a5f3f; hover:background-color: rgba(26, 95, 63, 0.1);">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 rounded-lg font-semibold text-white transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                                    style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                                Add Member
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

