# Hook yang sengaja dibuang

Format: `<view>|<jenis>|<nilai>` per baris; dibaca oleh `node scripts/hooks-audit.mjs check`. Alasan di komentar tabel di bawah.


pages/Courier/chatpage.blade.php|dataAttrs|data-count-id
pages/Courier/dashboard.blade.php|hookIds|cancelModal
pages/Courier/dashboard.blade.php|hookIds|closeCancelModal
pages/Courier/dashboard.blade.php|hookIds|closeModal
pages/Courier/dashboard.blade.php|hookIds|modalConfirmation
pages/Courier/dashboard.blade.php|hookIds|uploadButton
pages/Courier/dashboard.blade.php|hookClasses|triggerCancelModal
pages/Courier/dashboard.blade.php|hookClasses|triggerModalButton
pages/Courier/orderspage.blade.php|hookIds|card
pages/Courier/orderspage.blade.php|hookIds|orderDetailModal
pages/Courier/profile.blade.php|hookIds|ubahNomorTeleponModal
pages/Courier/profile.blade.php|hookIds|ubahPasswordModal
pages/Courier/profile.blade.php|hookClasses|bg-gray-300
pages/Courier/profile.blade.php|routes|logout
pages/Courier/profile.blade.php|dataAttrs|data-modal-hide
pages/Courier/profile.blade.php|dataAttrs|data-modal-show
pages/Courier/riwayat.blade.php|texts|Have Any History
pages/Courier/riwayatdetail.blade.php|hookClasses|bg-gray-300

## Alasan

- `data-count-id`: -
- `cancelModal`: modal batal kini x-ui.modal name="cancel" (id modal-cancel), custId diisi lewat event modal:opening
- `closeCancelModal`: tutup modal lewat data-modal-close
- `closeModal`: tutup modal lewat data-modal-close
- `modalConfirmation`: modal selesai kini x-ui.modal name="complete" (id modal-complete)
- `uploadButton`: tombol file kini <label for=bukti> di x-ui.file-input
- `triggerCancelModal`: pemicu modal kini data-modal-open="cancel"
- `triggerModalButton`: pemicu modal kini data-modal-open="complete"
- `card`: wadah halaman orderspage tidak dipakai JS lagi
- `orderDetailModal`: modal detail kini x-ui.modal name="order-detail"
- `ubahNomorTeleponModal`: modal profil kini x-ui.modal name="phone"
- `ubahPasswordModal`: modal profil kini x-ui.modal name="password"
- `bg-gray-300`: kelas utilitas lama (bukan hook JS)
- `logout`: form logout dipindah ke x-ui.confirm di layouts/courier
- `data-modal-hide`: diganti data-modal-close
- `data-modal-show`: diganti data-modal-open
- `Have Any History`: copy empty-state diganti "Belum ada riwayat pesanan" (test diperbarui)
- `bg-gray-300`: kelas utilitas lama (bukan hook JS)
