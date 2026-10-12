@extends('layouts.courier')

@section('page-title', 'History')

@section('content')
@if ($data->isEmpty())
    <x-ui.card class="!p-0 md:mx-auto md:max-w-xl">
        <x-ui.empty-state icon="clipboard-list" title="Belum ada riwayat pesanan" description="Pesanan yang sudah kamu antar atau batalkan akan tercatat di sini." />
    </x-ui.card>
@else
    <ul class="grid gap-3 lg:grid-cols-2" x-motion.rise.stagger>
        @foreach ($data->sortByDesc('created_at') as $history)
            @php
                $nama = $history->umkm_id == 0 ? 'Jastip' : ($nama_umkm[$history->umkm_id] ?? 'UMKM tidak tersedia');
                $orderId = '#TRX'.strtoupper(substr($history->order_id, 0, 10));
            @endphp
            <li>
                <article class="flex h-full flex-col rounded-2xl bg-surface shadow-card">
                    <header class="flex items-start gap-3 p-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800"><x-ui.icon name="store" class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $nama }}</h2>
                            <p class="font-mono text-xs text-ink-500">{{ optional($history->created_at)->format('d F Y H:i') }}</p>
                        </div>
                        <x-ui.status :value="$history->status" :label="ucfirst($history->status)" />
                    </header>
                    <div class="flex-1 space-y-1 px-4 pb-4 text-[15px]">
                        <p class="text-ink-700"><span class="font-semibold text-ink">Pesanan:</span> {{ $history->item }}</p>
                        <p class="font-mono text-sm text-ink-500">Order ID <span class="text-ink-700">{{ $orderId }}</span></p>
                    </div>
                    <footer class="flex items-center justify-between gap-3 border-t border-line/70 p-3 pl-4">
                        <div>
                            <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Ongkir</p>
                            <p class="tnum font-display text-xl font-extrabold tracking-tight text-brand-700">Rp {{ number_format($history->ongkir, 0, ',', '.') }}</p>
                            <p class="tnum font-mono text-xs text-ink-500">Total Rp {{ number_format($history->ongkir + $history->harga, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('historydetail') }}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $history->user_id }}" name="custId">
                                <input type="hidden" value="{{ $history->id }}" name="id">
                                <x-ui.button type="submit" icon-right="chevron-right">Detail</x-ui.button>
                            </form>
                            <form action="{{ route('room.chat.courier') }}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $history->user_id }}" name="custId">
                                <x-ui.button type="submit" variant="secondary" class="!px-0 size-11" aria-label="Chat pelanggan"><x-ui.icon name="message-circle" class="size-5" /></x-ui.button>
                            </form>
                        </div>
                    </footer>
                </article>
            </li>
        @endforeach
    </ul>
@endif
@endsection
