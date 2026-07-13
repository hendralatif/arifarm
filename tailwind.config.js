import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50:  '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                accent: {
                    50:  '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                'glow-green':  '0 0 24px -4px rgba(16,185,129,0.35)',
                'glow-amber':  '0 0 24px -4px rgba(251,191,36,0.35)',
                'card':        '0 2px 20px -4px rgba(15,23,42,0.10)',
                'card-hover':  '0 8px 40px -8px rgba(15,23,42,0.20)',
            },
            keyframes: {
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                shimmer: {
                    '0%':   { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                pulse_soft: {
                    '0%, 100%': { opacity: '1' },
                    '50%':      { opacity: '.65' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%':      { transform: 'translateY(-8px)' },
                },
                bounceIn: {
                    '0%':   { transform: 'scale(0.3)', opacity: '0' },
                    '50%':  { transform: 'scale(1.05)' },
                    '70%':  { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            animation: {
                'fade-in-up':  'fadeInUp 0.5s ease-out both',
                'shimmer':     'shimmer 2s linear infinite',
                'pulse-soft':  'pulse_soft 2.5s ease-in-out infinite',
                'float':       'float 3s ease-in-out infinite',
                'bounce-in':   'bounceIn 0.5s cubic-bezier(0.68,-0.55,0.265,1.55) both',
            },
            backgroundImage: {
                'hero-pattern':    "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
                'dots-pattern':    "radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px)",
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            },
        },
    },

    plugins: [forms],
};
