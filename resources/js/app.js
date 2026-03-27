import './bootstrap';

// Dark mode - must be imported early to prevent flash
import './dark-mode';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// UX animations and helpers (non-intrusive)
import './ux';

// Realtime chat: lazy-load only when a messaging container is present.
window.loadRealtimeChat = async function loadRealtimeChat() {
    if (window.RealtimeChat) {
        return window.RealtimeChat;
    }

    const mod = await import('./chat');
    window.RealtimeChat = mod.default;
    return window.RealtimeChat;
};

if (document.getElementById('messagesContainer')) {
    window.loadRealtimeChat().catch((error) => {
        console.error('Failed to load realtime chat module:', error);
    });
}
