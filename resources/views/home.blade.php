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
                            <span class="bg-blue-600 text-white px-4 py-2 md:px-6 md:py-3 text-2xl md:text-4xl lg:text-5xl font-bold">Find Your</span>
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
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-blue-700 text-white font-semibold rounded-md hover:bg-blue-800 transition">
                        Register Now
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-md hover:bg-gray-200 transition">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-8">Upcoming Events</h2>
            
            <div class="space-y-6">
                @forelse($events ?? [] as $event)
                    <div class="flex items-start gap-4 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition">
                        <!-- Date Box -->
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 rounded-lg flex flex-col items-center justify-center border-2 border-green-200">
                            <span class="text-xs font-semibold text-green-700 uppercase">{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span>
                            <span class="text-2xl font-bold text-green-700">{{ \Carbon\Carbon::parse($event->date)->format('d') }}</span>
                        </div>
                        
                        <!-- Event Details -->
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ $event->title }}</h3>
                            <p class="text-gray-600">
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
                    <div class="flex items-start gap-4 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition">
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 rounded-lg flex flex-col items-center justify-center border-2 border-green-200">
                            <span class="text-xs font-semibold text-green-700 uppercase">OCT</span>
                            <span class="text-2xl font-bold text-green-700">28</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Community Health Drive</h3>
                            <p class="text-gray-600">Town Plaza, 9 AM - 3 PM</p>
                        </div>
                    </div>
                    
                    <!-- Sample Event 2 -->
                    <div class="flex items-start gap-4 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition">
                        <div class="flex-shrink-0 w-20 h-20 bg-green-50 rounded-lg flex flex-col items-center justify-center border-2 border-green-200">
                            <span class="text-xs font-semibold text-green-700 uppercase">NOV</span>
                            <span class="text-2xl font-bold text-green-700">15</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">Tree Planting Activity</h3>
                            <p class="text-gray-600">Sunrise Hill Park, 8 AM</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
