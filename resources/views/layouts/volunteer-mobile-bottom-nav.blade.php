@auth
    @if(auth()->user()?->isVolunteer())
        <style>
            #volunteer-mobile-bottom-nav { display: none; }
            /* Show on phone + tablet widths (matches Tailwind <lg). */
            @media (max-width: 1023px) {
                #volunteer-mobile-bottom-nav {
                    display: block !important;
                    position: fixed !important;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 60;
                }
                #volunteer-mobile-bottom-nav .volunteer-mobile-bottom-nav-grid {
                    display: grid !important;
                    grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                    align-items: center;
                    gap: 0;
                }
                #volunteer-mobile-bottom-nav .volunteer-mobile-bottom-nav-item {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-decoration: none;
                    min-height: 52px;
                }
            }
            /* Hide on desktop (Tailwind lg and above). */
            @media (min-width: 1024px) {
                #volunteer-mobile-bottom-nav {
                    display: none !important;
                }
            }
        </style>
        <nav id="volunteer-mobile-bottom-nav" class="border-t border-slate-200 bg-white/95 backdrop-blur dark:border-slate-700 dark:bg-slate-900/95" style="padding-bottom: max(env(safe-area-inset-bottom), 0px);">
            <div class="mx-auto max-w-2xl">
                <div class="volunteer-mobile-bottom-nav-grid grid grid-cols-6 px-2 py-2">
                    <a href="{{ route('volunteer.dashboard') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('volunteer.dashboard')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Overview">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4 0V9m5 3l2 2"/>
                        </svg>
                        <span>Home</span>
                    </a>

                    <a href="{{ route('events.index') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('events.*')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Events">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Events</span>
                    </a>

                    <a href="{{ route('members.index') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('members.*')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Members">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Members</span>
                    </a>

                    <a href="{{ route('notifications.index') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('notifications.*')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Notifications">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                        </svg>
                        <span>Alerts</span>
                    </a>

                    <a href="{{ route('messaging') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('messaging*')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Messaging">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-4A8.994 8.994 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Chats</span>
                    </a>

                    <a href="{{ route('settings') }}"
                       class="volunteer-mobile-bottom-nav-item flex flex-col items-center justify-center rounded-lg py-1 text-[11px] font-semibold transition @if(request()->routeIs('settings*') || request()->routeIs('profile.*')) text-emerald-600 dark:text-emerald-400 @else text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 @endif"
                       aria-label="Settings">
                        <svg class="h-5 w-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </nav>
    @endif
@endauth
