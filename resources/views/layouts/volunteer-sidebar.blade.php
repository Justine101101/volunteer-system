<aside class="hidden md:flex md:flex-col fixed left-0 top-0 h-screen w-64 bg-slate-900 dark:bg-slate-900 text-white shadow-xl z-50">
    <!-- Logo Section -->
    <div class="px-4 pt-4 pb-3 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-12 h-12 rounded-xl object-cover ring-2 ring-slate-700" />
            <div>
                <p class="text-xs text-slate-400 font-medium">Volunteer System</p>
                <p class="text-sm font-semibold text-white">Cordillera Adivay</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 pt-4 pb-4 space-y-1 overflow-y-auto">
        <a href="{{ route('events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('events.*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Events</span>
        </a>
        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('notifications.*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/></svg>
            <span>Notifications</span>
        </a>
        <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('members.*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Members</span>
        </a>
        <a href="{{ route('messaging') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('messaging*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-4A8.994 8.994 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Messaging</span>
        </a>
        <a href="{{ route('settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Settings & Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full pt-2">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-rose-400 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>
