<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- Purple title strip -->
    <div class="w-full bg-purple-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center gap-3">
            <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-6 h-6 rounded-full object-cover"/>
            <span class="font-semibold">Volunteer Management System</span>
        </div>
    </div>
    <!-- Top Bar with Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-3 {{ request()->routeIs('dashboard') ? 'flex flex-col items-start gap-3' : 'flex justify-between items-center' }}">
            <!-- Top Left: Small Logo + Text -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-800 rounded flex items-center justify-center">
                    <img src="{{ asset('images/partners/logo.png') }}" alt="Lions Club Logo" class="w-6 h-6 object-contain">
                </div>
                <span class="text-sm text-gray-600 font-medium">Lions Club</span>
            </div>
            
            <!-- Desktop Navigation (centered) -->
            <nav class="{{ request()->routeIs('dashboard') ? 'flex-1 flex items-center justify-center gap-6' : 'hidden md:flex flex-1 items-center justify-center gap-6' }}">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900 font-medium {{ request()->routeIs('home') ? 'text-gray-900 border-b-2 border-gray-900 pb-1' : '' }} transition">Home</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-gray-900 font-medium {{ request()->routeIs('about') ? 'text-gray-900 border-b-2 border-gray-900 pb-1' : '' }} transition">About</a>
                <div class="relative group">
                    <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-gray-900 font-medium {{ request()->routeIs('events.*') ? 'text-gray-900 border-b-2 border-gray-900 pb-1' : '' }} transition flex items-center">
                        Events
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="{{ route('events.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('events.index') ? 'bg-gray-100' : '' }}">All Events</a>
                            <a href="{{ route('events.calendar') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('events.calendar') ? 'bg-gray-100' : '' }}">Calendars</a>
                        </div>
                    </div>
                </div>
                @auth
                    <a href="{{ route('members.index') }}" class="text-gray-700 hover:text-gray-900 font-medium {{ request()->routeIs('members.*') ? 'text-gray-900 border-b-2 border-gray-900 pb-1' : '' }} transition">Members</a>
                @endauth
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-gray-900 font-medium {{ request()->routeIs('contact') ? 'text-gray-900 border-b-2 border-gray-900 pb-1' : '' }} transition">Contact</a>
            </nav>

            <!-- Authenticated user dropdown on the right -->
            @auth
                <div class="ml-auto">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @php
                                $onHeroPage = request()->routeIs('home');
                            @endphp
                            @if(auth()->user()->isSuperAdmin())
                                <x-dropdown-link :href="$onHeroPage ? route('admin.dashboard') : route('home')">
                                    {{ __($onHeroPage ? 'Dashboard' : 'Home') }}
                                </x-dropdown-link>
                            @elseif(auth()->user()->isVolunteer())
                                <x-dropdown-link :href="$onHeroPage ? route('volunteer.dashboard') : route('home')">
                                    {{ __($onHeroPage ? 'Dashboard' : 'Home') }}
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="$onHeroPage ? route('dashboard') : route('home')">
                                    {{ __($onHeroPage ? 'Dashboard' : 'Home') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('settings')">{{ __('Settings') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            <!-- Right-side Auth (visible on all sizes for guests) -->
            @guest
                <div class="{{ request()->routeIs('dashboard') ? 'w-full flex justify-end' : 'ml-auto flex items-center' }}">
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-medium transition">Join</a>
                </div>
            @endguest
        </div>
    </div>

    
</header>
