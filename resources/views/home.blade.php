<x-app-layout>
    <!-- Hero Section -->
    <section class="bg-light-gray py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Banner with Glassmorphism Overlay -->
            <div class="mb-12 rounded-3xl overflow-hidden relative shadow-soft-lg">
                <img src="{{ asset('images/partners/banner.jpg') }}" alt="Find Your Purpose - Community Event" class="w-full h-96 md:h-[500px] object-cover">
                
                <!-- Glassmorphism Text Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent backdrop-blur-sm">
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 lg:p-16">
                        <!-- Main Title with Glow Effect -->
                        <div class="mb-6">
                            <div class="inline-block">
                                <span class="bg-lions-green/90 backdrop-blur-sm text-white px-6 py-3 md:px-8 md:py-4 text-3xl md:text-5xl lg:text-6xl font-bold rounded-2xl animate-glow shadow-soft-lg">Find Your</span>
                                <span class="text-white text-3xl md:text-5xl lg:text-6xl font-bold ml-3 drop-shadow-2xl">Purpose</span>
                            </div>
                        </div>
                        
                        <!-- Subtitle Text -->
                        <p class="text-white/95 text-lg md:text-xl lg:text-2xl drop-shadow-lg max-w-3xl leading-relaxed">
                            Support local events and connect with us at the Cordillera Adivay Lions Club.<br>
                            Everyone is welcome! #BecomeUnstoppableLions
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="text-center max-w-4xl mx-auto mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6 leading-tight">
                    Join us for a meaningful day of service and giving back!
                </h2>
                <p class="text-xl text-gray-600 mb-10 leading-relaxed max-w-3xl mx-auto">
                    Be part of our upcoming activities and community programs. It's more than just an event—it's an opportunity to combine your passion for helping others with a mission to make a meaningful difference.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="{{ route('register') }}" class="px-10 py-4 bg-lions-green text-white font-semibold rounded-3xl hover:bg-lions-green/90 shadow-soft hover:shadow-soft-lg transform hover:scale-105 transition-all duration-300 text-lg">
                        Register Now
                    </a>
                    <a href="{{ route('login') }}" class="px-10 py-4 bg-white text-gray-700 font-semibold rounded-3xl hover:bg-gray-50 shadow-soft hover:shadow-soft-lg border border-gray-200 transition-all duration-300 text-lg">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">About Us</h2>
                <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">A Chronicle of Service and Community in the Cordillera Region</p>
            </div>
            
            <!-- History -->
            <div class="mb-20">
                <h3 class="text-3xl font-bold text-slate-dark mb-8 text-center">Our History</h3>
                <div class="prose prose-lg max-w-none">
                    <div class="bg-light-gray rounded-3xl p-8 md:p-12 shadow-soft">
                        <p class="text-lg text-gray-700 leading-relaxed mb-6">
                            In the heart of the Cordillera mountain ranges, known for their breathtaking landscapes and vibrant cultures, a group of driven individuals came together with a shared vision: to serve, empower, and uplift their communities. Thus was born the Cordillera Adivay Lions Club—a beacon of voluntary service and humanitarian commitment in the region.
                        </p>
                        <p class="text-lg text-gray-700 leading-relaxed">
                            The name "Adivay" reflects our deep connection to the Cordillera culture, meaning "gathering" or "coming together" - which perfectly represents our mission of uniting people for a common cause.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
                <div class="bg-white rounded-3xl shadow-soft-lg p-8 md:p-10 border border-gray-100">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-dark">Our Mission</h3>
                    </div>
                    <p class="text-center text-gray-600 leading-relaxed text-lg">
                        To empower communities in the Cordillera region through volunteer service, sustainable development projects, and fostering a culture of compassion and unity.
                    </p>
                </div>

                <div class="bg-white rounded-3xl shadow-soft-lg p-8 md:p-10 border border-gray-100">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-dark">Our Vision</h3>
                    </div>
                    <p class="text-center text-gray-600 leading-relaxed text-lg">
                        A thriving Cordillera region where every community member has access to opportunities, resources, and support needed to lead fulfilling and prosperous lives.
                    </p>
                </div>
            </div>

            <!-- Core Values -->
            <div class="mb-20">
                <h3 class="text-3xl font-bold text-slate-dark mb-12 text-center">Our Core Values</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center bg-white rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-4 text-slate-dark">Compassion</h4>
                        <p class="text-gray-600 leading-relaxed">We serve with empathy and understanding, putting the needs of others first.</p>
                    </div>
                    
                    <div class="text-center bg-white rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-4 text-slate-dark">Integrity</h4>
                        <p class="text-gray-600 leading-relaxed">We maintain the highest standards of honesty and ethical behavior in all our actions.</p>
                    </div>
                    
                    <div class="text-center bg-white rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold mb-4 text-slate-dark">Unity</h4>
                        <p class="text-gray-600 leading-relaxed">We work together as one team, celebrating diversity and fostering collaboration.</p>
                    </div>
                </div>
            </div>

            <!-- Leadership Team -->
            @if(isset($officers) && $officers->count() > 0)
            <div class="mb-20">
                <h3 class="text-3xl font-bold text-slate-dark mb-12 text-center">Our Leadership Team</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($officers as $index => $officer)
                        <div class="text-center group">
                            <!-- Minimalist Avatar Circle -->
                            <div class="mb-6 flex justify-center">
                                <div class="w-24 h-24 bg-lions-green/10 rounded-full flex items-center justify-center border-4 border-white shadow-soft-lg group-hover:shadow-soft-xl transition-shadow duration-300">
                                    @if($officer->photo_url)
                                        <img src="{{ $officer->photo_url }}" alt="{{ $officer->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-3xl font-bold text-lions-green">{{ substr($officer->name, 0, 1) }}</span>
                                    @endif
                                </div>
                            </div>
                            <!-- Name and Role -->
                            <div class="space-y-2">
                                <h4 class="text-lg font-semibold text-slate-dark">{{ $officer->name }}</h4>
                                <p class="text-sm text-gray-600 font-medium">{{ $officer->role }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section id="events" class="bg-light-gray py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">Upcoming Events</h2>
                <p class="text-xl text-gray-600 mb-8">Join us for our community activities and programs</p>
                <a href="{{ route('events.index') }}" class="inline-flex items-center text-lions-green font-semibold hover:text-lions-green/80 transition-colors duration-200 text-lg">
                    View All Events 
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            
            <div class="space-y-6">
                @forelse(($events ?? collect()) as $event)
                    <!-- Premium Ticket-Style Event Card -->
                    <div class="bg-white rounded-3xl shadow-soft-lg hover:shadow-soft-xl transition-all duration-300 overflow-hidden group">
                        <div class="flex items-stretch">
                            <!-- Date Box (Left Side) -->
                            <div class="flex-shrink-0 w-28 bg-lions-green flex flex-col items-center justify-center text-white p-4">
                                <span class="text-sm font-bold uppercase tracking-wider mb-1">
                                    {{ optional($event->date)->format('M') }}
                                </span>
                                <span class="text-4xl font-bold">
                                    {{ optional($event->date)->format('d') }}
                                </span>
                            </div>

                            <!-- Event Details (Right Side) -->
                            <div class="flex-1 p-8 flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-semibold text-slate-dark mb-3 group-hover:text-lions-green transition-colors duration-200">
                                        {{ $event->title }}
                                    </h3>
                                    <div class="flex items-center text-gray-600 space-x-4">
                                        @if(!empty($event->location))
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $event->location }}
                                            </div>
                                        @endif
                                        @if(!empty($event->time))
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $event->time }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Arrow Indicator -->
                                <div class="ml-6">
                                    <div class="w-12 h-12 bg-lions-green/10 rounded-full flex items-center justify-center group-hover:bg-lions-green/20 transition-colors duration-200">
                                        <svg class="w-6 h-6 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl shadow-soft p-12 text-center">
                        <div class="mx-auto mb-6 w-20 h-20 bg-lions-green/10 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-dark mb-3">No upcoming events yet</h3>
                        <p class="text-gray-600 text-lg">Please check back soon.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">Contact Us</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>

            @if ($errors->any())
                <div class="mb-8 rounded-2xl border-2 border-red-400 bg-red-50 dark:bg-red-900 dark:border-red-600 px-6 py-4 text-sm text-red-800 dark:text-red-200">
                    <div class="font-semibold mb-2">{{ __('Please fix the following errors:') }}</div>
                    <ul class="list-disc ms-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-8 rounded-2xl border-2 px-6 py-4 text-sm font-medium bg-green-50 dark:bg-green-900 border-green-500 dark:border-green-600 text-green-800 dark:text-green-200">
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
                        <h3 class="text-2xl font-bold text-center mb-3 text-slate-dark">Send us a Message</h3>
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
                            <h3 class="text-2xl font-bold text-center mb-3 text-slate-dark">Contact Information</h3>
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
                                    <h4 class="text-lg font-semibold mb-1 text-slate-dark">Email</h4>
                                    <p class="text-gray-600">info@cordilleraadivaylions.org</p>
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
                                    <h4 class="text-lg font-semibold mb-1 text-slate-dark">Phone</h4>
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
                                    <h4 class="text-lg font-semibold mb-1 text-slate-dark">Address</h4>
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
                            <h3 class="text-2xl font-bold text-center mb-3 text-slate-dark">Office Hours</h3>
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
        </div>
    </section>
</x-app-layout>
