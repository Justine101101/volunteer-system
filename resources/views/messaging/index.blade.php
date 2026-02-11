<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight" style="color:#111827;">
                {{ __('Messaging') }}
            </h2>
            <button type="button" onclick="openNewMessageModal()" class="inline-flex items-center px-4 py-2 rounded-lg shadow hover:bg-emerald-700 transition"
                    style="background-color:#047857;color:#ffffff;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Message
            </button>
        </div>
    </x-slot>

    <div class="py-6 bg-white">
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
                        <div class="p-4 border-b border-gray-200 bg-white">
                            <h3 class="text-lg font-semibold" style="color:#1f2937;">Inbox</h3>
                            <p class="text-sm mt-1" style="color:#374151;">{{ $conversations->count() }} conversations</p>
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            @forelse($conversations as $conversation)
                                <a href="{{ route('messaging.chat', $conversation['user']->id) }}" 
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
                                                    <p class="text-sm font-medium truncate" style="color:#111827;">
                                                        {{ $conversation['user']->name }}
                                                    </p>
                                                    @if($conversation['unread_count'] > 0)
                                                        <span class="flex-shrink-0 ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-emerald-600 rounded-full">
                                                            {{ $conversation['unread_count'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(isset($conversation['latest_message']) && $conversation['latest_message'])
                                                    <p class="text-sm truncate mt-1" style="color:#374151;">
                                                        {{ $conversation['latest_message']->sanitized_subject ? $conversation['latest_message']->sanitized_subject . ': ' : '' }}
                                                        {{ Str::limit($conversation['latest_message']->sanitized_message ?? '', 50) }}
                                                    </p>
                                                    <p class="text-xs mt-1" style="color:#6b7280;">
                                                        {{ $conversation['latest_message']->created_at->diffForHumans() ?? '' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center" style="color:#111827;">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm" style="color:#4b5563;">No conversations yet</p>
                                    <p class="text-xs mt-1" style="color:#6b7280;">Start a new conversation to get started</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Chat Window -->
                    <div class="flex-1 flex flex-col">
                        @if(isset($otherUser) && isset($messages))
                            <!-- Chat Header -->
                            <div class="p-4 border-b border-gray-200 bg-white" style="color:#111827;">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold" style="color:#111827;">{{ $otherUser->name }}</h3>
                                        <p class="text-sm" style="color:#4b5563;">{{ $otherUser->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div class="flex-1 overflow-y-auto p-4 bg-white space-y-4" 
                                 id="messagesContainer"
                                 data-current-user-id="{{ auth()->id() }}"
                                 data-other-user-id="{{ $otherUser->id }}">
                                @foreach($messages as $message)
                                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? '' : 'shadow' }}"
                                             style="{{ $message->sender_id === auth()->id() ? 'background-color:#047857;color:#ffffff;' : 'background-color:#ffffff;color:#111827;' }}">
                                            @if($message->sanitized_subject)
                                                <p class="font-semibold text-sm mb-1" style="{{ $message->sender_id === auth()->id() ? 'color:#d1fae5;' : 'color:#374151;' }}">
                                                    {{ $message->sanitized_subject }}
                                                </p>
                                            @endif
                                            <p class="text-sm whitespace-pre-wrap">{{ $message->sanitized_message }}</p>
                                            <div class="mt-2 flex items-center justify-between">
                                                <p class="text-xs" style="{{ $message->sender_id === auth()->id() ? 'color:#d1fae5;' : 'color:#6b7280;' }}">
                                                    {{ $message->created_at->format('M j, Y g:i A') }}
                                                    @if($message->sender_id === auth()->id())
                                                        • <span>{{ $message->read_at ? 'Seen' : 'Sent' }}</span>
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-2 text-xs">
                                                    <button type="button" class="underline hover:opacity-80"
                                                            onclick="prefillReply('{{ addslashes($message->sanitized_message) }}')"
                                                            style="{{ $message->sender_id === auth()->id() ? 'color:#d1fae5;' : 'color:#6b7280;' }}">Reply</button>
                                                    @if($message->sender_id === auth()->id())
                                                        <button type="button" class="underline hover:opacity-80"
                                                                onclick="toggleEdit('edit-{{ $message->id }}')"
                                                                style="{{ $message->sender_id === auth()->id() ? 'color:#d1fae5;' : 'color:#6b7280;' }}">Edit</button>
                                                        <form action="{{ route('messaging.destroy', $message) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="underline hover:opacity-80" style="{{ $message->sender_id === auth()->id() ? 'color:#d1fae5;' : 'color:#6b7280;' }}">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($message->sender_id === auth()->id())
                                                <form id="edit-{{ $message->id }}" action="{{ route('messaging.update', $message) }}" method="POST" class="mt-2 hidden">
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
                                <form action="{{ route('messaging.send') }}" method="POST" id="messageForm">
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
                                                id="sendButton"
                                                class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            <span class="text-sm font-semibold tracking-wide">Send</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="flex-1 flex items-center justify-center bg-white" style="color:#111827;">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium" style="color:#111827;">No conversation selected</h3>
                                    <p class="mt-2 text-sm" style="color:#4b5563;">Select a conversation from the inbox or start a new one</p>
                                    <button type="button" onclick="openNewMessageModal()" class="mt-4 inline-flex items-center px-4 py-2 rounded-lg shadow hover:bg-emerald-700 transition"
                                            style="background-color:#047857;color:#ffffff;">
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
                    <h3 class="text-lg font-semibold" style="color:#111827;">New Message</h3>
                    <button onclick="closeNewMessageModal()" class="hover:text-gray-600" style="color:#6b7280;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('messaging.send') }}" method="POST" id="newMessageForm">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_receiver_id" class="block text-sm font-medium mb-2" style="color:#1f2937;">To</label>
                        <select name="receiver_id" id="modal_receiver_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Select a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="modal_subject" class="block text-sm font-medium mb-2" style="color:#1f2937;">Subject</label>
                        <input type="text" name="subject" id="modal_subject" placeholder="Subject (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="mb-4">
                        <label for="modal_message" class="block text-sm font-medium mb-2" style="color:#1f2937;">Message</label>
                        <textarea name="message" id="modal_message" rows="4" required placeholder="Type your message..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeNewMessageModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                                style="color:#1f2937;background-color:#ffffff;">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg hover:bg-emerald-700"
                                style="background-color:#047857;color:#ffffff;">
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

        // Initialize Realtime Chat
        let realtimeChat = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                // Auto-scroll to bottom on initial load
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Initialize Supabase Realtime Chat if we have the required data
                const currentUserId = messagesContainer.getAttribute('data-current-user-id');
                const otherUserId = messagesContainer.getAttribute('data-other-user-id');
                
                if (currentUserId && otherUserId && window.RealtimeChat) {
                    // Get Supabase config from Laravel
                    const supabaseConfig = {
                        url: @json(config('supabase.url')),
                        anonKey: @json(config('supabase.anon_key')),
                        currentUserId: parseInt(currentUserId),
                        otherUserId: parseInt(otherUserId)
                    };
                    
                    if (supabaseConfig.url && supabaseConfig.anonKey) {
                        realtimeChat = new window.RealtimeChat(supabaseConfig);
                    }
                }
            }

            // Enhanced form submission with AJAX (prevents page reload)
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const sendButton = document.getElementById('sendButton');
                    const originalButtonText = sendButton.innerHTML;
                    
                    // Disable button during submission
                    sendButton.disabled = true;
                    sendButton.innerHTML = '<span class="text-sm">Sending...</span>';
                    
                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                            }
                        });
                        
                        if (response.ok) {
                            // Clear form
                            document.getElementById('message').value = '';
                            document.getElementById('subject').value = '';
                            
                            // Scroll to bottom (new message will appear via Realtime)
                            if (messagesContainer) {
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            }
                            
                            // Show success message
                            const successDiv = document.createElement('div');
                            successDiv.className = 'mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg';
                            successDiv.textContent = 'Message sent successfully.';
                            const header = document.querySelector('x-slot[name="header"]')?.parentElement || document.body;
                            header.insertBefore(successDiv, header.firstChild);
                            
                            // Remove success message after 3 seconds
                            setTimeout(() => successDiv.remove(), 3000);
                        } else {
                            throw new Error('Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please try again.');
                    } finally {
                        // Re-enable button
                        sendButton.disabled = false;
                        sendButton.innerHTML = originalButtonText;
                    }
                });
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (realtimeChat) {
                realtimeChat.destroy();
            }
        });

        // Close modal on outside click
        document.getElementById('newMessageModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeNewMessageModal();
            }
        });
    </script>
    <script>
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

