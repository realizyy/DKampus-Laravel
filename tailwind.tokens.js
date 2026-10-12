// Token warna DKampus: dipakai tailwind.config.js dan scripts/contrast-check.mjs
export const colors = {
    // Oranye DKampus. 500 = warna merek Figma (#F9832A); isi tombol berteks putih memakai 700 (kontras 4,6:1)
    brand: {
        50: '#FFF6EE',
        100: '#FFEBD9',
        200: '#FED7B5',
        300: '#FDBA84',
        400: '#FB9B54',
        500: '#F9832A',
        600: '#E96A10',
        700: '#C2530C',
        800: '#9A4112',
        900: '#7C3612',
        950: '#431A07',
    },
    // Kanvas krem hangat (bukan #FFF), lapisan, dan garis
    canvas: '#FBF7F1',
    surface: '#FFFDF9',
    sunken: '#F4EEE4',
    line: {
        DEFAULT: '#E8E0D4',
        strong: '#D8CDBB',
    },
    // Obsidian hangat untuk teks
    ink: {
        DEFAULT: '#1F1A14',
        700: '#3A332A',
        600: '#51483C',
        500: '#655B4D',
        400: '#7A6F5F',
        300: '#B0A592',
    },
    // Status: tidak ada ungu/biru neon
    moss: { 50: '#F0F6EC', 100: '#DDEBD4', 600: '#3F6B3A', 700: '#2F5430' },
    brick: { 50: '#FBEDEA', 100: '#F6D5CD', 600: '#B3402A', 700: '#8F3220' },
    amber: { 50: '#FFF7E0', 100: '#FDEBB5', 700: '#8A5A00' },
    teal: { 50: '#E6F4F2', 100: '#C7E6E1', 700: '#1F6B63' },
};
