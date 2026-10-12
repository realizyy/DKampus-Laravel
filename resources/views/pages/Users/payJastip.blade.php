@extends('layouts.customer')

@php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $lines = collect($carts ?? [])->values();
    $order = '';
    $prevNote = null;
    foreach ($lines as $item) {
        if ($prevNote !== ($item['catatan'] ?? null)) {
            $order .= ($item['catatan'] ?? '').' : ';
            $prevNote = $item['catatan'] ?? null;
        }
        $order .= $item['jumlah'].' '.$item['nama'].', ';
    }
    $order = rtrim($order, ', ');
@endphp

@section('page-title', 'Pembayaran Jastip')
@section('back', url('/jastip'))
@section('hide-nav', '1')

@section('content')
<div class="mx-auto max-w-md space-y-4 pb-24 md:pb-0">
    <x-ui.page-header title="Pembayaran" class="mb-2 hidden md:flex" />

    <x-ui.card class="space-y-5" x-motion.rise>
        <x-customer.store-banner :name="$nama_umkm" />
        <p class="text-[17px] font-semibold leading-snug text-ink">{{ $order }}</p>
        <dl class="space-y-3 text-[15px]">
            <div class="flex items-center justify-between"><dt class="text-ink-600">Order ID</dt><dd class="font-mono uppercase text-ink">#TRX{{ substr($orderID, 0, 10) }}</dd></div>
            <div class="flex items-center justify-between"><dt class="text-ink-600">Metode Pembayaran</dt><dd><img src="{{ asset('qris.svg') }}" alt="QRIS" class="h-9 w-auto"></dd></div>
            @if ($umkmCount > 1)
                <div class="flex justify-between"><dt class="text-ink-600">Biaya pembelian {{ $umkmCount }} toko</dt><dd class="tnum font-mono">{{ $rp($subtotal) }}</dd></div>
            @endif
            <div class="flex justify-between"><dt class="text-ink-600">Ongkir</dt><dd class="tnum font-mono">{{ $rp($ongkir) }}</dd></div>
            <div class="flex justify-between border-t border-line pt-3 text-base font-semibold"><dt>Total</dt><dd class="tnum font-mono">{{ $rp($subtotal + $ongkir) }}</dd></div>
        </dl>
    </x-ui.card>

    <p class="px-2 text-center text-sm leading-snug text-ink-500">
        Dengan melanjutkan, artinya Anda setuju dengan <a href="{{ route('terms') }}" class="font-semibold text-brand-700 underline underline-offset-2">Syarat dan Ketentuan</a> dan juga <a href="{{ route('privacy') }}" class="font-semibold text-brand-700 underline underline-offset-2">Kebijakan Privasi</a> kami.
    </p>

    <form id="myForm" action="{{ route('order') }}" method="POST" enctype="multipart/form-data" class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur md:static md:z-auto md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
        @csrf
        <x-ui.button id="submitOrder" type="submit" size="lg" block>Pesan Sekarang</x-ui.button>
    </form>

    <p class="text-center"><a href="{{ url('/jastip') }}" class="text-[15px] text-ink-600 underline underline-offset-2 hover:text-ink">Batalkan transaksi?</a></p>
</div>
@endsection

@push('js')
<script>
    // Beri tahu kurir ada order baru; keepalive agar tetap terkirim walau halaman langsung berpindah setelah submit
    document.getElementById('submitOrder').addEventListener('click', function () {
        fetch(@json(route('send.notificationCour')), {
            method: 'POST', keepalive: true,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ _token: document.querySelector('meta[name="csrf-token"]').content }),
        }).catch(function (error) { console.error('Error sending notification:', error); });
    });
</script>
@endpush
