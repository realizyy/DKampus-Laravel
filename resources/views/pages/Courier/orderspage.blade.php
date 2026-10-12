@extends('layouts.courier')

@section('page-title', 'Daftar Orderan')
@section('back', route('dashboardCourier'))

@section('content')
<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
    <section aria-label="Daftar orderan">
        <ul id="orderContainer" class="space-y-3">
            {{-- Order dari Firebase (needToDeliver) dirender di sini oleh skrip di bawah --}}
        </ul>
        <div id="orderEmpty" class="hidden">
            <x-ui.card class="!p-0">
                <x-ui.empty-state icon="bike" title="Belum ada orderan baru" description="Orderan baru dari pelanggan muncul di sini secara langsung." />
            </x-ui.card>
        </div>
    </section>

    {{-- Panel detail (desktop). Di mobile detail tampil sebagai modal. --}}
    <aside class="hidden lg:block" aria-label="Detail orderan">
        <div id="orderDetailPanel" class="sticky top-6 rounded-2xl bg-surface p-5 shadow-card">
            <p class="py-10 text-center text-[15px] text-ink-500">Pilih orderan untuk melihat detailnya.</p>
        </div>
    </aside>
</div>

<x-ui.modal name="order-detail" size="md" title="Order Details" icon="receipt">
    <div id="modalOrderDetails"></div>
    <x-slot:actions>
        <x-ui.button id="closeModalBtn" variant="outline" data-modal-close block>Tutup</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
@endsection

