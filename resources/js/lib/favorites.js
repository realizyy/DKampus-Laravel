// Tombol hati [data-fav]: toggle favorit (POST /favoritStore/{id}); tamu diarahkan ke halaman masuk.
export function registerFavorites() {
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-fav]');
        if (!button) return;
        event.preventDefault();
        event.stopPropagation();

        if (button.dataset.login) { window.location.href = button.dataset.login; return; }
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const on = button.getAttribute('aria-pressed') === 'true';
        button.setAttribute('aria-pressed', String(!on)); // optimistis
        try {
            const response = await fetch(`/favoritStore/${button.dataset.fav}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (response.status === 401 || response.redirected) {
                window.location.href = '/login';
                return;
            }
            if (!response.ok) throw new Error(String(response.status));
            window.DKToast?.info(on ? 'Dihapus dari favorit' : 'Ditambahkan ke favorit');
        } catch (error) {
            button.setAttribute('aria-pressed', String(on));
            window.DKToast?.error('Favorit belum bisa diubah. Coba lagi.');
        }
    });
}
