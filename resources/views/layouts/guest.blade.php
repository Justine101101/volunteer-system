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
    <body class="font-sans text-gray-900 antialiased">
        <a href="#main" class="sr-only focus:not-sr-only focus-ring px-3 py-2 bg-white text-gray-800">Skip to content</a>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100" role="document">
            <div>
                <a href="/">
                    <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-20 h-20 object-contain" />
                </a>
            </div>

            <div id="main" class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" role="main" aria-live="polite">
                {{ $slot }}
            </div>
            <!-- Toasts -->
            @if (session('status') || session('success') || session('error'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition.opacity
                     class="fixed inset-x-0 top-20 z-50 flex justify-center pointer-events-none">
                    <div class="pointer-events-auto rounded-md shadow-xl px-5 py-4 text-sm text-white max-w-md w-full mx-4"
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
