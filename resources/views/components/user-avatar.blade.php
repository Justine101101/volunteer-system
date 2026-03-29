@props(['user', 'size' => 36, 'class' => ''])
@php
    $photo = $user?->photo_url ?? null;
    $initial = strtoupper(substr($user?->name ?? 'U', 0, 1));
    $dimension = is_numeric($size) ? intval($size) : 36;
@endphp

@if($photo)
    <img
        src="{{ (is_string($photo) && str_starts_with($photo, 'http')) ? $photo : asset($photo) }}"
        alt="{{ $user?->name ?? 'User' }}"
        width="{{ $dimension }}"
        height="{{ $dimension }}"
        loading="lazy"
        decoding="async"
        class="rounded-full object-cover border border-slate-200 {{ $class }}"
        onerror="this.style.display='none';"
    >
@else
    <div
        class="rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold border border-emerald-700/30 {{ $class }}"
        style="width: {{ $dimension }}px; height: {{ $dimension }}px;"
        aria-hidden="true"
        title="{{ $user?->name ?? 'User' }}"
    >
        {{ $initial }}
    </div>
@endif
