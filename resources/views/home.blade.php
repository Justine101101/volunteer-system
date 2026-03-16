<x-app-layout>
    <!-- Hero Section -->
    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Banner with Text Overlay -->
            <div class="mb-8 rounded-lg overflow-hidden relative">
                <img src="{{ asset('images/partners/banner.jpg') }}" alt="Find Your Purpose - Community Event" class="w-full h-auto object-cover">
                
                <!-- Text Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 lg:p-12">
                    <!-- Main Title -->
                    <div class="mb-4">
                        <div class="inline-block">
                            <span class="bg-emerald-600 text-white px-4 py-2 md:px-6 md:py-3 text-2xl md:text-4xl lg:text-5xl font-bold rounded-lg">Find Your</span>
                            <span class="text-white text-2xl md:text-4xl lg:text-5xl font-bold ml-2 drop-shadow-lg">Purpose</span>
                        </div>
                    </div>
                    
                    <!-- Subtitle Text -->
                    <p class="text-white text-sm md:text-base lg:text-lg drop-shadow-md max-w-2xl">
                        Support local events and connect with us at the Ava Lions Club.<br>
                        Everyone is welcome! #BecomeUnstoppableLions
                    </p>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Join us for a meaningful day of service and giving back!
                </h2>
                <p class="text-lg text-gray-700 mb-8">
                    Be part of our upcoming activities and community programs. It's more than just an event—it's an opportunity to combine your passion for helping others with a mission to make a meaningful difference.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                        Register Now
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 dark:bg-slate-900" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">About Us</h2>
                <p class="text-lg" style="color: #90EE90;">A Chronicle of Service and Community in the Cordillera Region</p>
            </div>
            
            <!-- History -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-white mb-6">Our History</h3>
                <div class="space-y-4" style="color: #90EE90;">
                    <p class="text-lg">
                        In the heart of the Cordillera mountain ranges, known for their breathtaking landscapes and vibrant cultures, a group of driven individuals came together with a shared vision: to serve, empower, and uplift their communities. Thus was born the Cordillera Adivay Lions Club—a beacon of voluntary service and humanitarian commitment in the region.
                    </p>
                    <p>
                        The name "Adivay" reflects our deep connection to the Cordillera culture, meaning "gathering" or "coming together" - which perfectly represents our mission of uniting people for a common cause.
                    </p>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                <div class="p-8 rounded-lg shadow-lg overflow-hidden bg-white dark:bg-slate-800">
                        <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">Our Mission</h3>
                    </div>
                    <p class="text-center text-gray-700 dark:text-gray-300">
                        To empower communities in the Cordillera region through volunteer service, sustainable development projects, and fostering a culture of compassion and unity.
                    </p>
                </div>

                <div class="p-8 rounded-lg shadow-lg overflow-hidden bg-white dark:bg-slate-800">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">Our Vision</h3>
                    </div>
                    <p class="text-center text-gray-700 dark:text-gray-300">
                        A thriving Cordillera region where every community member has access to opportunities, resources, and support needed to lead fulfilling and prosperous lives.
                    </p>
                </div>
            </div>

            <!-- Core Values -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-white mb-8 text-center">Our Core Values</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2 text-emerald-700 dark:text-emerald-400">Compassion</h4>
                        <p class="text-gray-600 dark:text-gray-300">We serve with empathy and understanding, putting the needs of others first.</p>
                    </div>
                    
                    <div class="text-center bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2 text-emerald-700 dark:text-emerald-400">Integrity</h4>
                        <p class="text-gray-600 dark:text-gray-300">We maintain the highest standards of honesty and ethical behavior in all our actions.</p>
                    </div>
                    
                    <div class="text-center bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-2 text-emerald-700 dark:text-emerald-400">Unity</h4>
                        <p class="text-gray-600 dark:text-gray-300">We work together as one team, celebrating diversity and fostering collaboration.</p>
                    </div>
                </div>
            </div>

            <!-- Leadership Team -->
            @if(isset($officers) && $officers->count() > 0)
            <div>
                <h3 class="text-2xl font-bold text-white mb-8 text-center">Our Leadership Team</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($officers as $index => $officer)
                        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden group hover:scale-105 transition-transform duration-300">
                            <div class="p-6">
                                <div class="relative mb-4">
                                    <div class="w-32 h-32 mx-auto bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center border-4 border-white dark:border-slate-800 shadow-lg">
                                        @if($officer->photo_url)
                                            <img src="{{ $officer->photo_url }}" alt="{{ $officer->name }}" class="w-full h-full rounded-full object-cover">
                                        @else
                                            <span class="text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ substr($officer->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="px-4 py-3 rounded-lg bg-emerald-600 dark:bg-emerald-700">
                                    <h4 class="text-lg font-bold text-white mb-2">{{ $officer->name }}</h4>
                                    <p class="text-sm text-emerald-100">{{ $officer->role }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section id="events" class="bg-white dark:bg-slate-900 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">Upcoming Events</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">Join us for our community activities and programs</p>
                <a href="{{ route('events.index') }}" class="inline-block mt-4 text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                    View All Events →
                </a>
            </div>
            
            <div class="space-y-6">
                @forelse($events ?? [] as $event)
                    <div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:shadow-md transition">
                        <!-- Date Box -->
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 dark:bg-green-900 rounded-lg flex flex-col items-center justify-center border-2 border-green-200 dark:border-green-700">
                            <span class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase">{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span>
                            <span class="text-2xl font-bold text-green-700 dark:text-green-300">{{ \Carbon\Carbon::parse($event->date)->format('d') }}</span>
                        </div>
                        
                        <!-- Event Details -->
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">{{ $event->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                @if($event->location)
                                    {{ $event->location }}
                                @endif
                                @if($event->location && $event->time)
                                    , 
                                @endif
                                @if($event->time)
                                    {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <!-- Sample Event 1 -->
                    <div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:shadow-md transition">
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 dark:bg-green-900 rounded-lg flex flex-col items-center justify-center border-2 border-green-200 dark:border-green-700">
                            <span class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase">OCT</span>
                            <span class="text-2xl font-bold text-green-700 dark:text-green-300">28</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">Community Health Drive</h3>
                            <p class="text-gray-600 dark:text-gray-300">Town Plaza, 9 AM - 3 PM</p>
                        </div>
                    </div>
                    
                    <!-- Sample Event 2 -->
                    <div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:shadow-md transition">
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 dark:bg-green-900 rounded-lg flex flex-col items-center justify-center border-2 border-green-200 dark:border-green-700">
                            <span class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase">NOV</span>
                            <span class="text-2xl font-bold text-green-700 dark:text-green-300">15</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">Tree Planting Activity</h3>
                            <p class="text-gray-600 dark:text-gray-300">Sunrise Hill Park, 8 AM</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">Contact Us</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-md border-2 border-red-400 bg-red-50 dark:bg-red-900 dark:border-red-600 px-4 py-3 text-sm text-red-800 dark:text-red-200">
                    <div class="font-semibold mb-1">{{ __('Please fix the following errors:') }}</div>
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-6 rounded-lg border-2 px-4 py-3 text-sm font-medium bg-green-50 dark:bg-green-900 border-green-500 dark:border-green-600 text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-center mb-2 text-gray-800 dark:text-white">Send us a Message</h3>
                        <p class="text-center text-gray-600 dark:text-gray-300">Fill out the form below and we'll get back to you</p>
                    </div>
                    
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 dark:focus:border-emerald-400"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 dark:focus:border-emerald-400"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Message</label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="6"
                                          class="w-full px-4 py-3 border-2 rounded-lg focus:ring-2 focus:ring-offset-0 transition border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-emerald-500 dark:focus:border-emerald-400"
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
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
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-center mb-2 text-gray-800 dark:text-white">Contact Information</h3>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-start p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold mb-1 text-gray-800 dark:text-white">Email</h4>
                                    <p class="text-gray-700 dark:text-gray-300">info@cordilleraadivaylions.org</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold mb-1 text-gray-800 dark:text-white">Phone</h4>
                                    <p class="text-gray-700 dark:text-gray-300">+63 917 123 4567</p>
                                </div>
                            </div>

                            <div class="flex items-start p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-300 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold mb-1 text-gray-800 dark:text-white">Address</h4>
                                    <p class="text-gray-700 dark:text-gray-300">
                                        Baguio City, Benguet<br>
                                        Cordillera Administrative Region<br>
                                        Philippines
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Office Hours -->
                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-8 border-2 border-emerald-500 dark:border-emerald-600">
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-center mb-2 text-gray-800 dark:text-white">Office Hours</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Monday - Friday</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">9:00 AM - 5:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Saturday</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">9:00 AM - 12:00 PM</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Sunday</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
