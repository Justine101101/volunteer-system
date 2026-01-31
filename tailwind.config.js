import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'lions-green': {
                    DEFAULT: '#1a5f3f',
                    light: '#2d7a5a',
                    lighter: '#90EE90',
                },
                'lions-purple': {
                    DEFAULT: '#4a1a5f',
                },
            },
        },
    },

    plugins: [forms],
};
