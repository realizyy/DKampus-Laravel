@extends('layouts.courier')

@php
    $waValid = str_starts_with((string) $wa, '+');
@endphp

@section('page-title', $cust_name)
@section('back', route('chatpage'))
@section('hide-nav', '1')

@if ($waValid)
    @section('header-actions')
        <x-ui.tooltip text="Buka WhatsApp" position="bottom">
            <a href="{{ url('wa.me/' . $wa) }}" aria-label="Buka WhatsApp" class="ui-press grid size-11 place-items-center rounded-xl text-moss-700 shadow-hairline hover:bg-moss-50">
                <img src="/whatsapp.svg" alt="" width="22" height="22">
            </a>
        </x-ui.tooltip>
    @endsection
@endif

@section('content')
<div class="mx-auto max-w-2xl pb-24">
    <div id="chat-messages" class="flex flex-col gap-2" aria-live="polite">
        {{-- Pesan dari Firebase dirender di sini --}}
    </div>
</div>

{{-- Composer: menempel di bawah layar --}}
<div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 pb-[env(safe-area-inset-bottom)] backdrop-blur md:left-[4.5rem]">
    <form id="message-form" action="#" method="POST" enctype="multipart/form-data" class="mx-auto flex max-w-2xl items-center gap-2 p-3">
        @csrf
        <input id="image-input" type="file" name="image" accept="image/*" class="hidden">
        <x-ui.button id="image-btn" variant="secondary" class="!px-0 size-11 shrink-0" aria-label="Kirim gambar"><x-ui.icon name="paperclip" class="size-5" /></x-ui.button>
        <input id="message-input" type="text" name="message" placeholder="Ketik pesan…" autocomplete="off"
               class="block h-11 min-w-0 flex-1 rounded-xl border-0 bg-surface px-4 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line transition placeholder:text-ink-400 hover:ring-line-strong focus:ring-2 focus:ring-brand-600">
        <x-ui.button id="send-btn" type="submit" class="!px-0 size-11 shrink-0" aria-label="Kirim pesan"><x-ui.icon name="send" class="size-5" /></x-ui.button>
    </form>
</div>
@endsection

@push('js')
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script>
(function () {
    var custId = @json((string) $custId);
    var courId = @json((string) $courId);
    var box = document.getElementById('chat-messages');
    var form = document.getElementById('message-form');
    var textInput = document.getElementById('message-input');
    var fileInput = document.getElementById('image-input');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var uploadUrl = '/courier/uploadChatImageCour';
    var notifyUrl = @json(route('send.notification'));
    var IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'heic', 'webp'];
    var lastDateKey = null;
    var preview = !!window.dkChatSource || @json(isset($previewMessages));
    var database = null;
    if (!preview) { try { database = firebase.database(); } catch (e) { console.warn('Firebase belum siap:', e); } }

    function isImageFile(name) { return IMAGE_EXT.indexOf(String(name).split('.').pop().toLowerCase()) !== -1; }
    function parseTs(value) {
        if (typeof value === 'number') return new Date(value);
        var d = new Date(String(value).replace(' ', 'T'));
        return isNaN(d.getTime()) ? new Date() : d;
    }
    function pad(n) { return String(n).padStart(2, '0'); }
    function dateLabel(d) {
        var today = new Date(), yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);
        if (d.toDateString() === today.toDateString()) return 'Hari ini';
        if (d.toDateString() === yesterday.toDateString()) return 'Kemarin';
        return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function displayMessage(role, message, timestamp) {
        var d = parseTs(timestamp);
        var key = d.toDateString();
        if (key !== lastDateKey) {
            var label = document.createElement('div');
            label.className = 'date-label my-2 text-center';
            label.innerHTML = '<span class="inline-block rounded-full bg-sunken px-3 py-1 font-mono text-xs font-semibold text-ink-600"></span>';
            label.firstChild.textContent = dateLabel(d);
            box.appendChild(label);
            lastDateKey = key;
        }
        var mine = role === 'driver';
        var row = document.createElement('div');
        row.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
        var bubble = document.createElement('div');
        bubble.className = 'max-w-[80%] rounded-2xl px-3.5 py-2.5 text-[15px] leading-snug ' +
            (mine ? 'rounded-br-md bg-brand-700 text-white' : 'rounded-bl-md bg-surface text-ink shadow-card');
        if (isImageFile(message)) {
            var img = document.createElement('img');
            img.src = /^(https?:)?\//.test(message) ? message : '/storage/chatsImg/' + message;
            img.alt = 'Gambar dikirim';
            img.loading = 'lazy';
            img.className = 'max-h-80 w-full max-w-[16rem] rounded-xl object-contain bg-surface';
            bubble.appendChild(img);
        } else {
            var p = document.createElement('p');
            p.className = 'whitespace-pre-wrap break-words';
            p.textContent = message;
            bubble.appendChild(p);
        }
        var time = document.createElement('p');
        time.className = 'mt-1 text-right font-mono text-[11px] ' + (mine ? 'text-white' : 'text-ink-500');
        time.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes());
        bubble.appendChild(time);
        row.appendChild(bubble);
        box.appendChild(row);
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        row.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'end' });
    }

    function sendNotification(userId, title, body) {
        var data = new URLSearchParams({ user_id: userId, title: title, body: body, _token: csrf });
        fetch(notifyUrl, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .catch(function (error) { console.error('Error sending notification:', error); });
    }

    function sendMessage(message) {
        var now = new Date();
        var stamp = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        if (preview || !database) { displayMessage('driver', message, stamp); return; }
        database.ref('chats/' + custId + '-' + courId).push().set({ msgs: { role: 'driver', msg: message, timestamp: stamp } })
            .then(function () { sendNotification(custId, 'New Message', message); });
        database.ref('chats/' + custId + '-' + courId + '/courNewMssg').transaction(function (current) { return (current || 0) + 1; });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var message = textInput.value.trim();
        var file = fileInput.files[0];
        if (file) {
            var body = new FormData();
            body.append('image', file);
            body.append('_token', csrf);
            fetch(uploadUrl, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { return response.json(); })
                .then(function (json) {
                    if (json.success) sendMessage(json.imageUrl.split('/').pop());
                    else window.DKToast && DKToast.error('Gambar gagal diunggah');
                })
                .catch(function () { window.DKToast && DKToast.error('Gambar gagal diunggah'); });
        } else if (message !== '') {
            sendMessage(message);
        }
        textInput.value = '';
        fileInput.value = '';
    });
    document.getElementById('image-btn').addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () { form.requestSubmit(); });

    @isset($previewMessages)
        @json($previewMessages).forEach(function (m) { displayMessage(m.role, m.msg, m.timestamp); });
    @endisset
    if (database) {
        database.ref('chats/' + custId + '-' + courId).on('child_added', function (snapshot) {
            var data = snapshot.val();
            if (data && data.msgs) displayMessage(data.msgs.role, data.msgs.msg, data.msgs.timestamp);
        });
    }
})();
</script>
@endpush
