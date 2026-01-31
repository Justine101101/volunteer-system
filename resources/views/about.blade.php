<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <!-- History Section -->
    <div class="py-16" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-6">Our History</h2>
                    <div class="space-y-4" style="color: #90EE90;">
                        <p class="text-lg font-medium">
                        A Chronicle of Service and Community in the Cordillera Region
                        </p>
                        <p>
                        In the heart of the Cordillera mountain ranges, known for their breathtaking landscapes and vibrant cultures, a group of driven individuals came together with a shared vision: to serve, empower, and uplift their communities. Thus was born the Cordillera Adivay Lions Club—a beacon of voluntary service and humanitarian commitment in the region. The story of this club is woven into the evolving tapestry of civic engagement in the Cordilleras, echoing the global ideals of Lions Club International while reflecting the unique spirit of the local people.
                        </p>
                        <p>
                            The name "Adivay" reflects our deep connection to the Cordillera culture, meaning "gathering" 
                            or "coming together" - which perfectly represents our mission of uniting people for a common cause.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission & Vision Section -->
    <div class="py-16 bg-white" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Mission -->
                <div class="p-8 rounded-lg shadow-lg overflow-hidden" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);" data-animate>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Our Mission</h3>
                    </div>
                    <p class="text-center" style="color: #90EE90;">
                        To empower communities in the Cordillera region through volunteer service, 
                        sustainable development projects, and fostering a culture of compassion and unity.
                    </p>
                </div>

                <!-- Vision -->
                <div class="p-8 rounded-lg shadow-lg overflow-hidden" style="background: linear-gradient(to bottom right, #4a1a5f, #6b2d8a);" data-animate>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8" style="color: #4a1a5f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Our Vision</h3>
                    </div>
                    <p class="text-center" style="color: #d4a5ff;">
                        A thriving Cordillera region where every community member has access to opportunities, 
                        resources, and support needed to lead fulfilling and prosperous lives.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Officers Section -->
    <div class="py-16" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white mb-4">Our Leadership Team</h2>
                <p class="text-lg" style="color: #90EE90;">Meet the dedicated officers who guide our organization</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($officers as $index => $officer)
                    @php
                        $cardColor = ($index % 2 == 0) ? '#1a5f3f' : '#4a1a5f';
                    @endphp
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden group hover:scale-105 transition-transform duration-300" data-animate>
                        <div class="p-6">
                            <div class="relative mb-4">
                                <div class="w-32 h-32 mx-auto bg-yellow-300 rounded-full flex items-center justify-center border-4 border-white shadow-lg group-hover:scale-105 transition duration-300">
                                    @if($officer->photo_url)
                                        <img src="{{ $officer->photo_url }}" alt="{{ $officer->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-3xl font-bold" style="color: #1a5f3f;">{{ substr($officer->name, 0, 1) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="px-4 py-3 rounded-lg" style="background-color: {{ $cardColor }};">
                                <h3 class="text-lg font-bold text-white mb-2">{{ $officer->name }}</h3>
                                <p class="text-sm" style="color: #90EE90;">{{ $officer->role }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-lg" style="color: #90EE90;">Officer information will be available soon.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <div class="py-16 bg-white" data-animate>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold" style="color: #1a5f3f;">Our Core Values</h2>
                <p class="text-lg text-gray-600 mt-2">The principles that guide everything we do</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;" data-animate>
                    <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: #1a5f3f;">Compassion</h3>
                    <p class="text-gray-600">We serve with empathy and understanding, putting the needs of others first.</p>
                </div>
                
                <div class="text-center bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #4a1a5f;" data-animate>
                    <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8" style="color: #4a1a5f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: #4a1a5f;">Integrity</h3>
                    <p class="text-gray-600">We maintain the highest standards of honesty and ethical behavior in all our actions.</p>
                </div>
                
                <div class="text-center bg-white rounded-lg shadow-lg p-8 border-2" style="border-color: #1a5f3f;" data-animate>
                    <div class="w-16 h-16 bg-yellow-300 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8" style="color: #1a5f3f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2" style="color: #1a5f3f;">Unity</h3>
                    <p class="text-gray-600">We work together as one team, celebrating diversity and fostering collaboration.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
