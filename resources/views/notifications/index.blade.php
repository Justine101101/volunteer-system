<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
        </h2>
    </x-slot>

    <div class="py-10 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-900">Recent</h3>
                    <p class="mt-1 text-sm text-slate-500">Updates about your event registrations and announcements.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($notifications as $n)
                        @php
                            $isRead = !empty($n['read_at']);
                        @endphp
                        <div class="px-6 py-5 flex items-start justify-between gap-4 {{ $isRead ? 'bg-white' : 'bg-emerald-50/40' }}">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold {{ $isRead ? 'text-slate-900' : 'text-slate-900' }}">
                                    {{ $n['title'] ?? 'Notification' }}
                                </p>
                                @if(!empty($n['body']))
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $n['body'] }}
                                    </p>
                                @endif
                                <p class="mt-2 text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($n['created_at'] ?? now())->diffForHumans() }}
                                </p>
                            </div>

                            @if(!$isRead && !empty($n['id']))
                                <form method="POST" action="{{ route('notifications.read', ['notificationId' => $n['id']]) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Mark read
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-semibold text-slate-400">Read</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center">
                            <p class="text-sm text-slate-500">No notifications yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

