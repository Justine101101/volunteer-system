/**
 * Supabase Realtime Chat Implementation
 * Handles real-time message updates without page reloads
 */

import { createClient } from '@supabase/supabase-js';

class RealtimeChat {
    constructor(config) {
        this.supabaseUrl = config.url;
        this.supabaseAnonKey = config.anonKey;
        this.currentUserId = config.currentUserId;
        this.otherUserId = config.otherUserId;
        this.messagesContainer = null;
        this.subscription = null;
        this.supabase = null;
        
        // Initialize Supabase client
        this.init();
    }

    /**
     * Initialize Supabase client
     */
    init() {
        if (!this.supabaseUrl || !this.supabaseAnonKey) {
            console.warn('Supabase configuration missing. Realtime features disabled.');
            return;
        }

        this.supabase = createClient(this.supabaseUrl, this.supabaseAnonKey);
        this.messagesContainer = document.getElementById('messagesContainer');
        
        if (!this.messagesContainer) {
            console.warn('Messages container not found.');
            return;
        }

        // Only subscribe if we have both user IDs (active conversation)
        if (this.currentUserId && this.otherUserId) {
            this.subscribeToMessages();
        }
    }

    /**
     * Subscribe to new messages for the active conversation
     */
    subscribeToMessages() {
        if (!this.supabase || !this.currentUserId || !this.otherUserId) {
            return;
        }

        // Subscribe to INSERT events on messages table
        // Note: Supabase Realtime filters work with PostgREST syntax
        // We'll use a channel filter to listen to all messages, then filter in JavaScript
        // This is more reliable than complex filter syntax
        this.subscription = this.supabase
            .channel(`messages:${this.currentUserId}:${this.otherUserId}`)
            .on(
                'postgres_changes',
                {
                    event: 'INSERT',
                    schema: 'public',
                    table: 'messages'
                },
                (payload) => {
                    // Filter in JavaScript to ensure we only show messages for this conversation
                    const message = payload.new;
                    const isRelevant = 
                        (message.sender_id === this.currentUserId && message.receiver_id === this.otherUserId) ||
                        (message.sender_id === this.otherUserId && message.receiver_id === this.currentUserId);
                    
                    if (isRelevant) {
                        this.handleNewMessage(message);
                    }
                }
            )
            .subscribe((status) => {
                if (status === 'SUBSCRIBED') {
                    console.log('✅ Subscribed to real-time messages');
                } else if (status === 'CHANNEL_ERROR') {
                    console.error('❌ Error subscribing to messages');
                } else if (status === 'TIMED_OUT') {
                    console.warn('⏱️ Subscription timed out, retrying...');
                    // Retry subscription after a delay
                    setTimeout(() => this.subscribeToMessages(), 2000);
                }
            });
    }

    /**
     * Handle new message received via Realtime
     */
    async handleNewMessage(message) {
        // Handle both UUID strings and integer IDs (for compatibility)
        const messageSenderId = String(message.sender_id);
        const currentUserIdStr = String(this.currentUserId);
        
        // Don't show messages we just sent (they're already in the DOM after form submit)
        if (messageSenderId === currentUserIdStr) {
            return;
        }

        // Check if message already exists in DOM (prevent duplicates)
        const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
        if (existingMessage) {
            return;
        }

        // Fetch sender details if needed
        const sender = await this.getUserDetails(message.sender_id);
        
        // Render the new message
        this.appendMessage(message, sender);
        
        // Scroll to bottom
        this.scrollToBottom();
        
        // Optional: Play notification sound or show notification
        this.showNotification(sender, message);
    }

    /**
     * Get user details (name, email) from Supabase
     */
    async getUserDetails(userId) {
        try {
            const { data, error } = await this.supabase
                .from('users')
                .select('id, name, email')
                .eq('id', userId)
                .single();

            if (error) {
                console.error('Error fetching user:', error);
                return { name: 'Unknown', email: '' };
            }

            return data || { name: 'Unknown', email: '' };
        } catch (error) {
            console.error('Error fetching user details:', error);
            return { name: 'Unknown', email: '' };
        }
    }

