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
components/banner/carousel.blade.php|view|*
components/bannerfade/banner-fade.blade.php|view|*
components/carousel/slider-category-desktop.blade.php|view|*
components/carousel/slider-category.blade.php|view|*
components/floatingcshelp.blade.php|view|*
components/header/headerForDesktop.blade.php|view|*
components/header/menu.blade.php|view|*
components/header/menuDesktop.blade.php|view|*
components/header/search.blade.php|view|*
components/header/searchDesktop.blade.php|view|*
components/header/topbar.blade.php|view|*
components/list/slider-makanan.blade.php|view|*
components/list/slider.blade.php|view|*
components/list-food/slider.blade.php|view|*
components/navbar/navbar.blade.php|view|*
components/navbar/navbarDesktop.blade.php|view|*
components/navbar/subnavbar-homepage.blade.php|view|*
components/promo-carousel/carousel.blade.php|view|*
components/promo-slider/carousel.blade.php|view|*
components/promo-slider/semua-promo-carousel.blade.php|view|*
components/scrollBehaviourr/scroll-behaviour.blade.php|view|*
components/wave-animations/wave.blade.php|view|*
layouts/FavoritLayout.blade.php|view|*
layouts/PesananLayout.blade.php|view|*
layouts/PromoLayout.blade.php|view|*
layouts/Root.blade.php|view|*
pages/Users/ChatPage.blade.php|dataAttrs|data-count-id
pages/Users/CheckoutPage.blade.php|hookIds|modalConfrimation
pages/Users/CheckoutPage.blade.php|hookClasses|bg-gray-300
pages/Users/DaftarAlamat.blade.php|hookIds|modal-title
pages/Users/DetailMakanan.blade.php|hookIds|NavCheckbox
pages/Users/DetailMakanan.blade.php|hookIds|NavLikeButton
pages/Users/DetailMakanan.blade.php|hookIds|navbar-detail-makanan
pages/Users/DetailWarung.blade.php|hookIds|CardCheckbox
pages/Users/DetailWarung.blade.php|hookIds|CardLikeButton
pages/Users/DetailWarung.blade.php|hookIds|NavCheckbox
pages/Users/DetailWarung.blade.php|hookIds|NavLikeButton
pages/Users/DetailWarung.blade.php|hookIds|navbar-detail-warung
pages/Users/Homepage.blade.php|hookIds|developmentModal
pages/Users/Homepage.blade.php|hookIds|modal-title
pages/Users/JastipPage.blade.php|hookIds|addNewAddress
pages/Users/JastipPage.blade.php|hookIds|jenisJastip
pages/Users/JastipPage.blade.php|hookIds|overlayAddNewAddress
pages/Users/JastipPage.blade.php|hookClasses|switch-button
pages/Users/JastipPage.blade.php|hookClasses|switch-thumb
pages/Users/JastipPage.blade.php|hookClasses|switch-track
pages/Users/JastipPage.blade.php|names|kuantitas[0][]
pages/Users/JastipPage.blade.php|names|nama_menu[0][]
pages/Users/JastipPage.blade.php|dataAttrs|data-index
pages/Users/Pay.blade.php|routes|send.notificationCour
pages/Users/Pesanan.blade.php|hookIds|addNewAddress
pages/Users/Pesanan.blade.php|hookIds|alamat
pages/Users/Pesanan.blade.php|hookIds|decrement
pages/Users/Pesanan.blade.php|hookIds|delete
pages/Users/Pesanan.blade.php|hookIds|increment
pages/Users/Pesanan.blade.php|hookIds|number
pages/Users/Pesanan.blade.php|hookIds|overlayAddNewAddress
pages/Users/Pesanan.blade.php|hookClasses|quantity-input
pages/Users/Pesanan.blade.php|names|catatan
pages/Users/Pesanan.blade.php|names|items[harga][]
pages/Users/Pesanan.blade.php|names|items[id][]
pages/Users/Pesanan.blade.php|names|items[quantity][]
pages/Users/PromoCategoryPage.blade.php|view|*
pages/Users/PromoSemuaPage.blade.php|view|*
pages/Users/PromoSpecialPage.blade.php|view|*
pages/Users/Rating.blade.php|hookClasses|bg-gray-300
pages/Users/SearchPage.blade.php|hookIds|dropdown-menu
pages/Users/SearchPage.blade.php|hookIds|options-menu
pages/Users/SearchPage.blade.php|hookIds|search-input
pages/Users/SearchPage.blade.php|names|keyword
pages/Users/SearchPage.blade.php|texts|action="
pages/Users/SemuaPage.blade.php|view|*
pages/Users/Status.blade.php|names|courId
pages/Users/Status.blade.php|names|id
pages/Users/Status.blade.php|routes|historydetail.cust
pages/Users/Status.blade.php|routes|rating
pages/Users/StatusOrder.blade.php|hookClasses|bg-gray-300
pages/Users/UbahKataSandi.blade.php|names|confirmPassword
pages/Users/UbahKataSandi.blade.php|names|newPassword

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

