<footer class="mt-16 border-t border-slate-200 bg-white/90 py-10 backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/90" data-animate="fade-up">
    <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div>
            <div class="inline-flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 shadow-md shadow-emerald-600/20">
                    <img src="{{ asset('images/partners/logo.png') }}" alt="Lions Club Logo" class="h-6 w-6 object-contain">
                </span>
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Cordillera Adivay Lions Club</h3>
            </div>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                A modern volunteer portal focused on meaningful service, transparent event coordination, and a better member experience.
            </p>
        </div>

        <nav class="flex flex-col gap-3 text-sm text-slate-600 dark:text-slate-300">
            <a href="{{ route('home') }}" class="transition duration-300 hover:text-emerald-600">Home</a>
            <a href="{{ route('about') }}" class="transition duration-300 hover:text-emerald-600">About</a>
            <a href="{{ route('events.index') }}" class="transition duration-300 hover:text-emerald-600">Events</a>
            <a href="{{ route('events.calendar') }}" class="transition duration-300 hover:text-emerald-600">Calendar</a>
            <a href="{{ route('contact') }}" class="transition duration-300 hover:text-emerald-600">Contact</a>
        </nav>

        <div class="space-y-4">
            <p class="text-sm font-medium text-slate-800 dark:text-slate-100">Stay connected</p>
            <a href="https://www.facebook.com/profile.php?id=61566458549684"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-emerald-600 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50 dark:border-slate-700 dark:hover:bg-slate-800"
               aria-label="Follow us on Facebook">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                </svg>
            </a>
            <p class="text-xs text-slate-500 dark:text-slate-400">© {{ date('Y') }} Volunteer System. All rights reserved.</p>
        </div>
    </div>
</footer>
