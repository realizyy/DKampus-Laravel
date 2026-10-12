// Palet perintah (Cmd/Ctrl + K): daftar tautan per role, filter, navigasi panah, Enter untuk pindah
export function registerPalette(Alpine) {
    Alpine.data('palette', (items = []) => ({
        open: false,
        q: '',
        index: 0,
        items,
        get results() {
            const q = this.q.trim().toLowerCase();
            return q ? this.items.filter((i) => (i.label + ' ' + (i.hint ?? '')).toLowerCase().includes(q)) : this.items;
        },
        init() {
            window.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    this.toggle();
                }
            });
        },
        toggle() {
            this.open = !this.open;
            this.q = '';
            this.index = 0;
            if (this.open) this.$nextTick(() => this.$refs.input?.focus());
        },
        move(delta) {
            const n = this.results.length;
            if (n) this.index = (this.index + delta + n) % n;
        },
        go() {
            const item = this.results[this.index];
            if (item) window.location.href = item.href;
        },
    }));
}
