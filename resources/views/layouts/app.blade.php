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
            @if(request()->routeIs('admin.*') || (request()->routeIs('messaging*') && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('members.*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || ((request()->routeIs('events.*') && !request()->routeIs('events.calendar')) && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('settings*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('profile.*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()) || (request()->routeIs('notifications*') && auth()->check() && auth()->user()?->isAdminOrSuperAdmin()))
                <div class="bg-slate-50 dark:bg-slate-900" x-data="{ adminNavOpen: false }">
                    @include('layouts.sidebar')
                    <div class="md:hidden sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur dark:bg-slate-900/95 dark:border-slate-700">
                        <div class="flex items-center justify-between px-4 py-3">
                            <button type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 p-2 text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800"
                                    @click="adminNavOpen = true"
                                    aria-label="Open menu">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-lg object-cover ring-1 ring-slate-300 dark:ring-slate-600" />
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Admin Panel</span>
                            </div>
                            <a href="{{ route('settings') }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold uppercase text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </a>
                        </div>
                    </div>

                    <div class="fixed inset-0 z-50 md:hidden"
                         x-show="adminNavOpen"
                         x-transition.opacity
                         x-cloak
                         aria-hidden="true">
                        <div class="absolute inset-0 bg-black/50" @click="adminNavOpen = false"></div>
                        <div class="absolute inset-y-0 left-0">
                            @include('layouts.sidebar', ['mobile' => true])
                        </div>
                    </div>
                    <!-- Main content area with left margin for fixed sidebar -->
                    <div class="md:ml-64 min-h-screen md:h-screen flex flex-col overflow-hidden">
                        @isset($header)
                            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex-shrink-0 z-40">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main" class="flex-1 bg-light-gray dark:bg-slate-900 overflow-y-auto flex flex-col pb-24 md:pb-0">
                            <div class="flex-1">
                                {{ $slot }}
                            </div>
                            @unless(request()->routeIs('settings*') || request()->routeIs('profile.*'))
                                @include('layouts.footer')
                            @endunless
                        </main>
                    </div>
                    @include('layouts.volunteer-mobile-bottom-nav')
                </div>
            @elseif(request()->routeIs('volunteer.*') || (request()->routeIs('messaging*') && auth()->user()?->isVolunteer()) || (request()->routeIs('members.*') && auth()->check() && auth()->user()?->isVolunteer()) || ((request()->routeIs('events.*') && !request()->routeIs('events.calendar')) && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('settings*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('profile.*') && auth()->check() && auth()->user()?->isVolunteer()) || (request()->routeIs('notifications*') && auth()->check() && auth()->user()?->isVolunteer()))
                <div class="bg-white dark:bg-slate-900" x-data="{ volunteerNavOpen: false }">
                    @include('layouts.volunteer-sidebar')
                    <div class="md:hidden sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur dark:bg-slate-900/95 dark:border-slate-700">
                        <div class="flex items-center justify-between px-4 py-3">
                            <button type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 p-2 text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800"
                                    @click="volunteerNavOpen = true"
                                    aria-label="Open menu">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-lg object-cover ring-1 ring-slate-300 dark:ring-slate-600" />
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Volunteer Panel</span>
                            </div>
                            <a href="{{ route('settings') }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold uppercase text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'V', 0, 1)) }}
                            </a>
                        </div>
                    </div>

                    <div class="fixed inset-0 z-50 md:hidden"
                         x-show="volunteerNavOpen"
                         x-transition.opacity
                         x-cloak
                         aria-hidden="true">
                        <div class="absolute inset-0 bg-black/50" @click="volunteerNavOpen = false"></div>
                        <div class="absolute inset-y-0 left-0">
                            @include('layouts.volunteer-sidebar', ['mobile' => true])
                        </div>
                    </div>
                    <!-- Main content area with left margin for fixed sidebar -->
                    <div class="md:ml-64 min-h-screen md:h-screen flex flex-col overflow-hidden">
                        @isset($header)
                            <header class="bg-white dark:bg-slate-800 shadow border-b border-slate-200 dark:border-slate-700 flex-shrink-0 z-40">
                                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <main id="main" role="main" class="flex-1 bg-light-gray dark:bg-slate-900 overflow-y-auto flex flex-col pb-24 lg:pb-0">
                            <div class="flex-1">
                                {{ $slot }}
                            </div>
                            @unless(request()->routeIs('settings*') || request()->routeIs('profile.*'))
                                @include('layouts.footer')
                            @endunless
                        </main>
                    </div>
                    @include('layouts.volunteer-mobile-bottom-nav')
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

            <!-- Global Confirmation Modal (replaces browser confirm()) -->
            <div
                x-data="{
                    open: false,
                    title: '',
                    message: '',
                    confirmLabel: 'Confirm',
                    cancelLabel: 'Cancel',
                    tone: 'danger',
                    formId: null,
                    onConfirmEvent: null,
                    onConfirmDetail: null,
                    show(detail) {
                        this.title = detail?.title ?? 'Confirm action';
                        this.message = detail?.message ?? 'Are you sure you want to continue?';
                        this.confirmLabel = detail?.confirmLabel ?? 'Confirm';
                        this.cancelLabel = detail?.cancelLabel ?? 'Cancel';
                        this.tone = detail?.tone ?? 'danger';
                        this.formId = detail?.formId ?? null;
                        this.onConfirmEvent = detail?.onConfirmEvent ?? null;
                        this.onConfirmDetail = detail?.onConfirmDetail ?? null;
                        this.open = true;
                        this.$nextTick(() => this.$refs.confirmBtn?.focus());
                    },
                    close() {
                        this.open = false;
                        this.formId = null;
                        this.onConfirmEvent = null;
                        this.onConfirmDetail = null;
                    },
                    confirm() {
                        if (this.formId) {
                            const form = document.getElementById(this.formId);
                            if (form) form.submit();
                        } else if (this.onConfirmEvent) {
                            window.dispatchEvent(new CustomEvent(this.onConfirmEvent, { detail: this.onConfirmDetail ?? {} }));
                        }
                        this.close();
                    }
                }"
                x-on:confirm-dialog.window="show($event.detail)"
                x-cloak
            >
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
                    role="dialog"
                    aria-modal="true"
                    @keydown.escape.window="if(open) close()"
                    @click.self="close()"
                >
                    <div
                        x-show="open"
                        x-transition.scale
                        class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden"
                    >
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                <div class="mt-0.5 shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-xl flex items-center justify-center"
                                        :class="tone === 'danger'
                                            ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300'
                                            : 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-200'"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M12 5a7 7 0 110 14 7 7 0 010-14z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100" x-text="title"></h3>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300" x-text="message"></p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                @click="close()"
                                x-text="cancelLabel"
                            ></button>
                            <button
                                type="button"
                                x-ref="confirmBtn"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="tone === 'danger'
                                    ? 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500 focus:ring-offset-white dark:focus:ring-offset-slate-900'
                                    : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 focus:ring-offset-white dark:focus:ring-offset-slate-900'"
                                @click="confirm()"
                                x-text="confirmLabel"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Global confirm handler: replace native confirm() with the Tailwind modal above.
                // Usage: add `data-confirm="Message..."` to any <form>.
                (function () {
                    function ensureId(el) {
                        if (el.id) return el.id;
                        el.id = 'confirm-form-' + Math.random().toString(16).slice(2);
                        return el.id;
                    }

                    function openConfirmModal(form) {
                        const message = form.getAttribute('data-confirm') || 'Are you sure you want to continue?';
                        const isDanger = /delete|remove|decline|reject|disable/i.test(message);
                        const confirmLabel =
                            /delete|remove/i.test(message) ? 'Delete' :
                            /decline|reject/i.test(message) ? 'Decline' :
                            'Confirm';

                        window.dispatchEvent(new CustomEvent('confirm-dialog', {
                            detail: {
                                title: 'Confirm action',
                                message,
                                confirmLabel,
                                cancelLabel: 'Cancel',
                                tone: isDanger ? 'danger' : 'safe',
                                formId: ensureId(form),
                            }
                        }));
                    }

                    document.addEventListener('submit', function (e) {
                        const form = e.target;
                        if (!(form instanceof HTMLFormElement)) return;
                        if (!form.matches('form[data-confirm]')) return;
                        e.preventDefault();
                        openConfirmModal(form);
                    }, true);
                })();
            </script>
        </div>
    </body>
</html>
