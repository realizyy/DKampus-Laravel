@extends('layouts.courier')

@section('page-title', 'Status Order')
@section('back', route('cour.history'))

@section('content')
@php
    $orderId = '#TRX'.strtoupper(substr($id, 0, 10));
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
@endphp
<div class="grid items-start gap-4 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
    <x-ui.card class="space-y-5" x-motion.rise>
        <div class="flex items-start justify-between gap-3">
            <div>
                <x-ui.status :value="$status" :label="ucfirst($status)" />
                <p class="tnum mt-2 font-mono text-sm text-ink-500">{{ optional($created_at)->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Order ID</p>
                <p class="font-mono text-sm font-semibold uppercase text-ink">{{ $orderId }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 rounded-xl bg-sunken p-3">
            <span class="grid size-10 place-items-center rounded-lg bg-brand-100 text-brand-800"><x-ui.icon name="store" class="size-5" /></span>
            <p class="font-display text-lg font-bold tracking-tight text-ink">{{ $umkm }}</p>
        </div>

        <dl class="space-y-2.5 text-[15px]">
            <div class="flex items-center justify-between"><dt class="text-ink-600">Pembayaran</dt><dd><img src="{{ asset('qris.svg') }}" alt="QRIS" class="h-8 w-auto"></dd></div>
            <div class="flex justify-between"><dt class="text-ink-600">Subtotal</dt><dd class="tnum font-mono text-ink">{{ $rp($total) }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-600">Ongkir</dt><dd class="tnum font-mono text-ink">{{ $rp($ongkir) }}</dd></div>
            <div class="flex justify-between border-t border-line pt-2.5 text-base font-semibold"><dt class="text-ink">Total</dt><dd class="tnum font-mono text-ink">Rp {{ number_format($total + $ongkir, 0, ',', '.') }}</dd></div>
        </dl>
    </x-ui.card>

    <div class="space-y-4">
        <x-ui.card class="flex items-center justify-between gap-3" x-motion.rise="{ delay: 0.06 }">
            <div class="flex min-w-0 items-center gap-3">
                <x-ui.avatar :name="$cust_name" size="lg" />
                <div class="min-w-0">
                    <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Pelanggan</p>
                    <p class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $cust_name }}</p>
                </div>
            </div>
            <form action="{{ route('room.chat.courier') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $custId }}" name="custId">
                <x-ui.button type="submit" variant="secondary" class="!px-0 size-11" aria-label="Chat pelanggan"><x-ui.icon name="message-circle" class="size-5" /></x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.button :href="route('cour.history')" icon="arrow-left" variant="secondary" block>Kembali ke Status Order</x-ui.button>
    </div>
</div>
@endsection
