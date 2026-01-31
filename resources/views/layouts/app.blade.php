<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <a href="#main" class="sr-only focus:not-sr-only focus-ring px-3 py-2 bg-white text-gray-800">Skip to content</a>
        <div class="min-h-screen bg-white" role="document">
            @if(request()->routeIs('admin.*') || request()->routeIs('officer.*') || (request()->routeIs('messaging*') && auth()->user()?->isSuperAdmin()) || (request()->routeIs('members.*') && auth()->check() && (auth()->user()?->isSuperAdmin() || auth()->user()?->isOfficer())) || (request()->routeIs('events.*') && auth()->check() && (auth()->user()?->isSuperAdmin() || auth()->user()?->isOfficer())) || (request()->routeIs('settings*') && auth()->check() && (auth()->user()?->isSuperAdmin() || auth()->user()?->isOfficer())) || (request()->routeIs('profile.*') && auth()->check() && (auth()->user()?->isSuperAdmin() || auth()->user()?->isOfficer())))
                <!-- Admin top purple title strip -->
                <div class="w-full bg-purple-900 text-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center gap-3">
                        <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-6 h-6 rounded-full object-cover"/>
                        <span class="font-semibold">Volunteer Management System</span>
                    </div>
                </div>
                <div class="min-h-screen flex">
                    @include('layouts.sidebar')
                    <div class="flex-1 min-w-0">
                        @isset($header)
                            <header class="bg-white shadow">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main">
                            {{ $slot }}
                        </main>
                        @include('layouts.footer')
                    </div>
                </div>
            @elseif(request()->routeIs('volunteer.*') || (request()->routeIs('messaging*') && auth()->user()?->isVolunteer()) || (request()->routeIs('members.*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('events.*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('settings*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('profile.*') && auth()->check() && auth()->user()?->isVolunteer()))
                <div class="min-h-screen flex">
                    @include('layouts.volunteer-sidebar')
                    <div class="flex-1 min-w-0">
                        @isset($header)
                            <header class="bg-white shadow">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main">
                            {{ $slot }}
                        </main>
                        @include('layouts.footer')
                    </div>
                </div>
            @else
                @include('layouts.header')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main id="main" role="main" class="bg-white">
                    {{ $slot }}
                </main>
                @include('layouts.footer')
            @endif
            <!-- Toasts -->
            @if (session('status') || session('success') || session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition.opacity class="fixed top-4 right-4 z-50 max-w-sm">
                    <div class="rounded-md shadow-lg px-4 py-3 text-sm text-white"
                         @class([
                            'bg-green-600' => session('success') || session('status'),
                            'bg-red-600' => session('error'),
                         ])
                         role="status" aria-live="polite">
                        <div class="flex items-start">
                            <div class="flex-1">
                                {{ session('success') ?? session('status') ?? session('error') }}
                            </div>
                            <button type="button" class="ml-3 text-white/80 hover:text-white focus:outline-none" @click="show=false" aria-label="Dismiss notification">×</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
