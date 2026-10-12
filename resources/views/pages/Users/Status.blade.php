@extends('layouts.customer')

@php
    $histories = collect($data ?? [])->sortByDesc('created_at')->values();
    $umkmNames = \App\Models\Data_umkm::whereIn('id', $histories->pluck('umkm_id')->filter()->unique())->pluck('nama_umkm', 'id');
@endphp

@section('page-title', 'Pesanan')

@section('content')
<div class="space-y-5">
    <x-customer.order-tabs active="proses" />

    {{-- Pesanan aktif (Firebase: needToDeliver / onProgress) dirender di sini oleh skrip --}}
    <div id="card" class="grid gap-3 lg:grid-cols-2"></div>

    @if ($histories->isNotEmpty())
        <section class="space-y-3" aria-label="Riwayat">
            <h2 class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Riwayat</h2>
            <div class="grid gap-3 lg:grid-cols-2" x-motion.rise.stagger>
                @foreach ($histories as $history)
                    <x-customer.order-card :history="$history" :umkm="$history->umkm_id ? ($umkmNames[$history->umkm_id] ?? 'UMKM tidak tersedia') : 'Jastip'" />
                @endforeach
            </div>
        </section>
    @endif

    <div id="status-empty" class="{{ $histories->isNotEmpty() ? 'hidden' : '' }}">
        <x-ui.card class="!p-0"><x-ui.empty-state icon="clipboard-list" title="Ups, kamu belum pernah melakukan transaksi" description="Pesanan yang sedang diproses dan riwayatmu muncul di sini."><x-ui.button :href="url('/')" icon-right="arrow-right">Cari makanan</x-ui.button></x-ui.empty-state></x-ui.card>
    </div>
</div>
@endsection

