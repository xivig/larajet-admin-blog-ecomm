/**
 * Xivig Unified Dark/Light Mode Engine
 * Real-time Theme Switching with LocalStorage Persistence & System Preference Fallback
 */
(function() {
    'use strict';

    const STORAGE_KEY = 'xivig-theme';

    function getPreferredTheme() {
        const storedTheme = localStorage.getItem(STORAGE_KEY);
        if (storedTheme) {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
        if (document.body) {
            document.body.setAttribute('data-bs-theme', theme);
            document.body.setAttribute('data-theme', theme);
        }
        localStorage.setItem(STORAGE_KEY, theme);

        // Update any toggler buttons on the page
        document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
            btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
            btn.setAttribute('title', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        });

        // Sync with admin theme-view checkbox if present
        const adminCheckbox = document.getElementById('theme-view');
        if (adminCheckbox) {
            adminCheckbox.checked = (theme === 'dark');
        }

        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('xivig:theme:changed', { detail: { theme } }));
    }

    // Expose global controller
    window.XivigTheme = {
        get: getPreferredTheme,
        set: applyTheme,
        toggle: function() {
            const current = document.documentElement.getAttribute('data-bs-theme') || getPreferredTheme();
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            return next;
        }
    };

    // Immediately apply on script execution
    applyTheme(getPreferredTheme());

    // Bind event listeners once DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        applyTheme(getPreferredTheme());

        document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.XivigTheme.toggle();
            });
        });

        // Sync with admin checkbox if clicked
        const adminCheckbox = document.getElementById('theme-view');
        if (adminCheckbox) {
            adminCheckbox.addEventListener('change', function() {
                applyTheme(this.checked ? 'dark' : 'light');
            });
        }
    });

    // Listen for OS theme changes if user hasn't explicitly set a preference
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
})();
