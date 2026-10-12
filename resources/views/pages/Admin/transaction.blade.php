@extends('layouts.admin')

@php
    // Tiga sumber disatukan: Firebase needToDeliver (menunggu driver), Firebase onProgress (berjalan), tabel histories (riwayat)
    $rows = collect();
    $fromFirebase = function ($nodes, string $source) use ($rows) {
        foreach ((array) $nodes as $d) {
            $d = (array) $d;
            $order = (string) \Illuminate\Support\Arr::get($d, 'orderID', '');
            $ongkir = (int) \Illuminate\Support\Arr::get($d, 'ongkir', 0);
            $status = (string) \Illuminate\Support\Arr::get($d, 'status', '');
            $rows->push([
                'order' => $order,
                'date' => (string) \Illuminate\Support\Arr::get($d, 'timestamp', ''),
                'user' => (string) \Illuminate\Support\Arr::get($d, 'nama_penerima', '—'),
                'cour' => $source === 'ntd' ? 'Belum diambil' : (string) (\Illuminate\Support\Arr::get($d, 'cour_name') ?? \Illuminate\Support\Arr::get($d, 'nama_kurir') ?? '—'),
                'umkm' => (string) (\Illuminate\Support\Arr::get($d, 'nama_umkm') ?? \Illuminate\Support\Arr::get($d, 'umkm') ?? '—'),
                'ongkir' => $ongkir,
                'total' => (int) \Illuminate\Support\Arr::get($d, 'total', 0) + $ongkir,
                'jarak' => (float) \Illuminate\Support\Arr::get($d, 'jarak', 0),
                'status' => $status,
                'bukti' => ! empty($d['bukti']) ? Storage::url('public/payment/'.$d['bukti']) : null,
                'bukti_akhir' => null,
            ]);
        }
    };
    $fromFirebase($dataNTD ?? [], 'ntd');
    $fromFirebase($dataOP ?? [], 'op');
    foreach (collect($datas ?? [])->sortByDesc('created_at') as $h) {
        $rows->push([
            'order' => (string) $h->order_id,
            'date' => (string) $h->created_at,
            'user' => (string) ($h->customer->nama_user ?? 'Akun dihapus'),
            'cour' => (string) ($h->courier->nama_user ?? 'Akun dihapus'),
            'umkm' => (string) (\App\Models\Data_umkm::find($h->umkm_id)->nama_umkm ?? 'Jastip'),
            'ongkir' => (int) $h->ongkir,
            'total' => (int) $h->harga + (int) $h->ongkir,
            'jarak' => (float) $h->jarak,
            'status' => (string) $h->status,
            'bukti' => $h->bukti ? Storage::url('public/payment/'.$h->bukti) : null,
            'bukti_akhir' => $h->bukti_akhir ? Storage::url('public/payment/driver/'.$h->bukti_akhir) : null,
        ]);
    }

    // Kelompok status untuk filter: berjalan / selesai / batal
    $group = function (string $status): string {
        $key = strtolower(trim($status));
        return match (true) {
            in_array($key, ['completed', 'selesai'], true) => 'done',
            in_array($key, ['canceled', 'cancelled', 'batal', 'dibatalkan'], true) => 'cancel',
            default => 'running',
        };
    };
    $rows = $rows->map(fn ($r) => $r + ['group' => $group($r['status']), 'code' => '#TRX'.strtoupper(substr($r['order'], 0, 10))])->values();
    $counts = $rows->countBy('group');
    $index = $rows->map(fn ($r, $i) => ['i' => $i, 'group' => $r['group'], 'hay' => mb_strtolower($r['code'].' '.$r['user'].' '.$r['cour'].' '.$r['umkm'])]);
    $grid = 'md:grid-cols-[8.5rem_minmax(0,1.4fr)_minmax(0,1.2fr)_9rem_8.5rem_5.5rem]';
    $money = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
@endphp

@section('page-title', 'Transaksi')
@section('subtitle', 'Pesanan yang menunggu driver, sedang diantar, dan riwayatnya.')

@section('header-actions')
    <x-ui.button :href="url('/spreadsheets')" variant="secondary" size="sm" icon="external-link"><span class="hidden sm:inline">Buka Spreadsheet</span><span class="sm:hidden">Sheet</span></x-ui.button>
@endsection

