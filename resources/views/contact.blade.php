<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Us') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <div class="py-12" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2" data-letter-pop>Get In Touch</h1>
                <p class="text-lg" style="color: #90EE90;" data-word-fade>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>

    <div class="py-16 bg-white" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div x-data="{init(){ $el.scrollIntoView({behavior:'smooth', block:'start'}); $el.focus(); }}" tabindex="-1" class="mb-6 rounded-md border-2 border-red-400 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="assertive">
                    <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-6 rounded-lg border-2 px-4 py-3 text-sm font-medium" style="background-color: #d4edda; border-color: #1a5f3f; color: #1a5f3f;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;" data-animate>
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mb-2" style="color: #1a5f3f;">Send us a Message</h2>
                        <p class="text-center text-gray-600">Fill out the form below and we'll get back to you</p>
                    </div>
                    
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Name</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition @error('name') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                       style="focus:ring-color: #1a5f3f;"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Email</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition @error('email') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium mb-2" style="color: #1a5f3f;">Message</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6"
                                          class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition @error('message') border-red-500 @else border-gray-300 focus:border-[#1a5f3f] @enderror"
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full py-3 px-6 rounded-lg transition duration-300 font-semibold text-white shadow-lg hover:shadow-xl transform hover:scale-105"
                                    style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-8">
                    <!-- Contact Details -->
                    <div class="bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #4a1a5f;" data-animate>
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8" style="color: #4a1a5f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-center mb-2" style="color: #4a1a5f;">Contact Information</h2>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-start p-4 rounded-lg" style="background-color: rgba(26, 95, 63, 0.05);">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1" style="color: #1a5f3f;">Email</h3>
                                    <p class="text-gray-700">info@cordilleraadivaylions.org</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-lg" style="background-color: rgba(74, 26, 95, 0.05);">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6" style="color: #4a1a5f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1" style="color: #4a1a5f;">Phone</h3>
                                    <p class="text-gray-700">+63 917 123 4567</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-lg" style="background-color: rgba(26, 95, 63, 0.05);">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1" style="color: #1a5f3f;">Address</h3>
                                    <p class="text-gray-700">
                                        Baguio City, Benguet<br>
                                        Cordillera Administrative Region<br>
                                        Philippines
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Hours -->
                    <div class="bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;" data-animate>
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-center mb-2" style="color: #1a5f3f;">Office Hours</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 rounded-lg" style="background-color: rgba(26, 95, 63, 0.05);">
                                <span class="text-gray-700 font-medium">Monday - Friday</span>
                                <span class="font-semibold" style="color: #1a5f3f;">9:00 AM - 5:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg" style="background-color: rgba(74, 26, 95, 0.05);">
                                <span class="text-gray-700 font-medium">Saturday</span>
                                <span class="font-semibold" style="color: #4a1a5f;">9:00 AM - 12:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg" style="background-color: rgba(26, 95, 63, 0.05);">
                                <span class="text-gray-700 font-medium">Sunday</span>
                                <span class="font-semibold" style="color: #1a5f3f;">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="mt-16" data-animate>
                <div class="bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;" data-animate>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2" style="color: #1a5f3f;">Find Us</h2>
                        <p class="text-gray-600">Visit our location in Baguio City, Benguet</p>
                    </div>
                    <div class="rounded-lg overflow-hidden shadow-lg border-2" style="border-color: #1a5f3f;">
                        <img src="{{ asset('images/partners/map.jpg') }}" 
                             alt="Location Map" 
                             class="w-full h-96 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
