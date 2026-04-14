<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-dark leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <!-- History Section -->
    <section class="py-20 bg-light-gray" data-animate="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-dark mb-6">About Us</h2>
                <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">A Chronicle of Service and Community in the Cordillera Region</p>
            </div>
            
            <div class="bg-white rounded-3xl shadow-soft-lg p-8 md:p-12">
                <h3 class="text-3xl font-bold text-slate-dark mb-8 text-center">Our History</h3>
                <div class="prose prose-lg max-w-none space-y-6">
                    <p class="text-lg text-gray-700 leading-relaxed">
                        In the heart of the Cordillera mountain ranges, known for their breathtaking landscapes and vibrant cultures, a group of driven individuals came together with a shared vision: to serve, empower, and uplift their communities. Thus was born the Cordillera Adivay Lions Club—a beacon of voluntary service and humanitarian commitment in the region. The story of this club is woven into the evolving tapestry of civic engagement in the Cordilleras, echoing the global ideals of Lions Club International while reflecting the unique spirit of the local people.
                    </p>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        The name "Adivay" reflects our deep connection to the Cordillera culture, meaning "gathering" 
                        or "coming together" - which perfectly represents our mission of uniting people for a common cause.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-20 bg-white" data-animate="slide-right">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Mission -->
                <div class="bg-light-gray rounded-3xl shadow-soft-lg p-8 md:p-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-dark">Our Mission</h3>
                    </div>
                    <p class="text-center text-gray-600 leading-relaxed text-lg">
                        To empower communities in the Cordillera region through volunteer service, 
                        sustainable development projects, and fostering a culture of compassion and unity.
                    </p>
                </div>

                <!-- Vision -->
                <div class="bg-light-gray rounded-3xl shadow-soft-lg p-8 md:p-10">
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
                        A thriving Cordillera region where every community member has access to opportunities, 
                        resources, and support needed to lead fulfilling and prosperous lives.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Officers Section -->
    <section class="py-20 bg-light-gray" data-animate="slide-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-dark mb-4">Our Leadership Team</h2>
                <p class="text-xl text-gray-600">Meet the dedicated officers who guide our organization</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($officers as $index => $officer)
                    <div class="text-center group" data-animate="zoom-in" style="--reveal-delay: {{ ($index % 6) * 60 }}ms;">
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
                            <h3 class="text-lg font-semibold text-slate-dark">{{ $officer->name }}</h3>
                            <p class="text-sm text-gray-600 font-medium">{{ $officer->role }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-xl text-gray-600">Officer information will be available soon.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-white" data-animate="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-dark mb-4">Our Core Values</h2>
                <p class="text-xl text-gray-600">The principles that guide everything we do</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center bg-light-gray rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300" data-animate="slide-right" style="--reveal-delay: 40ms;">
                    <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-slate-dark">Compassion</h3>
                    <p class="text-gray-600 leading-relaxed">We serve with empathy and understanding, putting the needs of others first.</p>
                </div>
                
                <div class="text-center bg-light-gray rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300" data-animate="fade-up" style="--reveal-delay: 80ms;">
                    <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-slate-dark">Integrity</h3>
                    <p class="text-gray-600 leading-relaxed">We maintain the highest standards of honesty and ethical behavior in all our actions.</p>
                </div>
                
                <div class="text-center bg-light-gray rounded-3xl shadow-soft-lg p-8 hover:shadow-soft-xl transition-shadow duration-300" data-animate="slide-left" style="--reveal-delay: 120ms;">
                    <div class="w-20 h-20 bg-lions-green/10 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <svg class="w-10 h-10 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-slate-dark">Unity</h3>
                    <p class="text-gray-600 leading-relaxed">We work together as one team, celebrating diversity and fostering collaboration.</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
