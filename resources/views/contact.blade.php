<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-dark leading-tight">
            {{ __('Contact Us') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <section class="py-16 bg-light-gray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">Get In Touch</h1>
                <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div x-data="{init(){ $el.scrollIntoView({behavior:'smooth', block:'start'}); $el.focus(); }}" tabindex="-1" class="mb-8 rounded-2xl border-2 border-red-400 bg-red-50 px-6 py-4 text-sm text-red-800" role="alert" aria-live="assertive">
                    <div class="font-semibold mb-2">{{ __('Please fix the following errors:') }}</div>
                    <ul class="list-disc ms-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-8 rounded-2xl border-2 px-6 py-4 text-sm font-medium bg-green-50 border-green-500 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-3xl shadow-soft-lg p-8 md:p-10">
                    <div class="mb-8">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mb-3 text-slate-dark">Send us a Message</h2>
                        <p class="text-center text-gray-600">Fill out the form below and we'll get back to you</p>
                    </div>
                    
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold mb-3 text-gray-700">Name</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-lions-green/20 focus:border-lions-green transition-all duration-200 bg-white"
                                       required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold mb-3 text-gray-700">Email</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-lions-green/20 focus:border-lions-green transition-all duration-200 bg-white"
                                       required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-semibold mb-3 text-gray-700">Message</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6"
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-lions-green/20 focus:border-lions-green transition-all duration-200 bg-white resize-none"
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 px-6 rounded-2xl transition duration-300 font-semibold text-white shadow-soft hover:shadow-soft-lg transform hover:scale-105 bg-lions-green hover:bg-lions-green/90 text-lg">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-8">
                    <!-- Contact Details -->
                    <div class="bg-lions-green/5 rounded-3xl shadow-soft-lg p-8 md:p-10">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                                <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-center mb-3 text-slate-dark">Contact Information</h2>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-start p-4 rounded-2xl bg-white shadow-soft">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-lions-green/10 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1 text-slate-dark">Email</h3>
                                    <p class="text-gray-600">beltranjerek@gmail.com</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-2xl bg-white shadow-soft">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-lions-green/10 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1 text-slate-dark">Phone</h3>
                                    <p class="text-gray-600">+63 917 123 4567</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-2xl bg-white shadow-soft">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-lions-green/10 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold mb-1 text-slate-dark">Address</h3>
                                    <p class="text-gray-600">
                                        Baguio City, Benguet<br>
                                        Cordillera Administrative Region<br>
                                        Philippines
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Hours -->
                    <div class="bg-lions-green/5 rounded-3xl shadow-soft-lg p-8 md:p-10">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                                <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-center mb-3 text-slate-dark">Office Hours</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-white shadow-soft">
                                <span class="text-gray-700 font-medium">Monday - Friday</span>
                                <span class="font-semibold text-lions-green">9:00 AM - 5:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-white shadow-soft">
                                <span class="text-gray-700 font-medium">Saturday</span>
                                <span class="font-semibold text-lions-green">9:00 AM - 12:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-4 rounded-2xl bg-white shadow-soft">
                                <span class="text-gray-700 font-medium">Sunday</span>
                                <span class="font-semibold text-lions-green">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="mt-16">
                <div class="bg-white rounded-3xl shadow-soft-lg p-8 md:p-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-3 text-slate-dark">Find Us</h2>
                        <p class="text-gray-600">Visit our location in Baguio City, Benguet</p>
                    </div>
                    <div class="rounded-2xl overflow-hidden shadow-soft border-2 border-lions-green/20">
                        <img src="{{ asset('images/partners/map.jpg') }}" 
                             alt="Location Map" 
                             class="w-full h-96 object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