@push('js')
@php use App\Support\Icon; @endphp
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script>
(function () {
    var container = document.getElementById('orderContainer');
    var emptyState = document.getElementById('orderEmpty');
    var panel = document.getElementById('orderDetailPanel');
    var modalBody = document.getElementById('modalOrderDetails');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var takeUrl = @json(route('take.order'));
    var desktop = window.matchMedia('(min-width: 1024px)');
    var orders = {};
    var selected = null;

    var ICON = {
        store: @json(Icon::svg('store', 'size-5')),
        pin: @json(Icon::svg('navigation', 'size-3.5')),
        wallet: @json(Icon::svg('wallet', 'size-3.5')),
        eye: @json(Icon::svg('eye', 'size-[18px]')),
        bike: @json(Icon::svg('bike', 'size-[18px]')),
    };

    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function rp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
    function distance(m) {
        m = Number(m);
        if (!isFinite(m) || m <= 0) return null;
        return m > 1000 ? (m / 1000).toFixed(1).replace('.', ',') + ' km' : Math.round(m) + ' m';
    }
    function lines(data) { return Object.keys(data.orders || {}).map(function (k) { return data.orders[k]; }); }

    function takeForm(id, block) {
        return '<form action="' + esc(takeUrl) + '" method="POST" class="' + (block ? 'w-full' : '') + '">' +
            '<input type="hidden" name="_token" value="' + esc(csrf) + '">' +
            '<input type="hidden" name="orderId" value="' + esc(id) + '">' +
            '<button type="submit" class="ui-press inline-flex h-11 select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-brand-700 px-4 text-[15px] font-semibold tracking-[-0.005em] text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.16),0_0_0_1px_rgb(124_54_18/0.7)] hover:bg-brand-800 active:bg-brand-900 ' + (block ? 'w-full' : '') + '">' + ICON.bike + 'Take Order</button>' +
            '</form>';
    }

    function renderRow(id, data) {
        var items = lines(data).map(function (l) {
            return '<span class="tnum font-mono font-semibold text-ink-700">' + esc(l.jumlah) + '×</span> ' + esc(l.nama);
        }).join(' · ');
        var dist = distance(data.jarak);
        var li = document.createElement('li');
        li.id = id;
        li.className = 'rounded-2xl bg-surface shadow-card transition-shadow data-[selected=true]:shadow-pop data-[selected=true]:ring-2 data-[selected=true]:ring-brand-500';
        li.innerHTML =
            '<div class="flex items-start gap-3 p-4">' +
                '<span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800">' + ICON.store + '</span>' +
                '<div class="min-w-0 flex-1">' +
                    '<h2 class="truncate font-display text-lg font-bold tracking-tight text-ink">' + esc(data.nama_umkm) + '</h2>' +
                    '<p class="font-mono text-xs text-ink-500">' + esc(data.timestamp) + '</p>' +
                '</div>' +
                '<span class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold leading-5 text-amber-700"><span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>Waiting For Driver</span>' +
            '</div>' +
            '<div class="space-y-2 px-4 pb-4 text-[15px]">' +
                '<p class="text-ink-700"><span class="font-semibold text-ink">Pesanan:</span> ' + items + '</p>' +
                '<div class="flex flex-wrap gap-2 font-mono text-xs text-ink-600">' +
                    '<span class="inline-flex items-center gap-1 rounded-md bg-sunken px-2 py-1">' + ICON.wallet + 'Ongkir <b class="tnum text-ink">' + esc(rp(data.ongkir)) + '</b></span>' +
                    (dist ? '<span class="inline-flex items-center gap-1 rounded-md bg-sunken px-2 py-1">' + ICON.pin + esc(dist) + '</span>' : '') +
                '</div>' +
            '</div>' +
            '<div class="flex flex-wrap items-center justify-end gap-2 border-t border-line/70 bg-sunken/50 p-3">' +
                '<button type="button" data-order-detail="' + esc(id) + '" class="ui-press inline-flex h-11 select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-surface px-4 text-[15px] font-semibold text-ink shadow-card hover:bg-sunken active:bg-line">' + ICON.eye + 'View Details</button>' +
                takeForm(id, false) +
            '</div>';
        return li;
    }

    function detailHtml(id, data) {
        var rows = lines(data).map(function (l) {
            var note = l.catatan && l.catatan !== '-' ? '<span class="text-ink-500"> — ' + esc(l.catatan) + '</span>' : '';
            return '<li><span class="tnum font-mono font-semibold text-ink-700">' + esc(l.jumlah) + '×</span> ' + esc(l.nama) + note + '</li>';
        }).join('');
        function row(label, value) {
            return '<div class="space-y-0.5"><dt class="font-mono text-[11px] uppercase tracking-widest text-ink-500">' + label + '</dt><dd class="text-[15px] text-ink">' + value + '</dd></div>';
        }
        return '<dl class="space-y-4">' +
            row('Nama Penerima', esc(data.nama_penerima)) +
            row('Nama UMKM', esc(data.nama_umkm)) +
            row('Catatan alamat', esc(data.notesAlamat && data.notesAlamat !== '-' ? data.notesAlamat : '—')) +
            row('Orders', '<ul class="space-y-1">' + rows + '</ul>') +
            '<div class="rounded-xl bg-sunken p-3 font-mono text-sm text-ink-600">' +
                '<div class="flex justify-between"><span>Subtotal</span><span class="tnum">' + esc(rp(data.total)) + '</span></div>' +
                '<div class="flex justify-between"><span>Ongkir</span><span class="tnum">' + esc(rp(data.ongkir)) + '</span></div>' +
                '<div class="mt-1 flex justify-between border-t border-line pt-1 font-semibold text-ink"><span>Total + Ongkir</span><span class="tnum">' + esc(rp(Number(data.total || 0) + Number(data.ongkir || 0))) + '</span></div>' +
            '</div>' +
        '</dl>';
    }

    function showDetail(id) {
        var data = orders[id];
        if (!data) return;
        selected = id;
        Array.prototype.forEach.call(container.children, function (el) { el.dataset.selected = String(el.id === id); });
        if (desktop.matches) {
            panel.innerHTML =
                '<div class="mb-4 flex items-center justify-between gap-3"><h2 class="font-display text-xl font-bold tracking-tight text-ink">Order Details</h2></div>' +
                detailHtml(id, data) + '<div class="mt-5">' + takeForm(id, true) + '</div>';
        } else {
            modalBody.innerHTML = detailHtml(id, data);
            window.dkModal.open('order-detail');
        }
    }

    function syncEmpty() { emptyState.classList.toggle('hidden', container.children.length > 0); }

    function add(id, data) {
        orders[id] = data;
        container.prepend(renderRow(id, data));
        syncEmpty();
        if (desktop.matches && !selected) showDetail(id);
    }
    function remove(id) {
        delete orders[id];
        var el = document.getElementById(id);
        if (el) el.remove();
        if (selected === id) {
            selected = null;
            panel.innerHTML = '<p class="py-10 text-center text-[15px] text-ink-500">Pilih orderan untuk melihat detailnya.</p>';
            var first = container.firstElementChild;
            if (first && desktop.matches) showDetail(first.id);
        }
        syncEmpty();
    }

    container.addEventListener('click', function (event) {
        var button = event.target.closest('[data-order-detail]');
        if (button) showDetail(button.getAttribute('data-order-detail'));
    });

    // Sumber data: Firebase Realtime Database (hanya membaca needToDeliver). Pratinjau lokal bisa mengganti lewat window.dkOrdersSource.
    @isset($previewOrders)
        window.dkOrdersSource = function (onAdd) { @json($previewOrders).forEach(function (o) { onAdd(o.id, o.data); }); };
    @endisset
    syncEmpty();
    if (window.dkOrdersSource) {
        window.dkOrdersSource(add, remove);
    } else {
        try {
            var database = firebase.database();
            database.ref('needToDeliver').on('child_added', function (snapshot) { add(snapshot.key, snapshot.val()); });
            database.ref('needToDeliver').on('child_removed', function (snapshot) { remove(snapshot.key); });
        } catch (error) {
            console.warn('Firebase belum siap:', error);
        }
    }
})();
</script>
@endpush
