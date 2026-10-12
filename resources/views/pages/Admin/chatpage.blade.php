@extends('layouts.admin')

@section('page-title', 'Chat')
@section('subtitle', 'Pesan masuk dari customer ke Customer Service.')

@section('content')
<div x-data="{ open: false }" @chat-open.window="open = true" @chat-close.window="open = false"
     class="flex h-[calc(100dvh-12.5rem)] min-h-[26rem] overflow-hidden rounded-2xl bg-surface shadow-card md:h-[calc(100dvh-11rem)]">
    {{-- Daftar chat --}}
    <aside class="min-w-0 flex-col border-line md:flex md:w-80 md:shrink-0 md:border-r" :class="open ? 'hidden' : 'flex w-full'" aria-label="Daftar chat">
        <div class="flex items-center justify-between border-b border-line px-4 py-3.5">
            <p class="font-display text-lg font-bold tracking-tight text-ink">Chats</p>
            <span id="chat-count" class="font-mono text-xs text-ink-500">0 percakapan</span>
        </div>
        <div id="list-chat" class="flex-1 divide-y divide-line overflow-y-auto" role="list">
            {{-- Daftar chat dari Firebase dirender di sini --}}
            <p id="list-empty" class="px-6 py-12 text-center text-sm text-ink-500">Belum ada pesan masuk.</p>
        </div>
    </aside>

    {{-- Ruang chat --}}
    <section class="min-w-0 flex-1 flex-col md:flex" :class="open ? 'flex' : 'hidden'" aria-label="Ruang chat">
        <header id="room-header" class="hidden items-center gap-3 border-b border-line px-3 py-2.5 md:px-4">
            <button type="button" @click="$dispatch('chat-close')" class="ui-press grid size-10 place-items-center rounded-lg text-brand-700 hover:bg-sunken md:hidden" aria-label="Kembali ke daftar chat"><x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" /></button>
            <x-ui.avatar name="" id="room-avatar" />
            <p id="room-name" class="min-w-0 flex-1 truncate font-display text-lg font-bold tracking-tight text-ink">—</p>
        </header>

        <div class="min-h-0 flex-1 overflow-y-auto bg-canvas/60 px-3 py-4 md:px-5" id="room-chat" aria-live="polite">
            <p id="ifx" class="flex h-full flex-col items-center justify-center gap-3 text-center text-ink-500">
                <span class="grid size-14 place-items-center rounded-2xl bg-sunken shadow-hairline"><x-ui.icon name="message-circle" class="size-6" /></span>
                Silakan pilih chat untuk melihat pesan
            </p>
            {{-- Pesan dirender di sini --}}
        </div>

        <form id="message-form" action="#" method="POST" class="border-t border-line bg-surface p-3">
            @csrf
            <div class="flex items-center gap-2">
                <input type="text" id="custId" class="hidden" value="">
                <input id="message-input" type="text" autocomplete="off" placeholder="Ketik pesan…" disabled
                       class="block h-11 min-w-0 flex-1 rounded-xl border-0 bg-surface px-4 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line transition placeholder:text-ink-400 hover:ring-line-strong focus:ring-2 focus:ring-brand-600 disabled:cursor-not-allowed disabled:bg-sunken">
                <x-ui.button type="submit" id="send-btn" class="!px-0 size-11 shrink-0" aria-label="Kirim pesan" disabled><x-ui.icon name="send" class="size-5" /></x-ui.button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('js')
