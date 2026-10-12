@extends('layouts.page')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink md:text-5xl">Kebijakan Privasi</h1>
<div class="mt-4 flex items-start gap-2 rounded-xl bg-amber-100 p-3 text-[15px] text-amber-700" role="note">
    <x-ui.icon name="triangle-alert" class="mt-0.5 size-5 shrink-0" />
    <p>Draf awal untuk ditinjau pengelola Dkampus sebelum dipublikasikan.</p>
</div>

<x-ui.card class="mt-6 space-y-6 p-6 leading-relaxed text-ink-700 md:p-8">
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">Data yang kami kumpulkan</h2>
        <p>Nama, email, nomor telepon, alamat pengantaran, serta riwayat pesanan dan percakapan dengan kurir yang diperlukan untuk menjalankan layanan.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">Cara data dipakai</h2>
        <p>Data dipakai untuk memproses pesanan, menghubungkanmu dengan kurir dan warung, mengirim kode verifikasi, serta menjaga keamanan akun. Kami tidak menjual data pribadimu.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">Berbagi data</h2>
        <p>Nomor telepon dan alamat pengantaran dibagikan kepada kurir yang mengambil pesananmu, dan nama pemesan kepada warung terkait, hanya selama pesanan berlangsung.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">Keamanan</h2>
        <p>Kata sandi disimpan dalam bentuk ter-hash dan kode verifikasi hanya berlaku sebentar. Jangan bagikan kode verifikasi kepada siapa pun.</p>
    </section>
    <section class="space-y-2">
        <h2 class="font-display text-xl font-bold tracking-tight text-ink">Hak kamu</h2>
        <p>Kamu dapat meminta perubahan atau penghapusan data akunmu dengan menghubungi tim Dkampus.</p>
    </section>
</x-ui.card>
@endsection
