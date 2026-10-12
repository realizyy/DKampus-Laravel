@extends('layouts.page')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink md:text-5xl">Tentang Kami</h1>
<p class="mt-3 max-w-prose text-lg leading-relaxed text-ink-600">Dkampus menghubungkan warung dan UMKM di sekitar Telkom University dengan mahasiswa yang lapar, lewat pemesanan yang cepat dan kurir yang dekat.</p>

<div class="mt-8 grid gap-4 md:grid-cols-5">
    <x-ui.card class="space-y-2 md:col-span-3">
        <h2 class="font-display text-xl font-bold tracking-tight">Untuk pembeli</h2>
        <p class="leading-relaxed text-ink-600">Jelajahi kelezatan kuliner di sekitar kampus, tambahkan catatan untuk restoran, bayar dengan QRIS, dan pantau pesananmu sampai tiba di kosan.</p>
    </x-ui.card>
    <x-ui.card class="space-y-2 md:col-span-2">
        <h2 class="font-display text-xl font-bold tracking-tight">Untuk kurir</h2>
        <p class="leading-relaxed text-ink-600">Dsquad Freelancer: ambil orderan, antar, dan unggah bukti pembayaran langsung dari aplikasi.</p>
    </x-ui.card>
    <x-ui.card class="space-y-2 md:col-span-5">
        <h2 class="font-display text-xl font-bold tracking-tight">Untuk warung dan UMKM</h2>
        <p class="max-w-prose leading-relaxed text-ink-600">Etalase menu, promo, dan pesanan masuk dikelola dalam satu tempat sehingga pemilik warung bisa fokus memasak.</p>
    </x-ui.card>
</div>

<p class="mt-8 text-[15px] text-ink-500">Ada pertanyaan atau masukan? Hubungi tim melalui tombol bantuan di beranda.</p>
@endsection
