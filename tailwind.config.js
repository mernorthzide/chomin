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
                    black: '#1a1a1a',
                    white: '#fafafa',
                    gray: '#F5F5F5',
                    'gray-dark': '#333333',
                    'gray-medium': '#555555',
                    'gray-light': '#767676',
                    'gray-border': '#E0E0E0',
                },
            },
            fontFamily: {
                sans: ['PK Maehongson', ...defaultTheme.fontFamily.sans],
                serif: ['Agatho Light CAPS', ...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [forms],
};
