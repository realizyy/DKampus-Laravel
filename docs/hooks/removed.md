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
components/header/courierHeader.blade.php|view|*
components/modals/modal.blade.php|view|*
components/navbar/navbarCourier.blade.php|view|*
layouts/Login&Register.blade.php|view|*
layouts/guest.blade.php|names|csrf-token
layouts/guest.blade.php|names|viewport
pages/Users/AturUlangKataSandi.blade.php|view|*
pages/Users/CodeVerification.blade.php|view|*
pages/Users/InputRegister.blade.php|view|*
pages/Users/Login.blade.php|hookIds|inputs
pages/Users/Login.blade.php|hookIds|modal_confirm_email
pages/Users/Register.blade.php|hookIds|daftar
pages/Users/Register.blade.php|hookIds|errorMsg
pages/Users/Register.blade.php|hookIds|inputs
pages/Users/Register.blade.php|hookIds|modal_error_msg
pages/Users/Register.blade.php|hookIds|overlayErrorMsg
pages/Users/Register.blade.php|hookIds|submit_btn

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
- `components/header/courierHeader.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `components/modals/modal.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `components/navbar/navbarCourier.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `layouts/Login&Register.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `layouts/guest.blade.php|names|csrf-token`: meta ada di layouts.partials.head-assets
- `layouts/guest.blade.php|names|viewport`: meta ada di layouts/base
- `pages/Users/AturUlangKataSandi.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `pages/Users/CodeVerification.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `pages/Users/InputRegister.blade.php|view|*`: view digantikan layout/komponen baru (courierHeader, navbarCourier -> layouts/courier; Login&Register -> layouts/auth; modals.modal -> x-ui.modal; prototipe statis CodeVerification/InputRegister/AturUlangKataSandi -> alur OTP nyata)
- `pages/Users/Login.blade.php|hookIds|inputs`: wadah form lama; submit tidak lagi dicegat JS
- `pages/Users/Login.blade.php|hookIds|modal_confirm_email`: modal galat login diganti pesan inline x-ui.field
- `pages/Users/Register.blade.php|hookIds|daftar`: tombol Daftar kini x-ui.button membuka modal confirm-email
- `pages/Users/Register.blade.php|hookIds|errorMsg`: galat registrasi per field
- `pages/Users/Register.blade.php|hookIds|inputs`: wadah form lama; submit tidak lagi dicegat JS
- `pages/Users/Register.blade.php|hookIds|modal_error_msg`: galat registrasi per field
- `pages/Users/Register.blade.php|hookIds|overlayErrorMsg`: galat registrasi per field
- `pages/Users/Register.blade.php|hookIds|submit_btn`: tombol "Ya, kirim kode" di modal confirm-email
