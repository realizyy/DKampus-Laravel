let seq = 0;

export function registerToast(Alpine) {
    Alpine.store('toast', {
        items: [],
        push({ type = 'info', title = '', message = '', timeout = 4500 } = {}) {
            const id = ++seq;
            this.items.push({ id, type, title, message });
            if (timeout > 0) setTimeout(() => this.remove(id), timeout);
            return id;
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });

    // API sederhana untuk skrip inline lama: DKToast.success('Tersimpan')
    window.DKToast = {
        success: (message, title = 'Berhasil') => Alpine.store('toast').push({ type: 'success', title, message }),
        error: (message, title = 'Gagal') => Alpine.store('toast').push({ type: 'error', title, message, timeout: 7000 }),
        info: (message, title = '') => Alpine.store('toast').push({ type: 'info', title, message }),
    };

    // Jembatan flash session: <x-ui.flash /> mencetak JSON di data-flash
    document.addEventListener('DOMContentLoaded', () => {
        const host = document.querySelector('[data-flash]');
        if (!host) return;
        try {
            const flash = JSON.parse(host.dataset.flash || '{}');
            if (flash.success) window.DKToast.success(flash.success);
            if (flash.status) window.DKToast.info(flash.status);
            if (flash.error) window.DKToast.error(flash.error);
        } catch (e) {
            /* flash rusak: abaikan */
        }
    });
}