@section('content')
<div x-data="trxList(@js($index))" class="space-y-4">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="lg:w-96">
            <x-ui.input type="search" icon="search" x-model="q" placeholder="Cari Order ID, nama, atau resto" aria-label="Cari transaksi" autocomplete="off" />
        </div>
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0 lg:pb-0">
            <x-ui.chip x-on:click="group = 'all'" ::aria-pressed="group === 'all'" ::class="group === 'all' && '!bg-ink !text-canvas'">Semua <span class="font-mono text-xs opacity-75">{{ $rows->count() }}</span></x-ui.chip>
            <x-ui.chip x-on:click="group = 'running'" ::aria-pressed="group === 'running'" ::class="group === 'running' && '!bg-ink !text-canvas'">Berjalan <span class="font-mono text-xs opacity-75">{{ $counts->get('running', 0) }}</span></x-ui.chip>
            <x-ui.chip x-on:click="group = 'done'" ::aria-pressed="group === 'done'" ::class="group === 'done' && '!bg-ink !text-canvas'">Selesai <span class="font-mono text-xs opacity-75">{{ $counts->get('done', 0) }}</span></x-ui.chip>
            <x-ui.chip x-on:click="group = 'cancel'" ::aria-pressed="group === 'cancel'" ::class="group === 'cancel' && '!bg-ink !text-canvas'">Dibatalkan <span class="font-mono text-xs opacity-75">{{ $counts->get('cancel', 0) }}</span></x-ui.chip>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-surface shadow-card">
        <div class="hidden gap-4 border-b border-line bg-sunken/60 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-ink-500 md:grid {{ $grid }}">
            <span>Order ID</span><span>Pemesan</span><span>Resto</span><span class="text-right">Total</span><span>Status</span><span class="text-right">Aksi</span>
        </div>

        <ul class="divide-y divide-line">
            @foreach ($rows as $i => $r)
                <li x-show="visible({{ $i }})" class="relative grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1 px-4 py-3.5 hover:bg-canvas/70 md:gap-x-4 md:px-5 {{ $grid }}">
                    <p class="col-start-1 row-start-1 font-mono text-sm font-semibold text-ink md:col-auto md:row-auto">{{ $r['code'] }}<span class="block text-xs font-normal text-ink-500 md:hidden">{{ $r['date'] }}</span></p>
                    <div class="col-start-1 row-start-2 min-w-0 md:col-auto md:row-auto">
                        <p class="truncate text-[15px] text-ink">{{ $r['user'] }}</p>
                        <p class="hidden font-mono text-xs text-ink-500 md:block">{{ $r['date'] }}</p>
                    </div>
                    <p class="hidden truncate text-[15px] text-ink-600 md:block">{{ $r['umkm'] }}</p>
                    <p class="tnum col-start-2 row-start-1 text-right font-mono text-sm font-semibold text-ink md:col-auto md:row-auto">{{ $money($r['total']) }}<span class="hidden text-xs font-normal text-ink-500 md:block">QRIS</span></p>
                    <div class="col-start-2 row-start-2 text-right md:col-auto md:row-auto md:text-left"><x-ui.status :value="$r['status']" :label="ucfirst($r['status'])" /></div>
                    <div class="max-md:contents md:col-auto md:text-right">
                        <button type="button" data-modal-open="detail" data-order-id="{{ $r['order'] }}" data-date="{{ $r['date'] }}" data-total="{{ $r['total'] }}" data-ongkir="{{ $r['ongkir'] }}"
                                data-umkm="{{ $r['umkm'] }}" data-payment="QRIS" data-status="{{ $r['status'] }}" data-bukti="{{ $r['bukti'] }}" data-user="{{ $r['user'] }}" data-cour="{{ $r['cour'] }}"
                                data-jarak="{{ $r['jarak'] }}" data-bukti-akhir="{{ $r['bukti_akhir'] }}"
                                class="detail-button ui-press inline-flex h-9 items-center justify-center gap-1 rounded-lg bg-surface px-3 text-sm font-semibold text-ink shadow-hairline hover:bg-sunken max-md:absolute max-md:inset-0 max-md:h-auto max-md:rounded-none max-md:bg-transparent max-md:text-transparent max-md:shadow-none max-md:hover:bg-transparent" aria-label="Detail transaksi {{ $r['code'] }}">
                            Detail<x-ui.icon name="chevron-right" class="size-4 text-ink-400 max-md:hidden" />
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        <div x-show="visibleCount === 0 && {{ $rows->count() }} > 0" x-cloak>
            <x-ui.empty-state icon="search" title="Transaksi tidak ditemukan" description="Coba kata kunci lain atau ubah filter status." />
        </div>
        @if ($rows->isEmpty())
            <x-ui.empty-state icon="receipt" title="Belum ada transaksi" description="Pesanan customer akan muncul di sini." />
        @endif
    </div>
</div>

