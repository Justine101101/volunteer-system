<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-dark-mode" content="{{ auth()->user()->dark_mode ? '1' : '0' }}">
        @endauth

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-light-gray dark:bg-slate-900 transition-colors duration-200">
        <a href="#main" class="sr-only focus:not-sr-only focus-ring px-3 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white">Skip to content</a>
        <div class="min-h-screen bg-light-gray dark:bg-slate-900" role="document">
            @if(request()->routeIs('admin.*') || (request()->routeIs('messaging*') && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('members.*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('events.*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('settings*') && auth()->check() && auth()->user()?->isSuperAdmin()) || (request()->routeIs('profile.*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()))
                <div class="bg-slate-50 dark:bg-slate-900">
                    @include('layouts.sidebar')
                    <!-- Main content area with left margin for fixed sidebar -->
                    <div class="md:ml-64 h-screen flex flex-col overflow-hidden">
                        @isset($header)
                            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex-shrink-0 z-40">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main" class="flex-1 bg-light-gray dark:bg-slate-900 overflow-y-auto flex flex-col">
                            <div class="flex-1">
                                {{ $slot }}
                            </div>
                            @include('layouts.footer')
                        </main>
                    </div>
                </div>
            @elseif(request()->routeIs('volunteer.*') || (request()->routeIs('messaging*') && auth()->user()?->isVolunteer()) || (request()->routeIs('members.*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('events.*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('settings*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('profile.*') && auth()->check() && auth()->user()?->isVolunteer()))
                <div class="bg-white dark:bg-slate-900">
                    @include('layouts.volunteer-sidebar')
                    <!-- Main content area with left margin for fixed sidebar -->
                    <div class="md:ml-64 h-screen flex flex-col overflow-hidden">
                        @isset($header)
                            <header class="bg-white dark:bg-slate-800 shadow border-b border-slate-200 dark:border-slate-700 flex-shrink-0 z-40">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main" class="flex-1 bg-light-gray dark:bg-slate-900 overflow-y-auto flex flex-col">
                            <div class="flex-1">
                                {{ $slot }}
                            </div>
                            @include('layouts.footer')
                        </main>
                    </div>
                </div>
            @else
                @include('layouts.header')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-slate-800 shadow border-b border-slate-200 dark:border-slate-700">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main id="main" role="main" class="bg-light-gray dark:bg-slate-900">
                    {{ $slot }}
                </main>
                @include('layouts.footer')
            @endif
            <!-- Global Toast Notifications -->
            @if (session('status') || session('success') || session('error'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition.opacity
                     class="fixed inset-x-0 top-20 z-50 flex justify-center pointer-events-none">
                    <div class="pointer-events-auto max-w-md w-full mx-4 rounded-xl border px-5 py-4 text-sm font-semibold shadow-xl dark:shadow-2xl bg-white/95 dark:bg-slate-900/95"
                         @class([
                            'border-emerald-500 text-emerald-800 dark:text-emerald-300' => session('success') || session('status'),
                            'border-red-500 text-red-800 dark:text-red-300' => session('error'),
                         ])
                         role="status" aria-live="polite">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                @if(session('error'))
                                    <svg class="h-5 w-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M12 5a7 7 0 110 14 7 7 0 010-14z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                {{ session('success') ?? session('status') ?? session('error') }}
                            </div>
                            <button type="button"
                                    class="ml-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 focus:outline-none"
                                    @click="show=false"
                                    aria-label="Dismiss notification">
                                ×
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