@push('js')
@php use App\Support\Icon; @endphp
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script>
(function () {
    var custId = @json((string) $custId);
    var container = document.getElementById('card');
    var emptyState = document.getElementById('status-empty');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var detailUrl = @json(route('status.order'));
    var chatUrl = @json(route('room.chat'));
    var ICON = {
        store: @json(Icon::svg('store', 'size-5')),
        chevron: @json(Icon::svg('chevron-right', 'size-[18px]')),
        chat: @json(Icon::svg('message-circle', 'size-5')),
    };
    var BADGE = {
        'on delivery': 'bg-teal-100 text-teal-700',
        'searching': 'bg-amber-100 text-amber-700',
        'completed': 'bg-moss-100 text-moss-700',
        'canceled': 'bg-brick-100 text-brick-700',
    };
    var BTN = 'ui-press inline-flex h-11 select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 text-[15px] font-semibold tracking-[-0.005em] ';

    function esc(v) { return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
    function rp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
    function badge(status) {
        var cls = BADGE[String(status).toLowerCase()] || 'bg-sunken text-ink-600';
        return '<span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold leading-5 ' + cls + '"><span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>' + esc(status) + '</span>';
    }
    function input(name, value) { return '<input type="hidden" name="' + name + '" value="' + esc(value) + '">'; }
    function formatDate(ts) {
        var d = new Date(ts);
        if (isNaN(d.getTime())) return '';
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    function render(o) {
        var el = document.createElement('article');
        el.className = 'flex flex-col rounded-2xl bg-surface shadow-card';
        el.setAttribute('data-id', o.orderId);
        el.innerHTML =
            '<header class="flex items-start gap-3 p-4">' +
                '<span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800">' + ICON.store + '</span>' +
                '<div class="min-w-0 flex-1"><h2 class="truncate font-display text-lg font-bold tracking-tight text-ink">' + esc(o.umkm) + '</h2><p class="font-mono text-xs text-ink-500">' + esc(o.date) + '</p></div>' +
                '<span id="' + esc(o.orderId) + '" data-badge>' + badge(o.status) + '</span>' +
            '</header>' +
            '<div class="flex-1 space-y-1 px-4 pb-4 text-[15px]"><p class="text-ink-700"><span class="font-semibold text-ink">Pesanan:</span> ' + esc(o.items) + '</p>' +
                '<p class="font-mono text-sm text-ink-500">Order ID <span class="text-ink-700">#TRX' + esc(o.orderId.toUpperCase()) + '</span></p></div>' +
            '<footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line/70 p-3 pl-4">' +
                '<div><p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Total</p><p class="tnum font-display text-xl font-extrabold tracking-tight text-brand-700">' + esc(rp(o.total)) + '</p></div>' +
                '<div class="flex items-center gap-2">' +
                    '<form action="' + esc(detailUrl) + '" method="POST">' + input('_token', csrf) + input('custId', custId) + (o.courId ? input('courId', o.courId) : '') + input('id', o.orderId) +
                        '<button type="submit" class="' + BTN + 'bg-brand-700 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.16),0_0_0_1px_rgb(124_54_18/0.7)] hover:bg-brand-800">Detail' + ICON.chevron + '</button></form>' +
                    (o.courId ? '<form action="' + esc(chatUrl) + '" method="POST">' + input('_token', csrf) + input('courId', o.courId) +
                        '<button type="submit" aria-label="Chat kurir" class="' + BTN + '!px-0 size-11 bg-surface text-ink shadow-card hover:bg-sunken">' + ICON.chat + '</button></form>' : '') +
                '</div>' +
            '</footer>';
        return el;
    }

    function show(o) {
        var existing = container.querySelector('[data-id="' + CSS.escape(o.orderId) + '"]');
        if (existing) existing.remove();
        container.prepend(render(o));
        emptyState.classList.add('hidden');
    }
    function setStatus(orderId, status) {
        var span = document.getElementById(orderId);
        if (span) span.innerHTML = badge(status);
    }
    function summary(data, courId) {
        return {
            umkm: data.nama_umkm, date: formatDate(data.timestamp), status: data.status,
            items: Object.keys(data.orders || {}).map(function (k) { return data.orders[k].jumlah + ' ' + data.orders[k].nama; }).join(', '),
            orderId: String(data.orderID || '').slice(0, 10), total: Number(data.total || 0) + Number(data.ongkir || 0), courId: courId || '',
        };
    }

    @isset($previewLive)
        window.dkStatusSource = function (api) { @json($previewLive).forEach(function (o) { api.show(o); }); };
    @endisset
    if (window.dkStatusSource) {
        window.dkStatusSource({ show: show, setStatus: setStatus });
        return;
    }
    try {
        var database = firebase.database();
        // Sedang diantar (kurir sudah mengambil): onProgress/{custId}-{courId}
        database.ref('onProgress').on('child_added', function (snapshot) {
            var parts = snapshot.key.split('-');
            if (parts[0] !== custId) return;
            var data = snapshot.val();
            var o = summary(data, parts[1]);
            show(o);
            database.ref('onProgress/' + snapshot.key + '/status').on('value', function (s) { if (s.val()) setStatus(o.orderId, s.val()); });
        });
        database.ref('onProgress').on('child_removed', function (snapshot) {
            var data = snapshot.val(); if (data && data.orderID) { var el = container.querySelector('[data-id="' + CSS.escape(String(data.orderID).slice(0, 10)) + '"]'); if (el) el.remove(); }
        });
        // Menunggu kurir: needToDeliver/{custId}-
        database.ref('needToDeliver').on('child_added', function (snapshot) {
            if (snapshot.key.split('-')[0] !== custId) return;
            show(summary(snapshot.val(), ''));
        });
        database.ref('needToDeliver').on('child_removed', function (snapshot) {
            var data = snapshot.val(); if (data && data.orderID) { var el = container.querySelector('[data-id="' + CSS.escape(String(data.orderID).slice(0, 10)) + '"]'); if (el) el.remove(); }
        });
    } catch (error) {
        console.warn('Firebase belum siap:', error);
    }
})();
</script>
@endpush