@unless (isset($previewChats))
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
@endunless
<script>
(function () {
    var COUR_ID = '1'; // Chat Customer Service (admin) memakai id kurir 1
    var IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'heic', 'webp'];
    var byId = function (id) { return document.getElementById(id); };
    var list = byId('list-chat'), room = byId('room-chat'), form = byId('message-form');
    var input = byId('message-input'), sendBtn = byId('send-btn'), idField = byId('custId');
    var preview = @json($previewChats ?? null);
    var database = null;
    var chats = {};          // custId -> { name, last, ts }
    var current = null;      // custId yang sedang dibuka
    var roomRef = null, roomHandler = null;

    if (!preview) {
        try {
            firebase.initializeApp({
                apiKey: @json(env('FIREBASE_API_KEY')),
                authDomain: @json(env('FIREBASE_AUTH_DOMAIN')),
                databaseURL: @json(env('FIREBASE_DATABASE_URL')),
                projectId: @json(env('FIREBASE_PROJECT_ID')),
                storageBucket: @json(env('FIREBASE_STORAGE_BUCKET')),
                messagingSenderId: @json(env('FIREBASE_MESSAGING_SENDER_ID')),
                appId: @json(env('FIREBASE_APP_ID')),
                measurementId: @json(env('FIREBASE_MEASUREMENT_ID'))
            });
            database = firebase.database();
        } catch (e) { console.warn('Firebase belum siap:', e); }
    }

    function pad(n) { return String(n).padStart(2, '0'); }
    function parseTs(value) {
        if (typeof value === 'number') return new Date(value);
        var d = new Date(String(value).replace(' ', 'T'));
        return isNaN(d.getTime()) ? new Date() : d;
    }
    function hhmm(ts) { var d = parseTs(ts); return pad(d.getHours()) + ':' + pad(d.getMinutes()); }
    function isImageFile(name) { return IMAGE_EXT.indexOf(String(name).split('.').pop().toLowerCase()) !== -1; }
    function initials(name) { return (name || '').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('') || '·'; }
    function el(tag, cls, text) { var n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; }

    // ---- Daftar chat (urut dari pesan terbaru) ----
    function renderList() {
        var ids = Object.keys(chats).sort(function (a, b) { return parseTs(chats[b].ts) - parseTs(chats[a].ts); });
        list.querySelectorAll('.chat-item').forEach(function (n) { n.remove(); });
        byId('list-empty').hidden = ids.length > 0;
        byId('chat-count').textContent = ids.length + ' percakapan';
        ids.forEach(function (id) {
            var c = chats[id];
            var button = el('button', 'chat-item ui-press flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-canvas ' + (id === current ? 'bg-brand-50' : ''));
            button.type = 'button';
            button.dataset.delId = id;
            button.appendChild(el('span', 'grid size-11 shrink-0 place-items-center rounded-full bg-brand-100 font-display font-bold text-brand-800 shadow-hairline', initials(c.name)));
            var body = el('span', 'chat-list min-w-0 flex-1');
            var top = el('span', 'flex items-center justify-between gap-2');
            top.appendChild(el('span', 'truncate font-semibold text-ink', c.name || 'Customer ' + id));
            var time = el('span', 'time shrink-0 font-mono text-xs text-ink-500', c.ts ? hhmm(c.ts) : '');
            time.dataset.time = c.ts || '';
            top.appendChild(time);
            body.appendChild(top);
            body.appendChild(el('span', 'message block truncate text-sm text-ink-500', isImageFile(c.last) ? 'Mengirim gambar' : (c.last || '')));
            button.appendChild(body);
            button.addEventListener('click', function () { loadChat(id); });
            list.appendChild(button);
        });
    }

    function upsertChat(id, patch) {
        chats[id] = Object.assign(chats[id] || {}, patch);
        renderList();
    }

    // ---- Ruang chat ----
    var lastDateKey = null;
    function dateLabel(d) {
        var today = new Date(), yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);
        if (d.toDateString() === today.toDateString()) return 'Hari ini';
        if (d.toDateString() === yesterday.toDateString()) return 'Kemarin';
        return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }
    function roomChat(role, message, timestamp) {
        var d = parseTs(timestamp);
        if (d.toDateString() !== lastDateKey) {
            var label = el('div', 'date-label my-2 text-center');
            label.appendChild(el('span', 'inline-block rounded-full bg-sunken px-3 py-1 font-mono text-xs font-semibold text-ink-600', dateLabel(d)));
            room.appendChild(label);
            lastDateKey = d.toDateString();
        }
        var mine = role === 'courier' || role === 'admin'; // sisi Customer Service di kanan
        var row = el('div', 'mb-2 flex ' + (mine ? 'justify-end' : 'justify-start'));
        var bubble = el('div', 'max-w-[80%] rounded-2xl px-3.5 py-2.5 text-[15px] leading-snug ' + (mine ? 'rounded-br-md bg-brand-700 text-white' : 'rounded-bl-md bg-surface text-ink shadow-card'));
        if (isImageFile(message)) {
            var img = el('img', 'max-h-72 w-full max-w-[16rem] rounded-xl bg-surface object-contain');
            img.src = /^(https?:)?\//.test(message) ? message : '/storage/chatsImg/' + message;
            img.alt = 'Gambar dikirim';
            img.loading = 'lazy';
            bubble.appendChild(img);
        } else {
            bubble.appendChild(el('p', 'whitespace-pre-wrap break-words', message));
        }
        bubble.appendChild(el('p', 'mt-1 text-right font-mono text-[11px] ' + (mine ? 'text-white' : 'text-ink-500'), hhmm(timestamp)));
        row.appendChild(bubble);
        room.appendChild(row);
        row.scrollIntoView({ block: 'end', behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
    }

    function loadChat(custId) {
        if (roomRef && roomHandler) roomRef.off('child_added', roomHandler);
        current = custId;
        lastDateKey = null;
        room.innerHTML = '';
        byId('ifx') && byId('ifx').remove();
        var c = chats[custId] || {};
        byId('room-header').classList.remove('hidden');
        byId('room-header').classList.add('flex');
        byId('room-name').textContent = c.name || 'Customer ' + custId;
        byId('room-avatar').textContent = initials(c.name);
        idField.value = custId;
        input.disabled = false;
        sendBtn.disabled = false;
        window.dispatchEvent(new CustomEvent('chat-open'));
        renderList();

        if (preview) {
            (preview[custId] ? preview[custId].messages : []).forEach(function (m) { roomChat(m.role, m.msg, m.timestamp); });
            return;
        }
        if (!database) return;
        roomRef = database.ref('chats/' + custId + '-' + COUR_ID);
        roomHandler = function (snapshot) {
            var data = snapshot.val();
            if (data && data.msgs) roomChat(data.msgs.role, data.msgs.msg, data.msgs.timestamp);
        };
        roomRef.on('child_added', roomHandler);
    }

    function sendMessage(message, custId) {
        var now = new Date();
        var stamp = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        if (preview || !database) { roomChat('admin', message, stamp); upsertChat(custId, { last: message, ts: stamp }); return; }
        var ref = database.ref('chats/' + custId + '-' + COUR_ID);
        ref.push().set({ msgs: { role: 'admin', msg: message, timestamp: stamp } });
        ref.child('custNewMssg').transaction(function (value) { return (value || 0) + 1; });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var message = input.value.trim();
        var custId = idField.value;
        if (message !== '' && custId) {
            sendMessage(message, custId);
            input.value = '';
        }
    });

    // ---- Sumber data ----
    if (preview) {
        Object.keys(preview).forEach(function (id) {
            var msgs = preview[id].messages || [], last = msgs[msgs.length - 1] || {};
            chats[id] = { name: preview[id].name, last: last.msg, ts: last.timestamp };
        });
        renderList();
    } else if (database) {
        database.ref('chats').on('child_added', function (snapshot) {
            var parts = snapshot.key.split('-');
            if (parts[1] !== COUR_ID) return;
            var custId = parts[0];
            upsertChat(custId, { name: (snapshot.val() || {}).cust_name });
            // Pesan terakhir per percakapan (juga memicu pembaruan saat ada pesan baru)
            snapshot.ref.orderByChild('msgs/timestamp').limitToLast(1).on('child_added', function (child) {
                var message = (child.val() || {}).msgs;
                if (message) upsertChat(custId, { last: message.msg, ts: message.timestamp });
            });
        });
    }
})();
</script>
@endpush
