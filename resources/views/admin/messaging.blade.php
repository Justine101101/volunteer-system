<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messaging') }}
            </h2>
            <button onclick="openNewMessageModal()" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Message
            </button>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: calc(100vh - 200px);">
                <div class="flex h-full">
                    <!-- Inbox Sidebar -->
                    <div class="w-1/3 border-r border-gray-200 flex flex-col">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Inbox</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $conversations->count() }} conversations</p>
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            @forelse($conversations as $conversation)
                                <a href="{{ route('admin.messaging.chat', $conversation['user']->id) }}" 
                                   class="block p-4 border-b border-gray-100 hover:bg-gray-50 transition @if(isset($otherUser) && $otherUser->id === $conversation['user']->id) bg-emerald-50 @endif">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                                    {{ strtoupper(substr($conversation['user']->name ?? 'U', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $conversation['user']->name }}
                                                    </p>
                                                    @if($conversation['unread_count'] > 0)
                                                        <span class="flex-shrink-0 ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-emerald-600 rounded-full">
                                                            {{ $conversation['unread_count'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(isset($conversation['latest_message']) && $conversation['latest_message'])
                                                    <p class="text-sm text-gray-600 truncate mt-1">
                                                        {{ $conversation['latest_message']->sanitized_subject ? $conversation['latest_message']->sanitized_subject . ': ' : '' }}
                                                        {{ Str::limit($conversation['latest_message']->sanitized_message ?? '', 50) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $conversation['latest_message']->created_at->diffForHumans() ?? '' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No conversations yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Start a new conversation to get started</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Chat Window -->
                    <div class="flex-1 flex flex-col">
                        @if(isset($otherUser) && isset($messages))
                            <!-- Chat Header -->
                            <div class="p-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $otherUser->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-4" id="messagesContainer">
                                @foreach($messages as $message)
                                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-emerald-600 text-white' : 'bg-white text-gray-900 shadow' }}">
                                            @if($message->sanitized_subject)
                                                <p class="font-semibold text-sm mb-1 {{ $message->sender_id === auth()->id() ? 'text-emerald-100' : 'text-gray-700' }}">
                                                    {{ $message->sanitized_subject }}
                                                </p>
                                            @endif
                                            <p class="text-sm whitespace-pre-wrap">{{ $message->sanitized_message }}</p>
                                            <div class="mt-2 flex items-center justify-between">
                                                <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-emerald-100' : 'text-gray-500' }}">
                                                    {{ $message->created_at->format('M j, Y g:i A') }}
                                                </p>
                                                <div class="flex items-center gap-2 text-xs {{ $message->sender_id === auth()->id() ? 'text-emerald-100' : 'text-gray-500' }}">
                                                    <button type="button" class="underline hover:opacity-80" onclick="prefillReply('{{ addslashes($message->sanitized_message) }}')">Reply</button>
                                                    @if($message->sender_id === auth()->id())
                                                        <button type="button" class="underline hover:opacity-80" onclick="toggleEdit('edit-{{ $message->id }}')">Edit</button>
                                                        <form action="{{ route('admin.messaging.destroy', $message) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="underline hover:opacity-80">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($message->sender_id === auth()->id())
                                                <form id="edit-{{ $message->id }}" action="{{ route('admin.messaging.update', $message) }}" method="POST" class="mt-2 hidden">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="subject" value="{{ $message->sanitized_subject }}" placeholder="Subject (optional)" class="w-full px-2 py-1 border border-gray-300 rounded mb-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                <textarea name="message" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500">{{ $message->sanitized_message }}</textarea>
                                                    <div class="mt-2 flex justify-end gap-2">
                                                        <button type="button" class="px-3 py-1 border rounded" onclick="toggleEdit('edit-{{ $message->id }}')">Cancel</button>
                                                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded">Save</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Send Message Box -->
                            <div class="p-4 border-t border-gray-200 bg-white">
                                <form action="{{ route('admin.messaging.send') }}" method="POST" id="messageForm">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                                    <div class="mb-3">
                                        <input type="text" 
                                               name="subject" 
                                               id="subject" 
                                               placeholder="Subject (optional)" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div class="flex items-end space-x-3">
                                        <textarea name="message" 
                                                  id="message" 
                                                  rows="2" 
                                                  required
                                                  placeholder="Type your message..." 
                                                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                                        <button type="submit" 
                                                class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="flex-1 flex items-center justify-center bg-gray-50">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">No conversation selected</h3>
                                    <p class="mt-2 text-sm text-gray-500">Select a conversation from the inbox or start a new one</p>
                                    <button onclick="openNewMessageModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg shadow hover:bg-emerald-700 transition">
                                        Start New Conversation
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div id="newMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">New Message</h3>
                    <button onclick="closeNewMessageModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.messaging.send') }}" method="POST" id="newMessageForm">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_receiver_id" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                        <select name="receiver_id" id="modal_receiver_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="modal_subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input type="text" name="subject" id="modal_subject" placeholder="Subject (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="mb-4">
                        <label for="modal_message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea name="message" id="modal_message" rows="4" required placeholder="Type your message..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeNewMessageModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openNewMessageModal() {
            document.getElementById('newMessageModal').classList.remove('hidden');
        }

        function closeNewMessageModal() {
            document.getElementById('newMessageModal').classList.add('hidden');
        }

        // Auto-scroll to bottom of messages
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });

        // Clear message input after sending (for better UX, but form will submit normally)
        document.getElementById('messageForm')?.addEventListener('submit', function(e) {
            // Let the form submit normally - Laravel will handle the redirect
            // Just clear the message field for better UX
            setTimeout(() => {
                const messageInput = document.getElementById('message');
                if (messageInput) {
                    messageInput.value = '';
                }
                const subjectInput = document.getElementById('subject');
                if (subjectInput) {
                    subjectInput.value = '';
                }
            }, 100);
        });

        // Close modal on outside click
        document.getElementById('newMessageModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewMessageModal();
            }
        });

        function prefillReply(text){
            const ta = document.getElementById('message');
            if(!ta) return;
            const quote = `> ${text.replace(/\n/g,"\n> ")}\n\n`;
            ta.value = quote;
            ta.focus();
            ta.selectionStart = ta.selectionEnd = ta.value.length;
        }
        function toggleEdit(id){
            const el = document.getElementById(id);
            if(el){ el.classList.toggle('hidden'); }
        }
    </script>
</x-app-layout>

