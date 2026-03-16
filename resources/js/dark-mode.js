/**
 * Dark Mode Handler
 * Applies dark mode based on user preference stored in database
 */

// Get user's dark mode preference from the page
function getUserDarkModePreference() {
    // Check if user is authenticated and has dark mode preference
    const darkModeMeta = document.querySelector('meta[name="user-dark-mode"]');
    if (darkModeMeta) {
        return darkModeMeta.getAttribute('content') === '1' || darkModeMeta.getAttribute('content') === 'true';
    }
    
    // Fallback: check localStorage
    const stored = localStorage.getItem('darkMode');
    if (stored !== null) {
        return stored === 'true';
    }
    
    return false;
}

// Apply dark mode to the document
function applyDarkMode(enabled) {
    if (enabled) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
}

// Initialize dark mode on page load
function initDarkMode() {
    const isDarkMode = getUserDarkModePreference();
    applyDarkMode(isDarkMode);
}

// Apply dark mode immediately before page renders (to prevent flash)
(function() {
    // Check localStorage first for immediate application
    const stored = localStorage.getItem('darkMode');
    if (stored === 'true') {
        document.documentElement.classList.add('dark');
    }
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDarkMode);
} else {
    initDarkMode();
}

// Handle dark mode toggle changes (for immediate feedback)
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'darkModeToggle' && e.target.type === 'checkbox') {
        applyDarkMode(e.target.checked);
    }
});

// Export for use in other scripts
window.DarkMode = {
    apply: applyDarkMode,
    init: initDarkMode,
    getPreference: getUserDarkModePreference,
};
