@php
    /** @var \stdClass|\App\Models\Event $event */
    $userRegistrationsByEvent = $userRegistrationsByEvent ?? [];
    $cardIndex = (int) ($cardIndex ?? 0);
    $currentVolunteers = $event->current_volunteers ?? 0;
    $maxVolunteers = $event->max_participants ?? null;

    $rawStatus = strtolower((string) ($event->event_status ?? ''));
    $now = now();
    $eventDate = $event->date ?? null;

    $derived = 'Upcoming';
    if ($rawStatus === 'completed') {
        $derived = 'Completed';
    } elseif ($eventDate instanceof \Carbon\Carbon) {
        $start = null;
        $end = null;
        if (!empty($event->start_time)) {
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i:s', (string) $event->start_time);
            } catch (\Throwable $e) {
            }
        }
        if (!empty($event->end_time)) {
            try {
                $end = \Carbon\Carbon::createFromFormat('H:i:s', (string) $event->end_time);
            } catch (\Throwable $e) {
            }
        }

        if ($eventDate->isToday()) {
            if ($start && $end) {
                $startAt = $eventDate->copy()->setTimeFrom($start);
                $endAt = $eventDate->copy()->setTimeFrom($end);
                $derived = ($now->between($startAt, $endAt)) ? 'Ongoing' : ($now->greaterThan($endAt) ? 'Ended' : 'Upcoming');
            } else {
                $derived = 'Ongoing';
            }
        } elseif ($eventDate->isPast()) {
            $derived = 'Ended';
        } else {
            $derived = 'Upcoming';
        }
    }

    $statusLabel = $derived;
    $canJoin = !in_array($statusLabel, ['Ended', 'Completed'], true);
    $statusClasses = match ($statusLabel) {
        'Ongoing' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
        'Ended' => 'bg-gray-50 text-gray-700 border border-gray-200',
        'Completed' => 'bg-lions-green/10 text-lions-green border border-lions-green',
        default => 'bg-amber-50 text-amber-700 border border-amber-200',
    };
    $category = strtolower(trim((string) ($event->category ?? 'general')));

    $eid = (string) ($event->id ?? '');
    $regStatus = ($eid !== '' && isset($userRegistrationsByEvent[$eid])) ? $userRegistrationsByEvent[$eid] : null;
@endphp
<div
    class="event-card bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300"
    data-animate="zoom-in"
    style="--reveal-delay: {{ ($cardIndex % 8) * 45 }}ms;"
    data-event-card
    data-event-title="{{ strtolower((string) ($event->title ?? '')) }}"
    data-event-description="{{ strtolower((string) strip_tags($event->description ?? '')) }}"
    data-event-location="{{ strtolower((string) ($event->location ?? '')) }}"
    data-event-category="{{ $category }}"
    data-event-date="{{ optional($event->date)->format('Y-m-d') ?? '' }}"
