// Salin ikon Lucide terpilih dari node_modules ke resources/svg/lucide (di-commit),
// supaya <x-ui.icon> bisa meng-inline SVG tanpa JS dan tanpa paket composer tambahan.
// Pakai: node scripts/sync-icons.mjs  (tambahkan nama ikon di ICONS lalu jalankan ulang)
import { copyFileSync, existsSync, mkdirSync, readdirSync, rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const from = join(root, 'node_modules/lucide-static/icons');
const to = join(root, 'resources/svg/lucide');

const ICONS = [
    // navigasi & aksi umum
    'house', 'clipboard-list', 'shopping-bag', 'user', 'users', 'search', 'heart', 'star', 'map-pin', 'navigation',
    'message-circle', 'send', 'paperclip', 'camera', 'image', 'bell', 'settings', 'menu', 'x', 'check', 'plus', 'minus',
    'chevron-left', 'chevron-right', 'chevron-down', 'chevron-up', 'arrow-left', 'arrow-right', 'arrow-up-right',
    'pencil', 'trash-2', 'log-out', 'eye', 'eye-off', 'filter', 'copy', 'external-link', 'refresh-cw', 'ellipsis',
    // domain DKampus
    'store', 'utensils', 'bike', 'package', 'receipt', 'wallet', 'qr-code', 'tag', 'ticket-percent', 'flame', 'sparkles',
    'clock', 'phone', 'mail', 'lock', 'shield-check', 'file-text', 'info', 'circle-alert', 'circle-check', 'triangle-alert',
    'loader', 'layout-dashboard', 'command', 'corner-down-left', 'badge-check', 'upload', 'download', 'map', 'history',
    'circle-user', 'key-round', 'trash', 'square-pen', 'list-filter', 'calendar',
];

mkdirSync(to, { recursive: true });
for (const f of readdirSync(to)) rmSync(join(to, f));

const missing = [];
for (const name of ICONS) {
    const src = join(from, `${name}.svg`);
    if (existsSync(src)) copyFileSync(src, join(to, `${name}.svg`));
    else missing.push(name);
}

console.log(`${ICONS.length - missing.length} ikon disalin ke resources/svg/lucide`);
if (missing.length) {
    console.error(`Tidak ditemukan di lucide-static: ${missing.join(', ')}`);
    process.exitCode = 1;
}
