<x-app-layout>
    <div class="h-screen flex flex-col bg-slate-50">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 px-4 py-3 mx-4 mt-4 rounded-lg shadow-sm" role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 mx-4 mt-4 rounded-lg shadow-sm" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="flex-1 flex overflow-hidden">
            <!-- LEFT SIDEBAR - Conversation List (320px) -->
            <div class="w-80 bg-white border-r border-slate-200 flex flex-col shadow-sm">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-slate-200 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-slate-900">Messages</h2>
                        <button 
                            type="button" 
                            onclick="openNewMessageModal()" 
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm hover:shadow-md"
                            title="New Message">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="relative">
                        <input 
                            type="text" 
                            id="conversationSearch"
                            placeholder="Search conversations..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-slate-50"
                            onkeyup="filterConversations(this.value)">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Conversations List -->
                <div class="flex-1 overflow-y-auto" id="conversationsList">
                    @forelse($conversations as $conversation)
                        <a 
                            href="{{ route('messaging.chat', $conversation['user']->id) }}" 
                            class="block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors conversation-item
                                @if(isset($otherUser) && $otherUser->id === $conversation['user']->id) bg-emerald-50 border-l-4 border-l-emerald-500 @endif"
                            data-conversation-name="{{ strtolower($conversation['user']->name) }}">
                            <div class="flex items-start gap-3">
                                <!-- Avatar -->
                                <div class="flex-shrink-0 relative">
                                    @if($conversation['user']->photo_url)
                                        <img 
                                            src="{{ asset($conversation['user']->photo_url) }}" 
                                            alt="{{ $conversation['user']->name }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-slate-200">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-semibold text-lg border-2 border-slate-200">
                                            {{ strtoupper(substr($conversation['user']->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <!-- Online Status Indicator (placeholder - can be enhanced) -->
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                </div>

                                <!-- Conversation Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3 class="text-sm font-semibold text-slate-900 truncate">
                                            {{ $conversation['user']->name }}
                                        </h3>
                                        @if(isset($conversation['latest_message']) && $conversation['latest_message'])
                                            <span class="text-xs text-slate-500 flex-shrink-0">
                                                {{ $conversation['latest_message']->created_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($conversation['latest_message']) && $conversation['latest_message'])
                                        <p class="text-sm text-slate-600 truncate mb-1">
                                            {{ Str::limit($conversation['latest_message']->sanitized_message ?? '', 40) }}
                                        </p>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        @if($conversation['unread_count'] > 0)
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-emerald-600 rounded-full min-w-[20px]">
                                                {{ $conversation['unread_count'] }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-16 w-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <p class="text-sm font-medium text-slate-600 mb-1">No conversations yet</p>
                            <p class="text-xs text-slate-500">Start a new conversation to get started</p>
                            <button 
                                type="button" 
                                onclick="openNewMessageModal()" 
                                class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Message
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- CENTER PANEL - Chat Area -->
            <div class="flex-1 flex flex-col bg-white">
                @if(isset($otherUser) && isset($messages))
                    <!-- Chat Header -->
                    <div class="px-6 py-4 border-b border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- Avatar -->
                                <div class="relative">
                                    @if($otherUser->photo_url)
                                        <img 
                                            src="{{ asset($otherUser->photo_url) }}" 
                                            alt="{{ $otherUser->name }}"
                                            class="w-12 h-12 rounded-full object-cover border-2 border-slate-200">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-semibold text-lg border-2 border-slate-200">
                                            {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <!-- Online Status -->
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $otherUser->name }}</h3>
                                    <p class="text-sm text-slate-500 flex items-center gap-1">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        Online
                                    </p>
                                </div>
                            </div>

                            <!-- Action Icons -->
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button"
                                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors"
                                    title="Conversation Info">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button 
                                    type="button"
                                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors"
                                    title="More Options">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area (Scrollable) -->
                    <div 
                        class="flex-1 overflow-y-auto px-6 py-6 bg-gradient-to-b from-slate-50 to-white"
                        id="messagesContainer"
                        data-current-user-id="{{ auth()->id() }}"
                        data-other-user-id="{{ $otherUser->id }}">
                        <div class="max-w-4xl mx-auto space-y-4">
                            @foreach($messages as $message)
                                <div 
                                    class="group flex items-end gap-3 message-item {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}"
                                    data-message-id="{{ $message->id }}">
                                    @if($message->sender_id !== auth()->id())
                                        <!-- Received Message Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($message->sender->photo_url)
                                                <img 
                                                    src="{{ asset($message->sender->photo_url) }}" 
                                                    alt="{{ $message->sender->name }}"
                                                    class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 text-white flex items-center justify-center font-semibold text-xs">
                                                    {{ strtoupper(substr($message->sender->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Message Bubble -->
                                    <div class="flex flex-col max-w-[70%] {{ $message->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
                                        @if($message->sanitized_subject)
                                            <p class="text-xs font-semibold text-slate-500 mb-1 px-1">
                                                {{ $message->sanitized_subject }}
                                            </p>
                                        @endif
                                        
                                        <div class="px-4 py-2.5 rounded-2xl shadow-sm
                                            {{ $message->sender_id === auth()->id() 
                                                ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-br-sm' 
                                                : 'bg-white text-slate-900 border border-slate-200 rounded-bl-sm' }}">
                                            <p class="text-sm whitespace-pre-wrap break-words">{{ $message->sanitized_message }}</p>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 mt-1 px-1">
                                            <span class="text-xs text-slate-500">
                                                {{ $message->created_at->format('h:i A') }}
                                            </span>
                                            @if($message->sender_id === auth()->id())
                                                @if($message->read_at)
                                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    @if($message->sender_id === auth()->id())
                                        <!-- Sent Message Actions (on hover) -->
                                        <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 message-actions">
                                            <div class="flex flex-col gap-1">
                                                <button 
                                                    type="button"
                                                    onclick="toggleEdit('edit-{{ $message->id }}')"
                                                    class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <form action="{{ route('messaging.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Delete this message?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="submit"
                                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                                        title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($message->sender_id === auth()->id())
                                    <!-- Edit Form (Hidden by default) -->
                                    <form id="edit-{{ $message->id }}" action="{{ route('messaging.update', $message) }}" method="POST" class="hidden max-w-[70%] ml-auto mt-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="subject" value="{{ $message->sanitized_subject }}" placeholder="Subject (optional)" class="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <textarea name="message" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $message->sanitized_message }}</textarea>
                                        <div class="mt-2 flex justify-end gap-2">
                                            <button type="button" class="px-3 py-1 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50" onclick="toggleEdit('edit-{{ $message->id }}')">Cancel</button>
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save</button>
                                        </div>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Typing Indicator (Hidden by default) -->
                    <div id="typingIndicator" class="px-6 py-2 hidden">
                        <div class="flex items-center gap-2 max-w-[70%]">
                            <div class="w-8 h-8 rounded-full bg-slate-300"></div>
                            <div class="bg-white border border-slate-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                                <div class="flex gap-1">
                                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input Area (Sticky Footer) -->
                    <div class="px-6 py-4 border-t border-slate-200 bg-white shadow-lg">
                        <form action="{{ route('messaging.send') }}" method="POST" id="messageForm" class="flex items-end gap-3">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                            
                            <!-- Emoji Button -->
                            <button 
                                type="button"
                                class="flex-shrink-0 p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors"
                                title="Add emoji">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>

                            <!-- Attachment Button -->
                            <button 
                                type="button"
                                class="flex-shrink-0 p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors"
                                title="Attach file">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>

                            <!-- Message Input -->
                            <div class="flex-1 relative">
                                <textarea 
                                    name="message" 
                                    id="messageInput"
                                    rows="1"
                                    required
                                    placeholder="Type a message..." 
                                    class="w-full px-4 py-3 pr-12 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-slate-50 text-slate-900 placeholder-slate-400"
                                    onkeydown="handleKeyDown(event)"
                                    oninput="autoResizeTextarea(this)"></textarea>
                                
                                <!-- Subject Input (Hidden by default, can be toggled) -->
                                <input 
                                    type="text" 
                                    name="subject" 
                                    id="subjectInput"
                                    placeholder="Subject (optional)" 
                                    class="hidden w-full px-4 py-2 mb-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 text-sm">
                            </div>

                            <!-- Send Button -->
                            <button 
                                type="submit" 
                                id="sendButton"
                                class="flex-shrink-0 inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Empty State - No Conversation Selected -->
                    <div class="flex-1 flex items-center justify-center bg-gradient-to-b from-slate-50 to-white">
                        <div class="text-center px-6">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-6">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 mb-2">No conversation selected</h3>
                            <p class="text-sm text-slate-600 mb-6 max-w-sm mx-auto">
                                Select a conversation from the sidebar or start a new one to begin messaging
                            </p>
                            <button 
                                type="button" 
                                onclick="openNewMessageModal()" 
                                class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Start New Conversation
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div 
        id="newMessageModal" 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4"
        onclick="if(event.target === this) closeNewMessageModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">New Message</h3>
                <button 
                    onclick="closeNewMessageModal()" 
                    class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('messaging.send') }}" method="POST" id="newMessageForm" class="flex-1 overflow-y-auto p-6">
                @csrf
                <div class="space-y-4">
                    <div class="relative">
                        <label for="userSearchInput" class="block text-sm font-medium text-slate-700 mb-2">To</label>
                        <input 
                            type="text" 
                            id="userSearchInput"
                            placeholder="Search for a user by name or email..." 
                            class="w-full px-4 py-2.5 pl-10 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                            autocomplete="off"
                            onkeyup="filterUsers(this.value)"
                            onfocus="showUserDropdown()">
                        <svg class="absolute left-3 top-9 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="hidden" name="receiver_id" id="modal_receiver_id" required>
                        
                        <!-- User Dropdown List -->
                        <div id="userDropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-slate-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                            @foreach($users as $user)
                                <div 
                                    class="user-option px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-b-0 transition-colors"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ strtolower($user->name) }}"
                                    data-user-email="{{ strtolower($user->email) }}"
                                    onclick="selectUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                                    <div class="flex items-center gap-3">
                                        @if($user->photo_url)
                                            <img src="{{ asset($user->photo_url) }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center font-semibold text-sm">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Selected User Display -->
                        <div id="selectedUserDisplay" class="hidden mt-2 px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span id="selectedUserName" class="text-sm font-medium text-emerald-900"></span>
                                </div>
                                <button 
                                    type="button"
                                    onclick="clearUserSelection()"
                                    class="text-emerald-600 hover:text-emerald-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="modal_subject" class="block text-sm font-medium text-slate-700 mb-2">Subject (Optional)</label>
                        <input 
                            type="text" 
                            name="subject" 
                            id="modal_subject" 
                            placeholder="Enter subject..." 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    </div>
                    <div>
                        <label for="modal_message" class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                        <textarea 
                            name="message" 
                            id="modal_message" 
                            rows="5" 
                            required 
                            placeholder="Type your message..." 
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none bg-white"></textarea>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeNewMessageModal()" 
                    class="px-4 py-2 border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button 
                    type="submit" 
                    form="newMessageForm"
                    class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                    Send Message
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-resize textarea
        function autoResizeTextarea(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        // Handle Enter key (Send on Enter, new line on Shift+Enter)
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                document.getElementById('messageForm')?.requestSubmit();
            }
        }

        // Filter conversations
        function filterConversations(searchTerm) {
            const items = document.querySelectorAll('.conversation-item');
            const term = searchTerm.toLowerCase();
            
            items.forEach(item => {
                const name = item.getAttribute('data-conversation-name') || '';
                if (name.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // User search functions
        // Users are available in the dropdown, no need to store separately

        function filterUsers(searchTerm) {
            const term = searchTerm.toLowerCase().trim();
            const dropdown = document.getElementById('userDropdown');
            const options = dropdown.querySelectorAll('.user-option');
            
            if (term === '') {
                showUserDropdown();
                return;
            }
            
            let hasMatches = false;
            options.forEach(option => {
                const name = option.getAttribute('data-user-name') || '';
                const email = option.getAttribute('data-user-email') || '';
                
                if (name.includes(term) || email.includes(term)) {
                    option.style.display = 'block';
                    hasMatches = true;
                } else {
                    option.style.display = 'none';
                }
            });
            
            if (hasMatches) {
                showUserDropdown();
            } else {
                hideUserDropdown();
            }
        }

        function showUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const selectedDisplay = document.getElementById('selectedUserDisplay');
            if (!selectedDisplay.classList.contains('hidden')) {
                return; // Don't show if user is already selected
            }
            dropdown.classList.remove('hidden');
        }

        function hideUserDropdown() {
            document.getElementById('userDropdown').classList.add('hidden');
        }

        function selectUser(userId, userName, userEmail) {
            document.getElementById('modal_receiver_id').value = userId;
            document.getElementById('userSearchInput').value = '';
            document.getElementById('selectedUserName').textContent = `${userName} (${userEmail})`;
            document.getElementById('selectedUserDisplay').classList.remove('hidden');
            hideUserDropdown();
        }

        function clearUserSelection() {
            document.getElementById('modal_receiver_id').value = '';
            document.getElementById('userSearchInput').value = '';
            document.getElementById('selectedUserDisplay').classList.add('hidden');
            document.getElementById('userSearchInput').focus();
        }

        // Modal functions
        function openNewMessageModal() {
            document.getElementById('newMessageModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('userSearchInput')?.focus();
            }, 100);
        }

        function closeNewMessageModal() {
            document.getElementById('newMessageModal').classList.add('hidden');
            document.getElementById('newMessageForm')?.reset();
            clearUserSelection();
            hideUserDropdown();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const searchInput = document.getElementById('userSearchInput');
            const dropdown = document.getElementById('userDropdown');
            if (searchInput && dropdown && !searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                hideUserDropdown();
            }
        });

        // Toggle edit form
        function toggleEdit(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.toggle('hidden');
            }
        }

        // Auto-scroll to bottom on load
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Add animation to messages
                const messages = document.querySelectorAll('.message-item');
                messages.forEach((msg, index) => {
                    msg.style.opacity = '0';
                    msg.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        msg.style.transition = 'all 0.3s ease';
                        msg.style.opacity = '1';
                        msg.style.transform = 'translateY(0)';
                    }, index * 50);
                });

                // Initialize Realtime Chat if available
                const currentUserId = messagesContainer.getAttribute('data-current-user-id');
                const otherUserId = messagesContainer.getAttribute('data-other-user-id');
                
                if (currentUserId && otherUserId && window.RealtimeChat) {
                    const supabaseConfig = {
                        url: @json(config('supabase.url')),
                        anonKey: @json(config('supabase.anon_key')),
                        currentUserId: parseInt(currentUserId),
                        otherUserId: parseInt(otherUserId)
                    };
                    
                    if (supabaseConfig.url && supabaseConfig.anonKey) {
                        window.realtimeChat = new window.RealtimeChat(supabaseConfig);
                    }
                }
            }

            // Enhanced form submission with AJAX
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const sendButton = document.getElementById('sendButton');
                    const messageInput = document.getElementById('messageInput');
                    const originalButtonHTML = sendButton.innerHTML;
                    
                    // Disable button and show loading
                    sendButton.disabled = true;
                    sendButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    
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
                            // Reload page to show new message (backend redirects)
                            window.location.reload();
                        } else {
                            throw new Error('Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please try again.');
                    } finally {
                        sendButton.disabled = false;
                        sendButton.innerHTML = originalButtonHTML;
                    }
                });
            }

            // Handle new message form submission
            const newMessageForm = document.getElementById('newMessageForm');
            if (newMessageForm) {
                newMessageForm.addEventListener('submit', function(e) {
                    // Let form submit normally - backend will redirect
                    // Modal will close on redirect
                });
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (window.realtimeChat) {
                window.realtimeChat.destroy();
            }
        });
    </script>
</x-app-layout>
