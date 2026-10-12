// Cek kontras WCAG (teks normal >= 4.5:1, elemen non-teks >= 3:1) untuk pasangan warna yang dipakai komponen.
// Pakai: node scripts/contrast-check.mjs   (exit 1 bila ada pasangan gagal)
import { colors as c } from '../tailwind.tokens.js';
const hex = {
    white: '#FFFFFF',
    canvas: c.canvas, surface: c.surface, sunken: c.sunken,
    ink: c.ink.DEFAULT, ink700: c.ink[700], ink600: c.ink[600], ink500: c.ink[500], ink400: c.ink[400],
    brand500: c.brand[500], brand600: c.brand[600], brand700: c.brand[700], brand800: c.brand[800],
    brand100: c.brand[100], brand50: c.brand[50],
    brick600: c.brick[600], brick700: c.brick[700], brick100: c.brick[100],
    moss700: c.moss[700], moss100: c.moss[100],
    amber700: c.amber[700], amber100: c.amber[100],
    teal700: c.teal[700], teal100: c.teal[100],
};

const lum = (h) => {
    const [r, g, b] = [1, 3, 5].map((i) => parseInt(h.slice(i, i + 2), 16) / 255).map((v) => (v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4));
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
};
const ratio = (a, b) => {
    const [x, y] = [lum(hex[a]), lum(hex[b])].sort((p, q) => q - p);
    return (x + 0.05) / (y + 0.05);
};

// [teks/ikon, latar, minimum, keterangan]
const PAIRS = [
    ['ink', 'canvas', 4.5, 'teks utama di kanvas'],
    ['ink', 'surface', 4.5, 'teks utama di kartu'],
    ['ink700', 'surface', 4.5, 'label field'],
    ['ink600', 'surface', 4.5, 'teks sekunder'],
    ['ink500', 'canvas', 4.5, 'teks muted di kanvas'],
    ['ink500', 'surface', 4.5, 'teks muted di kartu'],
    ['ink400', 'surface', 4.5, 'placeholder input'],
    ['white', 'brand700', 4.5, 'tombol primary'],
    ['white', 'brand800', 4.5, 'tombol primary (hover)'],
    ['brand800', 'brand100', 4.5, 'badge/tombol soft'],
    ['brand700', 'surface', 4.5, 'label tab aktif / tautan'],
    ['brand600', 'surface', 3, 'ikon tab aktif (non-teks)'],
    // Bump bottom-nav bersifat dekoratif (aria-hidden); tab aktif dinyatakan juga oleh warna teks/ikon dan aria-current
    ['brand500', 'surface', 2, 'bump nav (dekoratif)'],
    ['white', 'brick600', 4.5, 'tombol danger'],
    ['brick700', 'brick100', 4.5, 'badge batal'],
    ['moss700', 'moss100', 4.5, 'badge selesai'],
    ['amber700', 'amber100', 4.5, 'badge menunggu'],
    ['teal700', 'teal100', 4.5, 'badge diantar'],
    ['canvas', 'ink', 4.5, 'chip aktif'],
    ['ink', 'brand500', 4.5, 'teks gelap di oranye terang'],
];

let failed = 0;
for (const [fg, bg, min, note] of PAIRS) {
    const r = ratio(fg, bg);
    const ok = r >= min;
    if (!ok) failed++;
    console.log(`${ok ? 'OK  ' : 'GAGAL'} ${r.toFixed(2).padStart(5)}:1 (min ${min})  ${fg} on ${bg}  - ${note}`);
}
console.log(failed ? `\n${failed} pasangan gagal` : '\nSemua pasangan lolos');
process.exitCode = failed ? 1 : 0;
