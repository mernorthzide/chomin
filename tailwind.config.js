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
            colors: {
                brand: {
                    black: '#000000',
                    white: '#FFFFFF',
                    brown: '#3C2415',
                    gray: '#F5F5F5',
                    'gray-dark': '#333333',
                    'gray-medium': '#666666',
                    'gray-light': '#999999',
                    'gray-border': '#E5E5E5',
                },
            },
            fontFamily: {
                sans: ['IBM Plex Sans Thai', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [forms],
};
