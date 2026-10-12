// Modal <dialog>: buka lewat [data-modal-open="nama"] atau dkModal.open('nama').
// Event "modal:opening" (detail = dataset tombol pemicu) memungkinkan halaman mengisi modal dari tombol yang diklik.
export function registerModal() {
    const find = (name) => document.getElementById(`modal-${name}`);

    window.dkModal = {
        open(name, detail = {}) {
            const dialog = find(name);
            if (!dialog) return;
            dialog.dispatchEvent(new CustomEvent('modal:opening', { detail }));
            if (!dialog.open) dialog.showModal();
        },
        close(name) {
            const dialog = find(name);
            if (dialog?.open) dialog.close();
        },
    };

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-modal-open]');
        if (opener) {
            event.preventDefault();
            window.dkModal.open(opener.dataset.modalOpen, { ...opener.dataset });
            return;
        }
        const closer = event.target.closest('[data-modal-close]');
        if (closer) closer.closest('dialog')?.close();
    });
}
