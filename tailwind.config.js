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
                    'gray-light': '#6b6b6b', // AA-safe: 5.1:1 on white, 4.9:1 on #F5F5F5 (was #767676 = 4.35:1, failed WCAG AA)
                    'gray-border': '#E0E0E0',
                    // Semantic status — deep/desaturated to fit the B&W editorial system, all ≥4.5:1 on white
                    danger: '#b3261e',
                    success: '#1f7a3d',
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
