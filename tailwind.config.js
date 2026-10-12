import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';
import { colors } from './tailwind.tokens.js';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/View/Components/**/*.php',
    ],

    // Satu tema terang. Kelas dark: bawaan Breeze tidak lagi mengikuti prefers-color-scheme.
    darkMode: 'class',

    theme: {
        extend: {
            colors,
            fontFamily: {
                display: ['"Bricolage Grotesque Variable"', ...defaultTheme.fontFamily.sans],
                sans: ['"Source Sans 3 Variable"', ...defaultTheme.fontFamily.sans],
                mono: ['"Geist Mono Variable"', ...defaultTheme.fontFamily.mono],
            },
            letterSpacing: {
                tightest: '-0.045em',
            },
            boxShadow: {
                // Kedalaman = ring 1px + highlight dalam, bukan drop-shadow keras
                hairline: '0 0 0 1px rgb(31 26 20 / 0.07)',
                card: '0 0 0 1px rgb(31 26 20 / 0.06), inset 0 1px 0 rgb(255 255 255 / 0.75), 0 1px 2px rgb(31 26 20 / 0.04)',
                pop: '0 0 0 1px rgb(31 26 20 / 0.08), inset 0 1px 0 rgb(255 255 255 / 0.6), 0 14px 32px -10px rgb(31 26 20 / 0.22)',
                nav: '0 -1px 0 rgb(31 26 20 / 0.06), 0 -10px 24px -14px rgb(31 26 20 / 0.16)',
                inset: 'inset 0 1px 2px rgb(31 26 20 / 0.06)',
            },
            transitionTimingFunction: {
                'out-quint': 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            keyframes: {
                'rise-in': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'pop-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                'toast-in': {
                    '0%': { opacity: '0', transform: 'translateY(-8px) scale(0.98)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
                shimmer: {
                    '100%': { transform: 'translateX(100%)' },
                },
            },
            animation: {
                'rise-in': 'rise-in 0.4s cubic-bezier(0.22, 1, 0.36, 1) both',
                'pop-in': 'pop-in 0.22s cubic-bezier(0.22, 1, 0.36, 1) both',
                'toast-in': 'toast-in 0.28s cubic-bezier(0.22, 1, 0.36, 1) both',
                shimmer: 'shimmer 1.6s infinite',
            },
        },
    },

    plugins: [forms, daisyui],

    // DaisyUI hanya untuk token + primitif (d-modal, d-toast, d-tooltip, d-kbd, d-loading).
    // Prefix d- karena kelas lama btn/menu di Pesanan, JastipPage, dan DaftarAlamat bentrok.
    daisyui: {
        prefix: 'd-',
        darkTheme: false,
        logs: false,
        themes: [
            {
                dkampus: {
                    primary: '#C2530C',
                    'primary-content': '#FFFFFF',
                    secondary: '#3A332A',
                    'secondary-content': '#FBF7F1',
                    accent: '#F9832A',
                    'accent-content': '#1F1A14',
                    neutral: '#1F1A14',
                    'neutral-content': '#FBF7F1',
                    'base-100': '#FFFDF9',
                    'base-200': '#FBF7F1',
                    'base-300': '#E8E0D4',
                    'base-content': '#1F1A14',
                    info: '#1F6B63',
                    'info-content': '#FFFFFF',
                    success: '#3F6B3A',
                    'success-content': '#FFFFFF',
                    warning: '#FDEBB5',
                    'warning-content': '#8A5A00',
                    error: '#B3402A',
                    'error-content': '#FFFFFF',
                    '--rounded-box': '0.875rem',
                    '--rounded-btn': '0.625rem',
                    '--rounded-badge': '0.375rem',
                    '--animation-btn': '0.15s',
                    '--animation-input': '0.15s',
                    '--btn-focus-scale': '0.97',
                    '--border-btn': '1px',
                    '--tab-border': '1px',
                    '--tab-radius': '0.5rem',
                },
            },
        ],
    },
};
