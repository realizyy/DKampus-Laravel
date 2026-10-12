@extends('layouts.courier')

@section('page-title', 'Beranda')

@section('header-actions')
    <x-ui.tooltip text="Chat" position="bottom">
        <a href="{{ route('chatpage') }}" aria-label="Chat" class="ui-press grid size-11 place-items-center rounded-xl text-brand-700 shadow-hairline hover:bg-brand-50">
            <x-ui.icon name="message-circle" class="size-[22px]" />
        </a>
    </x-ui.tooltip>
@endsection

@section('content')
@php
    $orders = $orders ?? [];
    $count = count($orders);
    $sum = fn ($key) => collect($orders)->sum(fn ($o) => (int) ($o[$key] ?? 0));
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
@endphp

@if ($count === 0)
    <x-ui.card class="!p-0 md:mx-auto md:max-w-xl">
        <x-ui.empty-state icon="package" title="Ups, kamu belum mengambil pesanan" description="Ambil orderan dari daftar, lalu pesanan yang sedang kamu antar tampil di sini.">
            <x-ui.button :href="route('courierorder')" icon-right="arrow-right">Lihat daftar orderan</x-ui.button>
        </x-ui.empty-state>
    </x-ui.card>
@else
    <section class="space-y-5">
        <div class="flex items-end justify-between gap-3">
            <h2 class="font-display text-2xl font-extrabold tracking-tightest text-ink">Pesanan yang harus diantar</h2>
            <x-ui.badge tone="brand" class="tnum font-mono">{{ $count }} aktif</x-ui.badge>
        </div>

        {{-- Metadata ringkas (hanya desktop): hitungan order, subtotal, dan ongkir --}}
        <div class="hidden gap-3 md:grid md:grid-cols-3" x-motion.rise.stagger>
            <x-ui.stat label="Order aktif" :value="$count" meta="sedang diantar" icon="bike" />
            <x-ui.stat label="Subtotal" :value="$rp($sum('total'))" meta="uang talangan menu" icon="receipt" />
            <x-ui.stat label="Ongkir" :value="$rp($sum('ongkir'))" meta="penghasilan antar" icon="wallet" />
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($orders as $i => $item)
                <x-courier.order-card :item="$item" :cust-id="$custId[$i]" :phone-umkm="$no_telp_umkm[$i] ?? '-'" :phone-cust="$no_telp_cust[$i] ?? '-'" />
            @endforeach
        </div>
    </section>

    {{-- Modal Selesaikan Pesanan (Figma): ringkasan + total + bukti pembayaran --}}
    <x-ui.modal name="complete" size="md" tone="success" title="Selesaikan Pesanan" description="Apakah kamu yakin ingin menyelesaikan pesanan ini?">
        <form action="{{ route('complete.orders') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="custId" value="">
            <div data-slot="summary"></div>
            <x-ui.field label="Total" for="total-price" hint="*Total harga menu yang kamu keluarkan" required>
                <x-ui.input type="number" name="total-price" id="total-price" min="0" placeholder="Rp 30.000" required />
            </x-ui.field>
            <x-ui.field label="Bukti Pembayaran" hint="*Unggah bukti pembayaran mengirimkan pesanan" required>
                <x-ui.file-input name="bukti" id="bukti" required />
            </x-ui.field>
            <div class="flex flex-col gap-2 pt-1">
                <x-ui.button type="submit" variant="success" block>Selesaikan</x-ui.button>
                <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- Modal Batalkan Pesanan --}}
    <x-ui.modal name="cancel" size="md" tone="danger" icon="triangle-alert" title="Batalkan Pesanan" description="Apakah kamu yakin ingin membatalkan pesanan ini?">
        <form action="{{ route('delete.orders') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="custId" value="">
            <div data-slot="summary"></div>
            <x-ui.field label="Alasan Pembatalan" for="alasan" required>
                <x-ui.textarea name="alasan" id="alasan" placeholder="Alasan pembatalan" required></x-ui.textarea>
            </x-ui.field>
            <x-ui.field label="Total Harga" for="total-price-cancel" hint="*Total harga menu yang kamu keluarkan (isi 0 jika belum membeli menu)" required>
                <x-ui.input type="number" name="total-price-cancel" id="total-price-cancel" min="0" placeholder="Rp 30.000" required />
            </x-ui.field>
            <x-ui.field label="Bukti Pembatalan" hint="*Unggah bukti pembatalan pesanan" required>
                <x-ui.file-input name="bukti_batal" id="bukti_batal" required />
            </x-ui.field>
            <div class="flex flex-col gap-2 pt-1">
                <x-ui.button type="submit" variant="danger" block>Batalkan Pesanan</x-ui.button>
                <x-ui.button variant="outline" data-modal-close block>Kembali</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    @push('js')
    <script>
        // Isi modal dari kartu yang diklik: custId + ringkasan order (bukan selalu order terakhir/pertama)
        ['complete', 'cancel'].forEach(function (name) {
            var dialog = document.getElementById('modal-' + name);
            if (!dialog) return;
            dialog.addEventListener('modal:opening', function (event) {
                var detail = event.detail || {};
                dialog.querySelector('input[name="custId"]').value = detail.custId || '';
                var slot = dialog.querySelector('[data-slot="summary"]');
                var template = detail.summary ? document.querySelector(detail.summary) : null;
                slot.replaceChildren();
                if (template) slot.appendChild(template.content.cloneNode(true));
            });
        });
    </script>
    @endpush
@endif
@endsection
