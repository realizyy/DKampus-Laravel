{{--
    Kartu pesanan di tab "Proses" (riwayat dari database). Kartu pesanan aktif dari Firebase dirender JS dengan markup yang sama (lihat Status.blade).
    $history: App\Models\history; $umkm: nama warung.
--}}
@props(['history', 'umkm'])
@php
    $total = (int) $history->harga + (int) $history->ongkir;
    $orderId = '#TRX'.strtoupper(substr($history->order_id, 0, 10));
    $done = strtolower((string) $history->status) === 'completed';
@endphp
<article {{ $attributes->class('flex flex-col rounded-2xl bg-surface shadow-card') }} data-id="{{ substr($history->order_id, 0, 10) }}">
    <header class="flex items-start gap-3 p-4">
        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800"><x-ui.icon name="store" class="size-5" /></span>
        <div class="min-w-0 flex-1">
            <h2 class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $umkm }}</h2>
            <p class="font-mono text-xs text-ink-500">{{ optional($history->created_at)->format('d F Y H:i') }}</p>
        </div>
        <x-ui.status :value="$history->status" :label="ucfirst($history->status)" />
    </header>
    <div class="flex-1 space-y-1 px-4 pb-4 text-[15px]">
        <p class="text-ink-700"><span class="font-semibold text-ink">Pesanan:</span> {{ $history->item }}</p>
        <p class="font-mono text-sm text-ink-500">Order ID <span class="text-ink-700">{{ $orderId }}</span></p>
    </div>
    <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line/70 p-3 pl-4">
        <div>
            <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Total</p>
            <p class="tnum font-display text-xl font-extrabold tracking-tight text-brand-700">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('historydetail.cust') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $history->cour_id }}" name="courId">
                <input type="hidden" value="{{ $history->id }}" name="id">
                <x-ui.button type="submit" icon-right="chevron-right">Detail</x-ui.button>
            </form>
            @if ($done)
                <form action="{{ route('rating') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $history->id }}" name="id">
                    <x-ui.button type="submit" variant="soft" icon="star">Rating</x-ui.button>
                </form>
            @endif
            <form action="{{ route('room.chat') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $history->cour_id }}" name="courId">
                <x-ui.button type="submit" variant="secondary" class="!px-0 size-11" aria-label="Chat kurir"><x-ui.icon name="message-circle" class="size-5" /></x-ui.button>
            </form>
        </div>
    </footer>
</article>
