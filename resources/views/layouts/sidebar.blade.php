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
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.dashboard')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4 0V9m5 3l2 2"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.users*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Manage Users</span>
        </a>
        <a href="{{ route('events.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('events.*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Events</span>
        </a>
        <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('members.*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Members</span>
        </a>
        @if(auth()->user()?->isAdmin())
        <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.reports')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Reports</span>
        </a>
        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.audit-logs*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Audit Logs</span>
        </a>
        @endif
        <a href="{{ route('admin.attendance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.attendance')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Attendance</span>
        </a>
        @if(auth()->user()?->isAdmin())
        <a href="{{ route('admin.messaging') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('admin.messaging*')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-4A8.994 8.994 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Admin Messaging</span>
        </a>
        @endif
        <a href="{{ route('settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(request()->routeIs('settings')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @else text-slate-300 hover:bg-slate-800 hover:text-emerald-300 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Settings</span>
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