{{-- Detail transaksi. Id lama dipertahankan: orderID, date, orderBy, takenBy, ongkir, total, umkm, payment, jarak, status --}}
<x-ui.sheet name="detail" title="Detail Transaksi" description="Ringkasan pesanan dan bukti pembayaran">
    <dl class="grid grid-cols-2 gap-x-4 gap-y-5">
        @foreach ([
            ['orderID', 'Order ID', 'font-mono text-sm'], ['date', 'Tanggal', 'font-mono text-sm'],
            ['orderBy', 'Dipesan oleh', ''], ['takenBy', 'Diambil oleh', ''],
            ['umkm', 'Resto', ''], ['payment', 'Pembayaran', ''],
            ['ongkir', 'Ongkir', 'font-mono text-sm'], ['total', 'Total', 'font-mono text-sm font-bold'],
            ['jarak', 'Jarak', 'font-mono text-sm'], ['status', 'Status', 'font-semibold'],
        ] as [$id, $label, $cls])
            <div class="min-w-0">
                <dt class="text-xs font-semibold uppercase tracking-wider text-ink-500">{{ $label }}</dt>
                <dd id="{{ $id }}" class="mt-1 break-words text-[15px] text-ink {{ $cls }}">—</dd>
            </div>
        @endforeach
    </dl>

    <x-slot:footer>
        <div class="grid gap-2">
            <x-ui.button variant="secondary" icon="image" class="show-proof-button" data-bukti="">Bukti Pembayaran</x-ui.button>
            <x-ui.button variant="secondary" icon="image" class="show-proof-button-driver" data-bukti-akhir="">Bukti Pembayaran Driver</x-ui.button>
            <x-ui.button variant="outline" data-modal-close class="close-button">Tutup</x-ui.button>
        </div>
    </x-slot:footer>
</x-ui.sheet>

{{-- Bukti pembayaran --}}
<x-ui.modal name="proof" size="md" title="Bukti Pembayaran" icon="receipt">
    <img id="payment-proof-image" src="" alt="Bukti pembayaran" class="max-h-[60dvh] w-full rounded-xl bg-sunken object-contain">
    <x-slot:actions>
        <x-ui.button variant="outline" data-modal-close block>Tutup</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
@endsection

@push('js')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('trxList', (index) => ({
        index,
        q: '',
        group: 'all',
        match(r) {
            const q = this.q.trim().toLowerCase();
            return (this.group === 'all' || r.group === this.group) && (q === '' || r.hay.includes(q));
        },
        visible(i) { return this.match(this.index[i]); },
        get visibleCount() { return this.index.filter((r) => this.match(r)).length; },
    }));
});

(function () {
    var byId = function (id) { return document.getElementById(id); };
    var rupiah = function (n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); };
    var proofBtn = document.querySelector('.show-proof-button');
    var proofDriverBtn = document.querySelector('.show-proof-button-driver');

    function setProof(button, url) {
        button.dataset[button === proofBtn ? 'bukti' : 'buktiAkhir'] = url || '';
        button.disabled = !url;
        button.classList.toggle('ui-disabled', !url);
    }

    // Isi panel dari baris yang diklik (menggantikan listener .detail-button lama)
    byId('modal-detail').addEventListener('modal:opening', function (event) {
        var d = event.detail;
        var jarak = parseFloat(d.jarak) || 0;
        byId('orderID').textContent = '#TRX' + String(d.orderId || '').substring(0, 10).toUpperCase();
        byId('date').textContent = d.date || '—';
        byId('orderBy').textContent = d.user || '—';
        byId('takenBy').textContent = d.cour || '—';
        byId('umkm').textContent = d.umkm || '—';
        byId('payment').textContent = d.payment || '—';
        byId('ongkir').textContent = rupiah(d.ongkir);
        byId('total').textContent = rupiah(d.total);
        byId('jarak').textContent = jarak > 1000 ? (jarak / 1000).toFixed(2).replace('.', ',') + ' km' : Math.round(jarak) + ' m';
        byId('status').textContent = (d.status || '—').charAt(0).toUpperCase() + (d.status || '').slice(1);
        setProof(proofBtn, d.bukti);
        setProof(proofDriverBtn, d.buktiAkhir);
    });

    [proofBtn, proofDriverBtn].forEach(function (button) {
        button.addEventListener('click', function () {
            var url = button === proofBtn ? button.dataset.bukti : button.dataset.buktiAkhir;
            if (!url) { DKToast.info('Bukti pembayaran belum tersedia.'); return; }
            byId('payment-proof-image').src = url;
            dkModal.open('proof');
        });
    });
})();
</script>
@endpush
