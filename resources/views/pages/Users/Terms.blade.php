@extends('layouts.page')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink md:text-5xl">Syarat dan Ketentuan</h1>
<div class="mt-4 flex items-start gap-2 rounded-xl bg-amber-100 p-3 text-[15px] text-amber-700" role="note">
    <x-ui.icon name="triangle-alert" class="mt-0.5 size-5 shrink-0" />
    <p>Draf awal untuk ditinjau pengelola Dkampus sebelum dipublikasikan.</p>
</div>

<x-ui.card class="prose-sm mt-6 space-y-6 p-6 leading-relaxed text-ink-700 md:p-8">
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">1. Penggunaan layanan</h2>
        <p>Dkampus adalah platform pemesanan makanan dari warung dan UMKM di sekitar kampus. Dengan membuat akun, kamu menyatakan data yang diberikan benar dan akunmu hanya dipakai oleh dirimu sendiri.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">2. Pemesanan dan pembayaran</h2>
        <p>Harga dan ketersediaan menu ditentukan masing-masing warung. Pembayaran dilakukan melalui QRIS atau metode lain yang tersedia di aplikasi. Biaya antar (ongkir) ditampilkan sebelum pesanan dikonfirmasi.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">3. Pembatalan</h2>
        <p>Pesanan yang sedang diproses atau diantar dapat dibatalkan oleh kurir dengan alasan yang jelas. Pengembalian dana mengikuti kebijakan warung dan bukti yang diunggah kurir.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">4. Kewajiban pengguna</h2>
        <p>Pengguna dilarang menyalahgunakan layanan, membagikan kode verifikasi kepada pihak lain, atau menggunakan Dkampus untuk kegiatan yang melanggar hukum.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">5. Perubahan ketentuan</h2>
        <p>Ketentuan ini dapat diperbarui sewaktu-waktu. Perubahan penting akan diinformasikan melalui aplikasi.</p>
    </section>
</x-ui.card>
@endsection
