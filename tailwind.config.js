import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class', // Enable class-based dark mode

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Montserrat', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'lions-green': {
                    DEFAULT: '#008751',
                    light: '#10B981',
                    lighter: '#ECFDF5', // Mint background
                },
                'lions-purple': {
                    DEFAULT: '#4a1a5f',
                },
                'slate-dark': '#111827', // Dark slate for headings
                'light-gray': '#F9FAFB', // Main background
            },
            boxShadow: {
                'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'soft-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
            },
            animation: {
                'glow': 'glow 2s ease-in-out infinite alternate',
            },
            keyframes: {
                glow: {
                    '0%': { boxShadow: '0 0 5px rgba(0, 135, 81, 0.5)' },
                    '100%': { boxShadow: '0 0 20px rgba(0, 135, 81, 0.8)' },
                },
            },
        },
    },

    plugins: [forms],
};
