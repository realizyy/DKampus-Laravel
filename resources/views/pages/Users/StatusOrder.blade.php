@extends('layouts.customer')

@php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $lines = collect($orders ?? [])->values();
    $kode = '#TRX'.strtoupper(substr((string) $orderId, 0, 10));
@endphp

@section('page-title', 'Status Pesanan')
@section('back', url('pesanan/status'))
@section('hide-nav', '1')

@section('content')
<div class="mx-auto grid max-w-4xl items-start gap-4 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
    <x-ui.card class="space-y-5" x-motion.rise>
        <div class="flex items-start justify-between gap-3">
            <div>
                <x-ui.status :value="$status" :label="ucfirst($status)" />
                @if (! empty($date))<p class="tnum mt-2 font-mono text-sm text-ink-500">{{ $date }}</p>@endif
            </div>
            <div class="text-right">
                <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Order ID</p>
                <p class="font-mono text-sm font-semibold uppercase text-ink">{{ $kode }}</p>
            </div>
        </div>

        <x-customer.store-banner :name="$nama_umkm" class="rounded-xl bg-sunken p-3" />

        @if ($lines->isNotEmpty())
            <ul class="space-y-1.5 text-[15px]">
                @foreach ($lines as $line)
                    <li class="flex items-baseline gap-2"><span class="tnum font-mono text-sm font-semibold text-ink-700">{{ $line['jumlah'] ?? 1 }}×</span><span class="text-ink">{{ $line['nama'] ?? '' }}@if (($line['catatan'] ?? '-') !== '-')<span class="text-ink-500"> — {{ $line['catatan'] }}</span>@endif</span></li>
                @endforeach
            </ul>
        @endif

        <dl class="space-y-2.5 text-[15px]">
            <div class="flex items-center justify-between"><dt class="text-ink-600">Pembayaran</dt><dd><img src="{{ asset('qris.svg') }}" alt="QRIS" class="h-8 w-auto"></dd></div>
            <div class="flex justify-between"><dt class="text-ink-600">Subtotal</dt><dd class="tnum font-mono">{{ $rp($subTotal) }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-600">Ongkir</dt><dd class="tnum font-mono">{{ $rp($ongkir) }}</dd></div>
            <div class="flex justify-between border-t border-line pt-2.5 text-base font-semibold"><dt>Total</dt><dd class="tnum font-mono">{{ $rp($subTotal + $ongkir) }}</dd></div>
        </dl>
    </x-ui.card>

    <div class="space-y-4">
        @if ($nama_driver != null)
            <x-ui.card class="flex items-center justify-between gap-3" x-motion.rise="{ delay: 0.06 }">
                <div class="flex min-w-0 items-center gap-3">
                    <x-ui.avatar :name="$nama_driver" size="lg" />
                    <div class="min-w-0">
                        <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Kurir</p>
                        <p class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $nama_driver }}</p>
                    </div>
                </div>
                <form action="{{ route('room.chat') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $courId ?? '' }}" name="courId">
                    <x-ui.button type="submit" variant="secondary" class="!px-0 size-11" aria-label="Chat kurir"><x-ui.icon name="message-circle" class="size-5" /></x-ui.button>
                </form>
            </x-ui.card>
        @else
            <x-ui.card class="flex items-center gap-3 text-ink-600"><x-ui.icon name="bike" class="size-6 shrink-0 text-brand-600" />Menunggu kurir mengambil pesananmu…</x-ui.card>
        @endif
        <x-ui.button :href="url('pesanan/status')" icon="arrow-left" variant="secondary" block>Kembali ke Status Order</x-ui.button>
    </div>
</div>
@endsection