    /**
     * Append a new message to the messages container
     */
    appendMessage(message, sender) {
        if (!this.messagesContainer) {
            return;
        }

        // Handle both UUID strings and integer IDs
        const messageSenderId = String(message.sender_id);
        const currentUserIdStr = String(this.currentUserId);
        const isOwnMessage = messageSenderId === currentUserIdStr;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`;
        messageDiv.setAttribute('data-message-id', message.id);

        // Format created_at date
        const createdAt = new Date(message.created_at);
        const formattedDate = createdAt.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        // Sanitize message content (basic XSS prevention)
        const sanitize = (str) => {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        };

        const sanitizedSubject = message.subject ? sanitize(message.subject) : '';
        const sanitizedMessage = sanitize(message.message || '');

        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isOwnMessage ? '' : 'shadow'}"
                 style="${isOwnMessage ? 'background-color:#047857;color:#ffffff;' : 'background-color:#ffffff;color:#111827;'}">
                ${sanitizedSubject ? `
                    <p class="font-semibold text-sm mb-1" style="${isOwnMessage ? 'color:#d1fae5;' : 'color:#374151;'}">
                        ${sanitizedSubject}
                    </p>
                ` : ''}
                <p class="text-sm whitespace-pre-wrap">${sanitizedMessage}</p>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-xs" style="${isOwnMessage ? 'color:#d1fae5;' : 'color:#6b7280;'}">
                        ${formattedDate}
                        ${isOwnMessage ? ' • <span>Sent</span>' : ''}
                    </p>
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" class="underline hover:opacity-80"
                                onclick="prefillReply('${sanitizedMessage.replace(/'/g, "\\'").replace(/\n/g, '\\n')}')"
                                style="${isOwnMessage ? 'color:#d1fae5;' : 'color:#6b7280;'}">Reply</button>
                    </div>
                </div>
            </div>
        `;

        // Add fade-in animation
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(10px)';
        messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        this.messagesContainer.appendChild(messageDiv);

        // Trigger animation
        requestAnimationFrame(() => {
            messageDiv.style.opacity = '1';
            messageDiv.style.transform = 'translateY(0)';
        });
    }

    /**
     * Scroll messages container to bottom
     */
    scrollToBottom() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTo({
                top: this.messagesContainer.scrollHeight,
                behavior: 'smooth'
            });
        }
    }

    /**
     * Show notification for new message (optional)
     */
    showNotification(sender, message) {
        // Only show notification if page is not in focus
        if (document.hidden) {
            // Browser notification (requires permission)
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(`New message from ${sender.name}`, {
                    body: message.subject ? `${message.subject}: ${message.message}` : message.message,
                    icon: '/favicon.ico',
                    tag: `message-${message.id}`
                });
            }
        }

        // Update page title with unread count
        const originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
        document.title = `(1) ${originalTitle}`;
    }

    /**
     * Update conversation (when user switches to different chat)
     */
    updateConversation(otherUserId) {
        this.otherUserId = otherUserId;
        
        // Unsubscribe from previous conversation
        if (this.subscription) {
            this.supabase.removeChannel(this.subscription);
            this.subscription = null;
        }

        // Subscribe to new conversation
        if (this.otherUserId) {
            this.subscribeToMessages();
        }
    }

    /**
     * Cleanup: Unsubscribe when leaving the page
     */
    destroy() {
        if (this.subscription) {
            this.supabase.removeChannel(this.subscription);
            this.subscription = null;
        }
    }
}

// Export for use in other modules
export default RealtimeChat;

// Auto-initialize if config is available in window
if (typeof window !== 'undefined') {
    window.initRealtimeChat = function(config) {
        return new RealtimeChat(config);
    };
}