>
    @php
        $eventShowUrl = !empty($event->id) ? route('events.show', ['eventId' => $event->id]) : null;
        $useModalDetails = auth()->check() && auth()->user()?->isVolunteer();
        $creatorName = $event instanceof \App\Models\Event
            ? optional($event->creator)->name
            : null;
        $modalPayload = [
            'id' => (string) ($event->id ?? ''),
            'title' => (string) ($event->title ?? ''),
            'description' => (string) ($event->description ? strip_tags($event->description) : ''),
            'date' => (string) (optional($event->date)->format('M j, Y') ?? ''),
            'time' => (string) ($event->time ?? ''),
            'location' => (string) ($event->location ?? ''),
            'image' => (string) ($event->photo_url ?? ''),
            'status' => (string) ($statusLabel ?? 'Upcoming'),
            'current_volunteers' => (int) ($currentVolunteers ?? 0),
            'max_volunteers' => $maxVolunteers !== null ? (int) $maxVolunteers : null,
            'approved_volunteers' => $event->approved_volunteers ?? [],
            'organizer' => (string) (($event->organizer ?? '') ?: ($creatorName ?? 'Organizer')),
            'venue' => (string) (($event->venue ?? '') ?: ($event->location ?? '')),
            'requirements' => (string) ($event->requirements ?? ''),
            'join_url' => !empty($event->id) ? (string) route('events.join', ['eventId' => $event->id]) : '',
        ];
    @endphp
    <div class="relative h-44 bg-slate-100 overflow-hidden">
        @if($useModalDetails)
            <button type="button" class="block h-full w-full text-left" title="View event details" @click='open(@json($modalPayload))'>
                @if($event->photo_url)
                    <img src="{{ $event->photo_url }}"
                         alt="{{ $event->title }}"
                         class="event-image w-full h-full object-cover"
                         loading="lazy" decoding="async"
                         onerror="this.style.display='none';">
                @endif
            </button>
        @elseif($eventShowUrl)
            <a href="{{ $eventShowUrl }}" class="block h-full w-full" title="Open event details">
                @if($event->photo_url)
                    <img src="{{ $event->photo_url }}"
                         alt="{{ $event->title }}"
                         class="event-image w-full h-full object-cover"
                         loading="lazy" decoding="async"
                         onerror="this.style.display='none';">
                @endif
            </a>
        @else
            @if($event->photo_url)
                <img src="{{ $event->photo_url }}"
                     alt="{{ $event->title }}"
                     class="event-image w-full h-full object-cover"
                     loading="lazy" decoding="async"
                     onerror="this.style.display='none';">
            @endif
        @endif
        <span class="absolute top-3 left-3 inline-flex items-center h-7 px-3 rounded-full text-xs font-semibold bg-white/95 border border-emerald-100 text-emerald-700 capitalize">
            {{ $category }}
        </span>
        <div class="absolute top-3 right-3 rounded-xl bg-white/95 px-3 py-1.5 text-center shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-emerald-700">{{ optional($event->date)->format('M') ?? 'TBA' }}</p>
            <p class="text-2xl leading-none font-extrabold text-slate-800">{{ optional($event->date)->format('d') ?? '—' }}</p>
        </div>
    </div>

    <div class="p-4">
        <div class="mb-2">
            <span class="inline-flex items-center h-6 px-2.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                {{ $statusLabel }}
            </span>
        </div>

        @auth
            @if(auth()->user()->isAdminOrSuperAdmin() && !empty($event->id))
                <div class="mb-2 flex items-center gap-3">
                    <a href="{{ route('events.edit', ['eventId' => $event->id]) }}"
                       class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline transition">
                        Edit
                    </a>
                    <form method="POST"
                          action="{{ route('events.destroy', ['eventId' => $event->id]) }}"
                          class="inline"
                          data-confirm="Delete this event? This action cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline transition">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        <h3 class="event-title text-xl font-bold text-slate-900 leading-snug line-clamp-2 min-h-[3.25rem]">
            @if($useModalDetails)
                <button type="button" class="hover:text-emerald-700 transition text-left" title="View event details" @click='open(@json($modalPayload))'>
                    {{ e($event->title ?: 'Untitled event') }}
                </button>
            @elseif($eventShowUrl)
                <a href="{{ $eventShowUrl }}" class="hover:text-emerald-700 transition" title="Open event details">
                    {{ e($event->title ?: 'Untitled event') }}
                </a>
            @else
                {{ e($event->title ?: 'Untitled event') }}
            @endif
        </h3>

        <p class="mt-2 text-sm text-slate-600 line-clamp-2 min-h-[2.5rem]">
            {{ \Illuminate\Support\Str::limit($event->description ? strip_tags($event->description) : 'Details will be announced soon.', 90) }}
        </p>

        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
            <div class="rounded-lg bg-slate-50 px-2.5 py-2">
                <p class="text-[11px] uppercase tracking-wide text-slate-400">Full location</p>
                <p class="font-medium text-slate-700 line-clamp-1">{{ e($event->location ?: 'Address TBA') }}</p>
            </div>
            <div class="rounded-lg bg-slate-50 px-2.5 py-2">
                <p class="text-[11px] uppercase tracking-wide text-slate-400">Time</p>
                <p class="font-medium text-slate-700 line-clamp-1">{{ e($event->time ?: 'Time TBA') }}</p>
            </div>

            @auth
                @if(!empty($event->id))
                    @php
                        if ($regStatus === 'approved') {
                            $statusStyle = 'background: linear-gradient(to right, #008751, #10b981); color: white;';
                        } elseif ($regStatus === 'rejected') {
                            $statusStyle = 'background-color: #fee2e2; color: #991b1b;';
                        } else {
                            $statusStyle = 'background: linear-gradient(to right, #6366f1, #8b5cf6); color: white;';
                        }
                    @endphp

                    @if($regStatus)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold shadow-sm"
                                      style="{{ $statusStyle }}">
                                    @if($regStatus === 'approved')
                                        ✓ Approved
                                    @elseif($regStatus === 'rejected')
                                        ✗ Rejected
                                    @else
                                        ⏳ Pending
                                    @endif
                                </span>
                            </div>
                            <form id="leave-event-{{ $event->id }}" method="POST" action="{{ route('events.leave', ['eventId' => $event->id]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-300"
                                        @click.prevent="$dispatch('confirm-dialog', {
                                            title: 'Leave this event?',
                                            message: 'You will be removed from the participant list. You can join again later.',
                                            confirmLabel: 'Leave Event',
                                            cancelLabel: 'Cancel',
                                            tone: 'danger',
                                            formId: 'leave-event-{{ $event->id }}'
                                        })">
                                    Leave Event
                                </button>
                            </form>
                        </div>
                    @else
                        @if($canJoin)
                            <form method="POST" action="{{ route('events.join', ['eventId' => $event->id]) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                                        style="background: linear-gradient(to right, #008751, #10b981);">
                                    Join Event
                                </button>
                            </form>
                        @else
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-sm font-semibold">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Event ended
                            </div>
                        @endif
                    @endif
                @endif
            @else
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Please log in to join this event</p>
                    <a href="{{ route('login') }}"
                       class="inline-block px-6 py-3 rounded-2xl text-white font-semibold transition-all duration-300 shadow-soft hover:shadow-soft-lg transform hover:scale-105"
                       style="background: linear-gradient(to right, #008751, #10b981);">
                        Login to Join
                    </a>
                </div>
            @endauth

            <div class="mt-4 flex justify-between items-center">
                @if($useModalDetails)
                    <button
                        type="button"
                        class="inline-flex items-center text-base font-semibold text-slate-800 hover:text-emerald-700 transition"
                        @click='open(@json($modalPayload))'
                    >
                        View Details →
                    </button>
                @elseif($eventShowUrl)
                    <a
                        href="{{ $eventShowUrl }}"
                        class="inline-flex items-center text-base font-semibold text-slate-800 hover:text-emerald-700 transition"
                    >
                        View Details →
                    </a>
                @else
                    <button
                        type="button"
                        class="inline-flex items-center text-base font-semibold text-slate-800 hover:text-emerald-700 transition"
                        @click='open(@json($modalPayload))'
                    >
                        View Details →
                    </button>
                @endif

            </div>
        </div>
    </div>
</div>
