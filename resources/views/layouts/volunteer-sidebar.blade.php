<aside class="hidden md:flex md:flex-col w-64 min-h-screen bg-purple-900 text-white">
    <!-- Logo Section (moved into nav below) -->
    <div class="px-0 pt-3 pb-0"></div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-0 pt-0 pb-4 space-y-1 overflow-y-auto">
        <!-- Logo above dashboard in the same scroll area -->
        <div class="px-0 py-2 flex items-center">
            <img src="{{ asset('images/partners/logo.png') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover" />
            <div class="ml-3">
                <p class="text-xs text-purple-200">Volunteer System</p>
                <p class="text-sm font-semibold text-white">Cordillera Adivay</p>
            </div>
        </div>
        <a href="{{ route('volunteer.dashboard') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg @if(request()->routeIs('volunteer.dashboard')) bg-green-200 text-purple-900 font-semibold @else hover:bg-purple-800 @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4 0V9m5 3l2 2"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('events.index') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800 @if(request()->routeIs('events.*')) bg-green-200 text-purple-900 font-semibold @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Events</span>
        </a>
        <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800 @if(request()->routeIs('members.*')) bg-green-200 text-purple-900 font-semibold @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Members</span>
        </a>
        <a href="{{ route('messaging') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800 @if(request()->routeIs('messaging*')) bg-green-200 text-purple-900 font-semibold @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l.8-4A8.994 8.994 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Messaging</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800 @if(request()->routeIs('profile.*')) bg-green-200 text-purple-900 font-semibold @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>My Profile</span>
        </a>
        <a href="{{ route('settings') }}" class="flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800 @if(request()->routeIs('settings')) bg-green-200 text-purple-900 font-semibold @endif">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V22a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H2a2 2 0 010-4h.09c-.52.06-.97.39-1.19.86z"/></svg>
            <span>Settings</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-0 py-2 rounded-lg hover:bg-purple-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>
