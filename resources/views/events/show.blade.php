<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->title }}
            </h2>
            <a href="{{ route('events.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                ← Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <!-- Event Header -->
                <div class="bg-white border-b border-gray-200 p-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>
                            <div class="flex flex-wrap gap-6 text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $event->date->format('F j, Y') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $event->time }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $event->location }}
                                </div>
                            </div>
                        </div>
                        @auth
                            @if(auth()->user()->isSuperAdmin())
                                <div class="flex space-x-2">
                                    <a href="{{ route('events.edit', $event) }}" 
                                       class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-300">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('events.destroy', $event) }}" 
                                          class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this event?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Event Photo -->
                @if($event->photo_url)
                    <div class="w-full h-96 bg-gray-100 overflow-hidden">
                        <img src="{{ $event->photo_url }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Event Content -->
                <div class="p-8">
                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Event Description</h2>
                        <div class="prose max-w-none text-gray-600">
                            {{ $event->description }}
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ $event->date->format('F j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium">{{ $event->time }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-medium">{{ $event->location }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Created by:</span>
                                    <span class="font-medium">{{ $event->creator->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Status -->
                        @auth
                            @php
                                $userRegistration = $event->registrations->where('user_id', auth()->id())->first();
                            @endphp
                            
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Registration</h3>
                                
                                @if($userRegistration)
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                                @if($userRegistration->status === 'approved') bg-green-100 text-green-800
                                                @elseif($userRegistration->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                @if($userRegistration->status === 'approved')
                                                    ✓ Approved
                                                @elseif($userRegistration->status === 'rejected')
                                                    ✗ Rejected
                                                @else
                                                    ⏳ Pending Approval
                                                @endif
                                            </span>
                                        </div>
                                        
                                        <form method="POST" action="{{ route('events.leave', $event) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                    onclick="return confirm('Are you sure you want to leave this event?')">
                                                Leave Event
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <p class="text-gray-600 mb-4">You haven't registered for this event yet.</p>
                                        <form method="POST" action="{{ route('events.join', $event) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                                Join Event
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Join This Event</h3>
                                <p class="text-gray-600 mb-4">Please log in to register for this event.</p>
                                <a href="{{ route('login') }}" 
                                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                    Login to Join
                                </a>
                            </div>
                        @endauth
                    </div>

                    <!-- Registered Volunteers (for superadmin) -->
                    @auth
                        @if(auth()->user()->isSuperAdmin() && $event->registrations->count() > 0)
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Registered Volunteers ({{ $event->registrations->count() }})</h3>
                                <div class="space-y-3">
                                    @foreach($event->registrations as $registration)
                                        <div class="flex items-center justify-between bg-white p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-blue-600 font-semibold">{{ substr($registration->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $registration->user->name }}</p>
                                                    <p class="text-sm text-gray-600">{{ $registration->user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($registration->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($registration->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($registration->status) }}
                                                </span>
                                                
                                                @if($registration->status === 'pending')
                                                    <form method="POST" action="{{ route('registrations.approve', $registration) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('registrations.reject', $registration) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
