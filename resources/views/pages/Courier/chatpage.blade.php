@extends('layouts.courier')

@section('page-title', 'Chat')
@section('back', route('dashboardCourier'))

@section('content')
<div class="mx-auto max-w-2xl">
    {{-- Klik percakapan: kirim custId ke chatWaCour (membuka WhatsApp pelanggan) --}}
    <form id="chat-form" action="{{ route('chatWaCour') }}" method="POST">
        @csrf
        <input type="hidden" name="custId" value="">
        <x-ui.card :padded="false" class="overflow-hidden">
            <ul id="chat-list" class="divide-y divide-line/70">
                {{-- Daftar chat dari Firebase dirender di sini --}}
            </ul>
            <div id="chat-empty" class="hidden">
                <x-ui.empty-state icon="message-circle" title="Belum ada percakapan" description="Percakapan dengan pelanggan muncul di sini setelah kamu mengambil pesanan." />
            </div>
        </x-ui.card>
    </form>
</div>
@endsection

@push('js')
@php use App\Support\Icon; @endphp
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script>
(function () {
    var courId = @json((string) $courId);
    var list = document.getElementById('chat-list');
    var emptyState = document.getElementById('chat-empty');
    var form = document.getElementById('chat-form');
    var rows = {}; // custId -> { el, ts }
    var IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'heic', 'webp'];
    var ICON_IMAGE = @json(Icon::svg('image', 'size-3.5'));

    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function isImageFile(name) { return IMAGE_EXT.indexOf(String(name).split('.').pop().toLowerCase()) !== -1; }
    // Timestamp chat: "YYYY-MM-DD HH:mm:ss" (string) atau milidetik
    function parseTs(value) {
        if (typeof value === 'number') return value;
        var ms = new Date(String(value).replace(' ', 'T')).getTime();
        return isNaN(ms) ? 0 : ms;
    }
    function fmtTime(ms) {
        if (!ms) return '';
        var d = new Date(ms), now = new Date();
        if (d.toDateString() === now.toDateString()) return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }
    function initials(name) {
        return String(name || '?').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('');
    }
    function syncEmpty() { emptyState.classList.toggle('hidden', list.children.length > 0); }

    function buildRow(custId, name) {
        var li = document.createElement('li');
        li.innerHTML =
            '<button type="submit" data-del-id="' + esc(custId) + '" class="chat-item ui-press flex w-full items-center gap-3 p-4 text-left hover:bg-sunken">' +
                '<span class="grid size-12 shrink-0 place-items-center rounded-full bg-brand-100 font-display text-base font-bold text-brand-800">' + esc(initials(name)) + '</span>' +
                '<span class="min-w-0 flex-1" data-cust-id="' + esc(custId) + '">' +
                    '<span class="block truncate font-display text-base font-bold tracking-tight text-ink">' + esc(name) + '</span>' +
                    '<span class="message mt-0.5 flex items-center gap-1 truncate text-sm text-ink-500"></span>' +
                '</span>' +
                '<span class="timestamp shrink-0 self-start pt-1 font-mono text-xs text-ink-500" data-time="0"></span>' +
            '</button>';
        li.querySelector('button').addEventListener('click', function (event) {
            event.preventDefault();
            form.querySelector('input[name="custId"]').value = custId;
            // Tandai pesan pelanggan sudah dibaca
            try { firebase.database().ref('chats/' + custId + '-' + courId + '/custNewMssg').set(0); } catch (e) { /* preview/offline */ }
            form.requestSubmit();
        });
        return li;
    }

    function update(custId, name, message, ts) {
        var row = rows[custId];
        if (!row) {
            row = rows[custId] = { el: buildRow(custId, name), ts: 0 };
            list.appendChild(row.el);
        }
        if (ts < row.ts) return; // pesan lama
        row.ts = ts;
        var text = row.el.querySelector('.message');
        if (isImageFile(message)) { text.innerHTML = ICON_IMAGE + '<span>Foto</span>'; } else { text.textContent = message; }
        var stamp = row.el.querySelector('.timestamp');
        stamp.textContent = fmtTime(ts);
        stamp.dataset.time = String(ts);
        // Urutkan: terbaru di atas
        Object.keys(rows).sort(function (a, b) { return rows[b].ts - rows[a].ts; }).forEach(function (id) { list.appendChild(rows[id].el); });
        syncEmpty();
    }

    @isset($previewChats)
        window.dkChatsSource = function (cb) { @json($previewChats).forEach(function (c) { cb(c.custId, c.name, c.message, parseTs(c.timestamp)); }); };
    @endisset
    if (window.dkChatsSource) {
        window.dkChatsSource(update);
    } else {
        try {
            var database = firebase.database();
            database.ref('chats').on('child_added', function (snapshot) {
                var parts = snapshot.key.split('-');
                if (parts[1] !== courId) return;
                var custId = parts[0];
                var name = (snapshot.val() && snapshot.val().cust_name) || ('Pelanggan ' + custId);
                // Hanya pesan terakhir; child_added berikutnya = pesan baru
                snapshot.ref.orderByChild('msgs/timestamp').limitToLast(1).on('child_added', function (child) {
                    var data = child.val();
                    if (data && data.msgs) update(custId, name, data.msgs.msg, parseTs(data.msgs.timestamp));
                });
            });
        } catch (error) {
            console.warn('Firebase belum siap:', error);
        }
    }
    setTimeout(syncEmpty, 1500);
})();
</script>
@endpush
