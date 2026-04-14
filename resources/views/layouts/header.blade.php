<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/85 backdrop-blur-xl shadow-sm transition-all duration-300" data-nav>
    <div class="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="group inline-flex items-center gap-3 transition duration-300">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 shadow-md shadow-emerald-600/20 transition duration-300 group-hover:scale-105 group-hover:shadow-lg">
                <img src="{{ asset('images/partners/logo.png') }}" alt="Lions Club Logo" class="h-7 w-7 object-contain">
            </span>
            <span class="hidden text-sm font-semibold leading-tight text-slate-900 sm:block md:text-base">
                Cordillera Adivay Lions Club
            </span>
        </a>

        <nav class="hidden items-center gap-7 md:flex" aria-label="Primary Navigation">
            @php
                $linkClass = 'text-sm font-medium text-slate-600 hover:text-emerald-600 transition duration-300';
                $activeClass = 'text-emerald-600';
            @endphp
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? $activeClass : $linkClass }}">Home</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? $activeClass : $linkClass }}">About</a>
            <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.index') ? $activeClass : $linkClass }}">Events</a>
            <a href="{{ route('events.calendar') }}" class="{{ request()->routeIs('events.calendar') ? $activeClass : $linkClass }}">Calendar</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? $activeClass : $linkClass }}">Contact</a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-emerald-200 hover:text-emerald-600 hover:shadow-md focus:outline-none">
                            <x-user-avatar :user="Auth::user()" :size="28" class="shadow-sm" />
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @if(auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')">{{ __('Dashboard') }}</x-dropdown-link>
                        @elseif(auth()->user()->isVolunteer())
                            <x-dropdown-link :href="route('home')">{{ __('Home') }}</x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('dashboard')">{{ __('Dashboard') }}</x-dropdown-link>
                        @endif
                        <x-dropdown-link :href="route('settings')">{{ __('Settings') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm text-slate-700 transition duration-200 hover:bg-slate-50">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition duration-300 hover:-translate-y-0.5 hover:bg-emerald-500 hover:shadow-lg">
                    Join as Volunteer
                </a>
            @endauth
        </div>
    </div>
</header>