### Fase 3 (Customer)

- `<view>|view|*` untuk layouts/Root, PromoLayout, PesananLayout, FavoritLayout, komponen header/navbar/carousel/list/promo-*, floatingcshelp, dan view promo lama: digantikan `layouts/customer`, `components/customer/*`, dan satu view `Promo`.
- `hookIds`/`hookClasses` di halaman customer: drawer/overlay/modal digerakkan style inline dan JS (`public/js/*`) diganti Alpine + `x-ui.modal`/`data-modal-open`; id duplikat (`#increment`, `#decrement`, `#number`, `#delete`) diganti `.increment-btn/.decrement-btn` di Pesanan.
- `names`: `kuantitas[0][]` dan `nama_menu[0][]` kini dibuat dinamis oleh Alpine (`:name`), field tetap `warung[]`, `nama_menu[i][]`, `kuantitas[i][]`; Pesanan membuang field tak terpakai (`catatan`, `items[*][]`); Ubah Kata Sandi memakai `current_password/password/password_confirmation` (PUT /password).
- `routes`: `send.notificationCour` di Pay dipertahankan lewat `@push('js')`; `historydetail.cust` dan `rating` kini berada di `components/customer/order-card`.

### Fase 4 (Admin)

- `pages/Admin/UMKM_update.blade.php|hookIds|image-preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/UMKM_update.blade.php|hookIds|image_logo_umkm`: pratinjau logo kini x-ui.image-input (Alpine)
- `pages/Admin/UMKM_update.blade.php|hookIds|preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/UMKM_update.blade.php|hookClasses|delete-button`: konfirmasi hapus SweetAlert diganti x-ui.modal (delete-product/delete-umkm)
- `pages/Admin/UMKM_update.blade.php|hookClasses|delete-form`: konfirmasi hapus SweetAlert diganti x-ui.modal (delete-product/delete-umkm)
- `pages/Admin/UMKM_update.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/UMKM_update.blade.php|routes|product`: daftar produk resto memakai panel edit/hapus (modal) dan tombol Tambah, bukan tautan halaman
- `pages/Admin/UMKM_update.blade.php|routes|product.edit`: daftar produk resto memakai panel edit/hapus (modal) dan tombol Tambah, bukan tautan halaman
- `pages/Admin/account.blade.php|hookIds|modal-edit`: panel edit akun kini x-ui.sheet name="user" (#modal-user)
- `pages/Admin/account.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/chatpage.blade.php|hookIds|addNewChat`: tombol uji coba yang mengirim 'Hello, can i help you?!' ke customer id 24 dibuang
- `pages/Admin/chatpage.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/chatpage.blade.php|dataAttrs|data-count-id`: lencana jumlah pesan belum dibaca tidak pernah dirender; dibuang
- `pages/Admin/chatpage.blade.php|dataAttrs|data-del-id`: atribut ini kini dibuat JS (dataset.delId) pada item chat
- `pages/Admin/dashboard.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/product.blade.php|hookIds|edit-image-preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/product.blade.php|hookIds|edit-preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/product.blade.php|hookIds|edit_category`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_deskripsi`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_harga`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_image`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_nama_makanan`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_promo`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|edit_umkm`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|hookIds|image-preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/product.blade.php|hookIds|modalEditProductumkm`: panel edit produk kini x-ui.sheet name="edit-product" (#modal-edit-product)
- `pages/Admin/product.blade.php|hookIds|preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/product.blade.php|hookIds|umkm`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|names|category`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|names|deskripsi`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|names|edit_category`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_deskripsi`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_harga`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_image`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_nama_makanan`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_promo`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|edit_umkm`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|names|harga`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/product.blade.php|names|image`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Courier/chatroom.blade.php, pages/Users/ChatRoomPage.blade.php
- `pages/Admin/product.blade.php|names|nama_makanan`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|names|promo`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|routes|save.product`: pindah ke pages/Admin/partials/product-modals.blade.php
- `pages/Admin/product.blade.php|texts|name="edit_nama_makanan"`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product.blade.php|texts|name="edit_promo"`: pindah ke pages/Admin/partials/product-modals.blade.php, pages/Admin/product_update.blade.php
- `pages/Admin/product_update.blade.php|hookIds|edit-preview`: pratinjau gambar kini x-ui.image-input (Alpine)
- `pages/Admin/product_update.blade.php|hookClasses|bg-gray-300`: kelas utilitas lama (bukan hook JS)
- `pages/Admin/product_update.blade.php|hookClasses|nama_umkm`: kelas penanda select2 lama; select2 tidak dipakai lagi
- `pages/Admin/product_update.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/transaction.blade.php|hookIds|date`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|jarak`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|modal-edit`: panel detail kini x-ui.sheet name="detail" (#modal-detail)
- `pages/Admin/transaction.blade.php|hookIds|ongkir`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|orderBy`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|orderID`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|payment`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|payment-proof-modal`: modal bukti pembayaran kini x-ui.modal name="proof" (#modal-proof)
- `pages/Admin/transaction.blade.php|hookIds|status`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|takenBy`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|total`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|hookIds|umkm`: id tetap sama; dibuat lewat @foreach di panel x-ui.sheet name="detail"
- `pages/Admin/transaction.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/umkm.blade.php|hookIds|alamat`: pindah ke pages/Users/JastipPage.blade.php
- `pages/Admin/umkm.blade.php|hookIds|autocomplete`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/DaftarAlamat.blade.php
- `pages/Admin/umkm.blade.php|hookIds|editForm`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|hookIds|edit_alamat`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_close_time`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_geo`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_link`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_nama_umkm`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_no_telp_umkm`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|edit_open_time`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|hookIds|geo`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/JastipPage.blade.php
- `pages/Admin/umkm.blade.php|hookIds|image-preview`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|hookIds|link`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/JastipPage.blade.php
- `pages/Admin/umkm.blade.php|hookIds|logo_umkm`: pindah ke pages/Admin/UMKM_update.blade.php, pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|hookIds|modal-edit`: modal edit di daftar dihapus; edit dilakukan di halaman Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|hookIds|modalumkm`: form tambah resto kini halaman wizard /admin/umkm/create (umkm_create)
- `pages/Admin/umkm.blade.php|hookIds|preview`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|hookIds|umkmForm`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|alamat`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/JastipPage.blade.php, pages/Users/Pesanan.blade.php
- `pages/Admin/umkm.blade.php|names|close_time`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|edit_alamat`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_close_time`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_geo`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_link`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_nama_umkm`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_no_telp_umkm`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_open_time`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|edit_vip`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|names|geo`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/DaftarAlamat.blade.php, pages/Users/JastipPage.blade.php
- `pages/Admin/umkm.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `pages/Admin/umkm.blade.php|names|link`: pindah ke pages/Admin/umkm_create.blade.php, pages/Users/DaftarAlamat.blade.php, pages/Users/JastipPage.blade.php
- `pages/Admin/umkm.blade.php|names|logo_umkm`: pindah ke pages/Admin/UMKM_update.blade.php, pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|nama_umkm`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|no_telp_umkm`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|open_time`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|user_id`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|names|vip`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|routes|umkm.save`: pindah ke pages/Admin/umkm_create.blade.php
- `pages/Admin/umkm.blade.php|routes|umkmUpdate`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|dataAttrs|data-alamat`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|dataAttrs|data-bs-target`: Bootstrap JS dicabut; modal memakai data-modal-open
- `pages/Admin/umkm.blade.php|dataAttrs|data-bs-toggle`: Bootstrap JS dicabut; modal memakai data-modal-open
- `pages/Admin/umkm.blade.php|dataAttrs|data-geo`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|dataAttrs|data-id`: pindah ke components/customer/order-card.blade.php, pages/Admin/UMKM_update.blade.php, pages/Admin/account.blade.php
- `pages/Admin/umkm.blade.php|dataAttrs|data-link`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|dataAttrs|data-vip`: modal tambah/edit di daftar dihapus: tambah di halaman umkm_create (wizard), edit di Detail Resto (UMKM_update)
- `pages/Admin/umkm.blade.php|texts|name="edit_nama_umkm"`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|texts|name="edit_open_time"`: pindah ke pages/Admin/UMKM_update.blade.php
- `pages/Admin/umkm.blade.php|texts|name="edit_vip"`: pindah ke pages/Admin/UMKM_update.blade.php
- `profile/edit.blade.php|names|header`: slot header (x-app-layout) diganti @section('page-title') di layouts/admin
- `components/application-logo.blade.php|view|*`: komponen Breeze tak terpakai (logo memakai asset logoDkampus.svg)
- `components/danger-button.blade.php|view|*`: digantikan x-ui.button variant=danger
- `components/secondary-button.blade.php|view|*`: digantikan x-ui.button variant=outline/secondary
- `components/dropdown.blade.php|view|*`: menu pengguna admin kini ada di sidebar layouts/admin
- `components/dropdown-link.blade.php|view|*`: menu pengguna admin kini ada di sidebar layouts/admin
- `components/modal.blade.php|view|*`: digantikan x-ui.modal (dialog native) dan x-ui.sheet
- `components/nav-link.blade.php|view|*`: navigasi admin kini di layouts/admin
- `components/responsive-nav-link.blade.php|view|*`: navigasi admin kini di layouts/admin
- `layouts/app.blade.php|view|*`: digantikan layouts/admin (komponen x-app-layout dihapus)
- `layouts/navigation.blade.php|view|*`: digantikan sidebar + bottom-nav di layouts/admin
