<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-emerald-600 leading-tight">
                {{ __('Edit Event') }}
            </h2>
            <a href="{{ route('events.index') }}" 
               class="text-emerald-600 hover:text-emerald-600-light">
                ← Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                {{-- Use the explicit Supabase UUID passed from the controller to avoid missing-parameter errors --}}
                <form method="POST" action="{{ route('events.update', ['eventId' => $eventId ?? $event->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $event->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('title') border-red-500 @enderror"
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
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Describe the event details, what volunteers will be doing, etc."
                                      required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Organizer / Venue -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Organizer</label>
                                <input type="text"
                                       id="organizer"
                                       name="organizer"
                                       value="{{ old('organizer', $event->organizer) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('organizer') border-red-500 @enderror"
                                       placeholder="Enter organizer name">
                                @error('organizer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                                <input type="text"
                                       id="venue"
                                       name="venue"
                                       value="{{ old('venue', $event->venue) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('venue') border-red-500 @enderror"
                                       placeholder="Enter venue">
                                @error('venue')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div>
                            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                            <textarea id="requirements"
                                      name="requirements"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('requirements') border-red-500 @enderror"
                                      placeholder="List what volunteers should prepare (e.g. gloves, IDs, water)">{{ old('requirements', $event->requirements) }}</textarea>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date and Time -->
                        @php
                            $rawTime = $event->time ?? '';
                            $parsedStart = $event->start_time ?? null;
                            $parsedEnd = $event->end_time ?? null;
                            if (is_string($rawTime) && str_contains($rawTime, ' - ')) {
                                [$parsedStart, $parsedEnd] = explode(' - ', $rawTime, 2);
                            }
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                                <input type="date" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date', optional($event->date)->format('Y-m-d')) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('date') border-red-500 @enderror"
                                       required>
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                <input type="time" 
                                       id="start_time" 
                                       name="start_time" 
                                       value="{{ old('start_time', $parsedStart) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('start_time') border-red-500 @enderror"
                                       required>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                <input type="time" 
                                       id="end_time" 
                                       name="end_time" 
                                       value="{{ old('end_time', $parsedEnd) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('end_time') border-red-500 @enderror"
                                       required>
                                @error('end_time')
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
                                   value="{{ old('location', $event->location) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-transparent @error('location') border-red-500 @enderror"
                                   placeholder="Enter event location"
                                   required>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Event Photo -->
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $event->photo_url ? 'Update Event Photo' : 'Event Photo' }}
                            </label>
                            
                            @if($event->photo_url)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $event->photo_url }}" 
                                             alt="{{ $event->title }}" 
                                             class="w-full max-w-md h-64 object-cover rounded-lg border-4 border-emerald-600 shadow-lg">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Upload a new photo below to replace it</p>
                                </div>
                            @endif
                            
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-emerald-600-lighter file:text-emerald-600
                                              hover:file:bg-emerald-600-light
                                              @error('photo') border-red-500 @enderror"
                                       onchange="previewImage(event)">
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Upload an event photo (JPG, PNG, GIF - Max 2MB). Leave empty to keep current photo.</p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden">
                                <p class="text-sm text-gray-600 mb-2">New photo preview:</p>
                                <img id="preview" src="" alt="Preview" class="w-full max-w-md h-64 object-cover rounded-lg border-4 border-emerald-600 shadow-lg">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-emerald-600">
                            <a href="{{ route('events.index') }}" 
                               class="px-6 py-3 border border-emerald-600 text-emerald-600 rounded-lg hover:bg-emerald-600-lighter transition duration-300">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-600-light transition duration-300 font-semibold">
                                Update Event
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
