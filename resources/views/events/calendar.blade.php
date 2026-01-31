<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Club Events') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Calendar Header -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <!-- Left side: Today button and navigation -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('events.calendar', ['year' => $today->year, 'month' => $today->month]) }}" 
                       class="px-4 py-2 bg-lions-green text-white rounded-lg hover:bg-lions-green-light transition font-medium">
                        Today
                    </a>
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('events.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" 
                           class="p-2 rounded-lg hover:bg-lions-green-lighter transition">
                            <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('events.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" 
                           class="p-2 rounded-lg hover:bg-lions-green-lighter transition">
                            <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Center: Month and Year -->
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-lions-green">
                        {{ $startDate->format('F Y') }}
                    </h1>
                    <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <!-- Right side: Action buttons -->
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-lg hover:bg-lions-green-lighter transition" title="Messages">
                        <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </button>
                    <button class="p-2 rounded-lg hover:bg-lions-green-lighter transition" title="Calendar">
                        <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button class="p-2 rounded-lg hover:bg-lions-green-lighter transition" title="Print" onclick="window.print()">
                        <svg class="w-5 h-5 text-lions-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </button>
                    <div class="relative">
                        <button class="px-4 py-2 bg-lions-green-lighter rounded-lg hover:bg-lions-green-light transition font-medium text-lions-green flex items-center gap-2">
                            Month
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-lions-green-lighter border-b border-gray-200">
                    @php
                        $dayNames = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    @endphp
                    @foreach($dayNames as $day)
                        <div class="p-3 text-center font-semibold text-lions-green text-sm">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7">
                    @php
                        $firstDayOfMonth = $startDate->copy()->startOfMonth();
                        $lastDayOfMonth = $startDate->copy()->endOfMonth();
                        $startCalendar = $firstDayOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $endCalendar = $lastDayOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                        $currentDate = $startCalendar->copy();
                        $monthNames = [
                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
                            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                        ];
                    @endphp

                    @while($currentDate <= $endCalendar)
                        @php
                            $isCurrentMonth = $currentDate->month == $startDate->month;
                            $isToday = $currentDate->format('Y-m-d') == $today->format('Y-m-d');
                            $dateKey = $currentDate->format('Y-m-d');
                            $dayEvents = $events->get($dateKey, collect());
                        @endphp
                        
                        <div class="min-h-24 border-r border-b border-gray-200 p-2 {{ !$isCurrentMonth ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-1">
                                <span class="text-sm font-medium {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }} {{ $isToday ? 'bg-lions-green text-white rounded-full w-7 h-7 flex items-center justify-center' : '' }}">
                                    {{ $currentDate->day }}
                                </span>
                                @if(!$isCurrentMonth && $currentDate->month != $startDate->month)
                                    <span class="text-xs text-gray-400">
                                        {{ $monthNames[$currentDate->month] }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Events for this day -->
                            <div class="space-y-1 mt-1">
                                @foreach($dayEvents->take(2) as $event)
                                    <a href="{{ route('events.show', $event) }}" 
                                       class="block text-xs text-lions-green hover:text-lions-green-light transition"
                                       title="{{ $event->title }}">
                                        <span class="font-medium">• {{ $event->time ? substr($event->time, 0, 5) : '' }}</span> {{ Str::limit($event->title, 15) }}
                                    </a>
                                @endforeach
                                @if($dayEvents->count() > 2)
                                    <div class="text-xs text-gray-500 px-1">
                                        +{{ $dayEvents->count() - 2 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>

            <!-- Event Legend -->
            <div class="mt-6 flex items-center justify-center gap-6 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-lions-green-lighter rounded"></div>
                    <span>Event</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-lions-green rounded-full"></div>
                    <span>Today</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</x-app-layout>

