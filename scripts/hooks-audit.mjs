// Audit "hook" yang dipakai JS/test/route di setiap view Blade, supaya redesain tidak memutus kontraknya.
//   node scripts/hooks-audit.mjs snapshot <label>   -> tulis docs/hooks/<label>.json
//   node scripts/hooks-audit.mjs check <label>      -> bandingkan view saat ini dengan snapshot; exit 1 bila ada yang hilang
// Hook = id/kelas yang dirujuk JS (public/js, resources/js, <script> inline), atribut name=, route('...'), action=, data-*, dan
// teks yang di-assert test (assertSee). Hook yang sengaja dibuang dicatat di docs/hooks/removed.md lalu dikecualikan di check.
import { existsSync, mkdirSync, readFileSync, readdirSync, statSync, writeFileSync } from 'node:fs';
import { dirname, join, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const walk = (dir, ext, out = []) => {
    if (!existsSync(dir)) return out;
    for (const f of readdirSync(dir)) {
        const p = join(dir, f);
        statSync(p).isDirectory() ? walk(p, ext, out) : ext.some((e) => p.endsWith(e)) && out.push(p);
    }
    return out;
};
const read = (p) => readFileSync(p, 'utf8');
const uniq = (a) => [...new Set(a)].sort();

const views = walk(join(root, 'resources/views'), ['.blade.php']);
const jsFiles = [...walk(join(root, 'public/js'), ['.js']), ...walk(join(root, 'resources/js'), ['.js'])];

// Semua selector yang disebut JS (file + <script> inline di view)
const scriptsInline = views.flatMap((v) => [...read(v).matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/g)].map((m) => m[1]));
const jsSource = [...jsFiles.map(read), ...scriptsInline].join('\n');
const jsIds = new Set([
    ...[...jsSource.matchAll(/getElementById\(\s*['"`]([^'"`$]+)['"`]/g)].map((m) => m[1]),
    ...[...jsSource.matchAll(/['"`][^'"`\n]*#([A-Za-z][\w-]*)[^'"`\n]*['"`]/g)].map((m) => m[1]),
]);
const jsClasses = new Set([
    ...[...jsSource.matchAll(/(?:querySelector(?:All)?|getElementsByClassName|\$|closest|matches)\(\s*['"`]([^'"`]+)['"`]/g)]
        .flatMap((m) => [...m[1].matchAll(/\.([A-Za-z_][\w-]*)/g)].map((x) => x[1])),
    ...[...jsSource.matchAll(/getElementsByClassName\(\s*['"`]([^'"`]+)['"`]/g)].map((m) => m[1]),
]);
const testTexts = uniq(
    walk(join(root, 'tests'), ['.php']).flatMap((t) => [...read(t).matchAll(/assertSee(?:Text)?\(\s*'([^']+)'/g)].map((m) => m[1])),
);

function auditView(path) {
    const src = read(path);
    const html = src.replace(/\{\{--[\s\S]*?--\}\}/g, '').replace(/<!--[\s\S]*?-->/g, '');
    const ids = uniq([...html.matchAll(/\bid=["']([^"'{}$]+)["']/g)].map((m) => m[1]));
    const classes = uniq([...html.matchAll(/\bclass=["']([^"']*)["']/g)].flatMap((m) => m[1].split(/\s+/)).filter(Boolean));
    return {
        _ids: ids,
        _classes: classes,
        hookIds: ids.filter((i) => jsIds.has(i)),
        hookClasses: classes.filter((c) => jsClasses.has(c)),
        names: uniq([...html.matchAll(/\bname=["']([^"'{}$]+)["']/g)].map((m) => m[1])),
        routes: uniq([...html.matchAll(/route\(\s*['"]([^'"]+)['"]/g)].map((m) => m[1])),
        dataAttrs: uniq([...html.matchAll(/\b(data-[a-z0-9-]+)=/g)].map((m) => m[1])),
        texts: testTexts.filter((t) => src.includes(t)),
    };
}

const [mode, label = 'baseline'] = process.argv.slice(2);
const file = join(root, 'docs/hooks', `${label}.json`);
const current = Object.fromEntries(views.map((v) => [relative(join(root, 'resources/views'), v), auditView(v)]));
const strip = (o) => Object.fromEntries(Object.entries(o).filter(([k]) => !k.startsWith('_')));

if (mode === 'snapshot') {
    mkdirSync(dirname(file), { recursive: true });
    writeFileSync(file, JSON.stringify(Object.fromEntries(Object.entries(current).map(([k, v]) => [k, strip(v)])), null, 1) + '\n');
    console.log(`Snapshot ${Object.keys(current).length} view -> ${relative(root, file)}`);
} else if (mode === 'check') {
    const base = JSON.parse(read(file));
    const removedPath = join(root, 'docs/hooks/removed.md');
    const removed = existsSync(removedPath) ? read(removedPath) : '';
    let missing = 0;
    for (const [view, hooks] of Object.entries(base)) {
        if (!current[view]) {
            if (!removed.includes(`${view}|view|*`)) { console.log(`HILANG  view ${view}`); missing++; }
            continue;
        }
        for (const [kind, list] of Object.entries(hooks)) {
            for (const item of list) {
                // id/kelas dianggap ada selama masih ada di view (walau JS-nya sudah tidak merujuk lagi)
                const pool = kind === 'hookIds' ? current[view]._ids : kind === 'hookClasses' ? current[view]._classes : current[view][kind];
                if (!pool.includes(item) && !removed.includes(`${view}|${kind}|${item}`)) {
                    console.log(`HILANG  ${view}  ${kind}: ${item}`);
                    missing++;
                }
            }
        }
    }
    console.log(missing ? `\n${missing} hook hilang (catat sengaja di docs/hooks/removed.md: "<view>|<jenis>|<nilai>")` : 'Semua hook masih ada');
    process.exitCode = missing ? 1 : 0;
} else {
    console.log('Pakai: node scripts/hooks-audit.mjs snapshot|check <label>');
    process.exitCode = 1;
}
