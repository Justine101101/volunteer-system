import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// UX animations and helpers (non-intrusive)
import './ux';

// Realtime chat (only loads on messaging pages)
import RealtimeChat from './chat';
window.RealtimeChat = RealtimeChat;
