@extends('layouts.customer')

@php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $lines = collect($carts ?? [])->values();
@endphp

@section('page-title', 'Pemesanan')
@section('back', url('/pesanan'))
@section('hide-nav', '1')

@section('content')
<div class="mx-auto grid max-w-5xl items-start gap-5 pb-24 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] lg:pb-0">
    <div class="space-y-4">
        <x-ui.page-header title="Pemesanan" class="mb-2 hidden md:flex" />
        <x-ui.card :padded="false" class="overflow-hidden" x-motion.rise>
            <div class="border-b border-line/70 p-4"><x-customer.store-banner :name="$nama_umkm" /></div>
            <ul class="divide-y divide-line/70">
                @foreach ($lines as $item)
                    <li class="flex items-center gap-3 p-4">
                        <span class="tnum grid size-8 shrink-0 place-items-center rounded-lg bg-brand-100 font-mono text-sm font-semibold text-brand-800">{{ $item['jumlah'] }}×</span>
                        <p class="min-w-0 flex-1 truncate text-[15px] text-ink">{{ $item['nama'] }}@if (($item['catatan'] ?? '-') !== '-')<span class="text-ink-500"> — {{ $item['catatan'] }}</span>@endif</p>
                        <p class="tnum font-mono text-sm font-semibold text-ink">{{ $rp($item['harga'] * $item['jumlah']) }}</p>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>

        <x-ui.card class="space-y-3">
            <div class="flex items-center justify-between"><h2 class="font-display text-lg font-bold tracking-tight">Notes Pesanan</h2><span class="text-sm text-brand-700">opsional</span></div>
            <x-ui.textarea form="formKonfirmasi" name="notesPesanan" id="notesPesanan" placeholder="Tambahkan notes pesanan"></x-ui.textarea>
        </x-ui.card>
    </div>

    <div class="space-y-4 lg:sticky lg:top-24">
        <x-ui.card class="space-y-3" x-motion.rise="{ delay: 0.05 }">
            <h2 class="font-display text-lg font-bold tracking-tight">Rangkuman Pembayaran</h2>
            <dl class="space-y-2 text-[15px]">
                <div class="flex justify-between"><dt class="text-ink-600">Total Harga</dt><dd class="tnum font-mono">{{ $rp($total) }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-600">Ongkir</dt><dd class="tnum font-mono">{{ $rp($ongkir) }}</dd></div>
                <div class="flex justify-between border-t border-line pt-2.5 text-base font-semibold"><dt>Total Pembayaran</dt><dd class="tnum font-mono">{{ $rp($total + $ongkir) }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card class="flex items-center gap-3">
            <img src="{{ asset('qris.svg') }}" alt="QRIS" class="h-10 w-auto">
            <div>
                <p class="font-semibold text-ink">Pembayaran</p>
                <p class="text-sm text-ink-500">*Dkampus hanya menerima pembayaran dengan QRIS</p>
            </div>
        </x-ui.card>

        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur lg:static lg:z-auto lg:border-0 lg:bg-transparent lg:p-0 lg:backdrop-blur-none">
            <x-ui.button id="pesanButton" size="lg" block data-modal-open="konfirmasi">Pembayaran</x-ui.button>
        </div>
    </div>
</div>

{{-- Form dan modal terpisah: <dialog> berisi <form method=dialog> tidak boleh bersarang di form lain --}}
<form id="formKonfirmasi" action="{{ route('confirm.pay') }}" method="POST">@csrf</form>
<x-ui.modal name="konfirmasi" tone="success" title="Apakah anda yakin?" description="Pastikan semua informasi sudah benar sebelum melanjutkan.">
    <x-slot:actions>
        <x-ui.button type="submit" form="formKonfirmasi" id="konfirmasiButton" block>Konfirmasi</x-ui.button>
        <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
@endsection
