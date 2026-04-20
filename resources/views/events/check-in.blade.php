<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Event Check-In
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Event</p>
                <h1 class="text-2xl font-bold text-gray-900">{{ $eventTitle }}</h1>
                @if($eventDate)
                    <p class="text-sm text-gray-600 mt-1">{{ $eventDate->format('F j, Y') }}</p>
                @endif

                @if(!empty($statusMessage))
                    <div class="mt-6 rounded-lg border border-amber-300 bg-amber-50 p-4 text-amber-900 text-sm">
                        {{ $statusMessage }}
                    </div>
                @endif

                @if(!empty($alreadyCheckedInAt))
                    <div class="mt-6 rounded-lg border border-blue-300 bg-blue-50 p-4 text-blue-900 text-sm">
                        Checked in at {{ $alreadyCheckedInAt->timezone(config('app.timezone'))->format('M j, Y g:i A') }}.
                    </div>
                @endif

                @if($canCheckIn && !empty($registrationId))
                    <div class="mt-6">
                        <p class="text-sm text-gray-700 mb-3">
                            Confirm your check-in for this event.
                        </p>
                        <form method="POST" action="{{ route('events.checkin.submit', array_filter(['eventId' => $eventId, 'signature' => request('signature'), 'expires' => request('expires')])) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700"
                            >
                                Confirm Check-In
                            </button>
                        </form>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('events.show', ['eventId' => $eventId]) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                        Back to event
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
