<!-- Members Home Content (shared between Members page + dashboards) -->
<!-- Hero Section -->
<div class="py-12" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Meet Our Members</h1>
            <p class="text-lg" style="color: #90EE90;">The dedicated volunteers who make our community stronger</p>
        </div>
        @if(auth()->user() && auth()->user()->isSuperAdmin())
            <div class="mt-6 text-center">
                <a href="{{ route('members.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg" style="background: linear-gradient(to right, #1a5f3f, #2d7a5a);">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Member
                </a>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
@endif

<!-- Members Grid -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($members as $index => $member)
                @php
                    // Alternate between dark green and dark purple for top section (row-based pattern)
                    $rowNumber = floor($index / 3);
                    $isGreen = ($rowNumber % 2 == 0);
                    $topColor = $isGreen ? '#1a5f3f' : '#4a1a5f';
                @endphp
                <div class="member-card bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer">
                    <!-- Card Layout: Photo on left, info on right -->
                    <div class="flex">
                        <!-- Member Photo - Left Side with Yellow Background -->
                        <div class="member-photo-container flex-shrink-0 p-4 flex items-center justify-center transition-transform duration-300" style="background-color: {{ $topColor }};">
                            @if($member->photo_url)
                                <div class="member-photo-circle w-24 h-24 rounded-full bg-yellow-300 flex items-center justify-center border-4 border-white shadow-lg overflow-hidden transition-transform duration-300">
                                    <img src="{{ $member->photo_url }}"
                                         alt="{{ $member->name }}"
                                         class="member-photo-img w-full h-full rounded-full object-cover transition-transform duration-300">
                                </div>
                            @else
                                <div class="member-photo-circle w-24 h-24 rounded-full bg-yellow-300 flex items-center justify-center border-4 border-white shadow-lg transition-transform duration-300">
                                    <span class="member-photo-initial text-3xl font-bold text-gray-800 transition-transform duration-300">{{ substr($member->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Member Info - Right Side -->
                        <div class="flex-1 flex flex-col">
                            <!-- Top Section - Name with alternating colors -->
                            <div class="member-section px-4 py-3 flex items-center min-h-[60px] transition-all duration-300" style="background-color: {{ $topColor }};">
                                <h3 class="member-name text-base font-bold text-white leading-tight transition-transform duration-300">
                                    {{ $member->name }}
                                </h3>
                            </div>

                            <!-- Bottom Section - Role in dark green -->
                            <div class="member-section px-4 py-3 flex-1 min-h-[50px] flex items-center transition-all duration-300" style="background-color: #1a5f3f;">
                                <p class="member-role text-sm font-medium leading-tight transition-transform duration-300" style="color: #90EE90;">
                                    {{ $member->role }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user() && auth()->user()->isSuperAdmin())
                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-center gap-3 border-t border-gray-200">
                            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                EDIT
                            </a>
                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    DELETE
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Members Available</h3>
                    <p class="text-gray-600">Member information will be available soon.</p>
                </div>
            @endforelse
        </div>

        <!-- Stats Section -->
        @if($members->count() > 0)
            <div class="mt-16 rounded-lg shadow-lg p-8" style="background: linear-gradient(to bottom right, #1a5f3f, #2d7a5a);">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Our Community at a Glance</h2>
                    <p class="font-medium" style="color: #90EE90;">Numbers that reflect our commitment to service</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center rounded-lg p-6" style="background-color: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                        <div class="text-4xl font-bold text-white mb-2">{{ $members->count() }}</div>
                        <div class="font-medium" style="color: #90EE90;">Total Members</div>
                    </div>

                    <div class="text-center rounded-lg p-6" style="background-color: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                        <div class="text-4xl font-bold text-white mb-2">
                            {{ $members->whereIn('role', ['President', 'First Vice President', 'Second Vice President', 'Secretary', 'Treasurer'])->count() }}
                        </div>
                        <div class="font-medium" style="color: #90EE90;">Officers</div>
                    </div>

                    <div class="text-center rounded-lg p-6" style="background-color: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);">
                        <div class="text-4xl font-bold text-white mb-2">
                            {{ $members->whereNotIn('role', ['President', 'First Vice President', 'Second Vice President', 'Secretary', 'Treasurer'])->count() }}
                        </div>
                        <div class="font-medium" style="color: #90EE90;">Committee Members</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Hover animations for member cards */
    .member-card {
        transition: all 0.3s ease-in-out;
    }
    .member-card:hover {
        transform: translateY(-8px) scale(1.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .member-card:hover .member-photo-container {
        transform: scale(1.1);
    }
    .member-card:hover .member-photo-circle {
        transform: rotate(6deg);
    }
    .member-card:hover .member-photo-img,
    .member-card:hover .member-photo-initial {
        transform: scale(1.1);
    }
    .member-card:hover .member-name,
    .member-card:hover .member-role {
        transform: translateX(8px);
    }
    .member-card:hover .member-section {
        opacity: 0.9;
    }
</style>
