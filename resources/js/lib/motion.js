import { animate, inView, stagger } from 'motion';

const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Gerakan masuk: x-motion.rise | .fade | .pop  (+ .stagger untuk anak, + .inview untuk menunggu viewport)
// Nilai opsional: x-motion.rise="{ delay: 0.1, duration: 0.5, y: 12 }"
const PRESETS = {
    rise: (o) => ({ opacity: [0, 1], transform: [`translateY(${o.y ?? 10}px)`, 'translateY(0px)'] }),
    fade: () => ({ opacity: [0, 1] }),
    pop: () => ({ opacity: [0, 1], transform: ['scale(0.96)', 'scale(1)'] }),
};

function done(el) {
    el.setAttribute('data-motion-done', '');
    el.style.opacity = '';
    el.style.transform = '';
}

export function registerMotion(Alpine) {
    Alpine.directive('motion', (el, { modifiers, expression }, { evaluate }) => {
        const options = expression ? evaluate(expression) : {};
        const name = ['rise', 'fade', 'pop'].find((m) => modifiers.includes(m)) ?? 'rise';
        const targets = modifiers.includes('stagger') ? Array.from(el.children) : [el];

        const run = () => {
            if (reduced()) {
                done(el);
                targets.forEach(done);
                return;
            }
            const keyframes = PRESETS[name](options);
            const controls = animate(targets, keyframes, {
                duration: options.duration ?? 0.45,
                delay: modifiers.includes('stagger') ? stagger(options.stagger ?? 0.05, { startDelay: options.delay ?? 0 }) : (options.delay ?? 0),
                ease: [0.22, 1, 0.36, 1],
            });
            Promise.resolve(controls.finished ?? controls).then(() => {
                done(el);
                targets.forEach(done);
            });
        };

        // Anak yang di-stagger harus tersembunyi dulu, bukan hanya induknya
        if (modifiers.includes('stagger') && !reduced()) {
            targets.forEach((t) => (t.style.opacity = '0'));
            el.setAttribute('data-motion-done', '');
        }

        if (modifiers.includes('inview')) {
            inView(el, () => {
                run();
            }, { amount: 0.2 });
        } else {
            requestAnimationFrame(run);
        }
    });
}

// Indikator "bump" bottom-nav: geser ke tab aktif dengan pegas halus
export function moveBump(bump, tab) {
    if (!bump || !tab) return;
    const x = tab.offsetLeft + tab.offsetWidth / 2 - bump.offsetWidth / 2;
    if (reduced()) {
        bump.style.transform = `translateX(${x}px)`;
        return;
    }
    animate(bump, { transform: `translateX(${x}px)` }, { type: 'spring', stiffness: 520, damping: 38 });
}

// Alpine.data('bottomNav', (jumlahTab, tabAktif)): bump geser dari tab sebelumnya (sessionStorage) ke tab aktif
export function registerBottomNav(Alpine) {
    Alpine.data('bottomNav', (count, active) => ({
        init() {
            const pct = (i) => ((i + 0.5) / count) * 100;
            let prev = null;
            try {
                prev = sessionStorage.getItem('dk.nav');
                sessionStorage.setItem('dk.nav', String(active));
            } catch (e) {
                /* storage diblokir: lewati animasi */
            }
            const bump = this.$refs.bump;
            if (!bump || prev === null || Number(prev) === active || reduced()) return;
            animate(bump, { left: [`${pct(Number(prev))}%`, `${pct(active)}%`] }, { type: 'spring', stiffness: 520, damping: 38 });
        },
    }));
}
