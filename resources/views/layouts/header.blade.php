<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-soft transition-all duration-300">
    <!-- Top Bar with Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-4 {{ request()->routeIs('dashboard') ? 'flex flex-col items-start gap-3' : 'flex justify-between items-center' }}">
            <!-- Top Left: Modern Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-lions-green rounded-2xl flex items-center justify-center shadow-soft">
                    <img src="{{ asset('images/partners/logo.png') }}" alt="Lions Club Logo" class="w-7 h-7 object-contain">
                </div>
                <span class="text-slate-dark font-semibold text-lg">Cordillera Adivay Lions Club</span>
            </div>
            
            <!-- Desktop Navigation (centered) -->
            <nav class="{{ request()->routeIs('dashboard') ? 'flex-1 flex items-center justify-center gap-8' : 'hidden md:flex flex-1 items-center justify-center gap-8' }}">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('home') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">Home</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('about') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">About</a>
                <a href="{{ route('events.index') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('events.index') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">Events</a>
                <a href="{{ route('events.calendar') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('events.calendar') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">Calendar</a>
                @auth
                    <a href="{{ route('members.index') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('members.*') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">Members</a>
                @endauth
                <a href="{{ route('contact') }}" class="text-gray-600 hover:text-lions-green font-medium {{ request()->routeIs('contact') ? 'text-lions-green font-semibold' : '' }} transition-colors duration-200">Contact</a>
            </nav>

            <!-- Authenticated user dropdown on the right -->
            @auth
                <div class="ml-auto">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-4 py-2 rounded-2xl text-sm leading-4 font-medium text-gray-600 hover:text-lions-green hover:bg-lions-green/5 focus:outline-none transition-all duration-200">
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
                            @if(auth()->user()->isAdmin())
                                <x-dropdown-link :href="$onHeroPage ? route('admin.dashboard') : route('home')">
                                    {{ __($onHeroPage ? 'Dashboard' : 'Home') }}
                                </x-dropdown-link>
                            @elseif(auth()->user()->isVolunteer())
                                <x-dropdown-link :href="route('home')">
                                    {{ __('Home') }}
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="$onHeroPage ? route('dashboard') : route('home')">
                                    {{ __($onHeroPage ? 'Dashboard' : 'Home') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('settings')">{{ __('Settings') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            <!-- Right-side Auth (visible on all sizes for guests) -->
            @guest
                <div class="{{ request()->routeIs('dashboard') ? 'w-full flex justify-end' : 'ml-auto flex items-center' }}">
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-lions-green text-white font-medium rounded-2xl hover:bg-lions-green/90 hover:scale-105 shadow-soft hover:shadow-soft-lg transition-all duration-300">Join</a>
                </div>
            @endguest
        </div>
    </div>
</header>
