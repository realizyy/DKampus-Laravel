@extends('layouts.base')

{{--
    Layout customer (Figma NEWEST CUSTOMER).
    Mobile: header (brand + pencarian + chat + akun, atau kembali + judul) dan bottom-nav 4 tab dengan bump.
    Desktop: topbar sticky (logo, tab, pencarian, aksi) dan konten lebar maksimal 7xl.
    Section: content (wajib), back (URL; mengaktifkan header kembali di mobile), page-title, header-actions, hide-nav.
    $Title menentukan tab aktif: Home, *Promo*, Pesanan/Status, Favorit.
--}}
@php
    $t = (string) ($Title ?? '');
    $section = match (true) {
        $t === 'Home' => 'home',
        str_contains($t, 'Promo') => 'promo',
        in_array($t, ['Pesanan', 'Status'], true) => 'pesanan',
        $t === 'Favorit' => 'favorit',
        default => null,
    };
    $nav = [
        ['label' => 'Home', 'href' => url('/'), 'icon' => 'house', 'active' => $section === 'home'],
        ['label' => 'Promo', 'href' => url('/promo'), 'icon' => 'ticket-percent', 'active' => $section === 'promo'],
        ['label' => 'Pesanan', 'href' => url('/pesanan'), 'icon' => 'shopping-bag', 'active' => $section === 'pesanan'],
        ['label' => 'Favorit', 'href' => url('/favorit'), 'icon' => 'heart', 'active' => $section === 'favorit'],
    ];
    $showNav = $section !== null && ! $__env->hasSection('hide-nav');
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('page-title')) ?: $t;
@endphp

@section('shell')
<div x-data="{ account: false }" @keydown.escape.window="account = false">
    {{-- Topbar desktop --}}
    <header class="sticky top-0 z-30 hidden border-b border-line bg-canvas/85 backdrop-blur md:block">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-8 px-8">
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2" aria-label="Dkampus">
                <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-8 w-auto">
                <span class="font-display text-xl font-extrabold tracking-tight text-ink">Dkampus</span>
            </a>
            <nav aria-label="Navigasi utama" class="flex h-full items-stretch gap-1">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}" @if ($item['active']) aria-current="page" @endif
                       class="ui-press relative flex items-center gap-2 px-3 text-[15px] font-semibold {{ $item['active'] ? 'text-ink' : 'text-ink-500 hover:text-ink' }}">
                        <x-ui.icon :name="$item['icon']" class="size-[18px] {{ $item['active'] ? 'text-brand-600' : '' }}" />
                        {{ $item['label'] }}
                        @if ($item['active'])<span class="absolute inset-x-2 bottom-0 h-0.5 rounded-t-full bg-brand-500"></span>@endif
                    </a>
                @endforeach
            </nav>
            <form action="{{ url('/search') }}" method="GET" role="search" class="mx-auto w-full max-w-md" x-data @keydown.window="if ($event.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) { $event.preventDefault(); $refs.q.focus() }">
                <label class="group relative block">
                    <span class="sr-only">Cari menu</span>
                    <x-ui.icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-[18px] -translate-y-1/2 text-ink-400 group-focus-within:text-brand-700" />
                    <input x-ref="q" name="keyword" type="search" value="{{ request('keyword') }}" placeholder="Cari menu" autocomplete="off"
                           class="block h-11 w-full rounded-xl border-0 bg-surface pl-11 pr-10 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line transition placeholder:text-ink-400 hover:ring-line-strong focus:ring-2 focus:ring-brand-600">
                    <x-ui.kbd class="absolute right-3 top-1/2 -translate-y-1/2">/</x-ui.kbd>
                </label>
            </form>
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <x-ui.tooltip text="Chat" position="bottom"><a href="{{ url('/chats') }}" aria-label="Chat" class="ui-press grid size-11 place-items-center rounded-xl text-ink-600 hover:bg-sunken hover:text-ink"><x-ui.icon name="message-circle" class="size-[22px]" /></a></x-ui.tooltip>
                    <button type="button" @click="account = true" aria-label="Menu akun" class="ui-press rounded-full ring-2 ring-transparent hover:ring-brand-200"><x-ui.avatar :name="$user->nama_user" /></button>
                @else
                    <x-ui.button :href="route('login')" size="sm">Masuk</x-ui.button>
                    <x-ui.button :href="route('register')" size="sm" variant="outline">Daftar</x-ui.button>
                @endauth
            </div>
        </div>
    </header>

    {{-- Header mobile --}}
    <header class="sticky top-0 z-30 border-b border-line/70 bg-canvas/90 px-4 pt-[env(safe-area-inset-top)] backdrop-blur md:hidden">
        @hasSection('back')
            <div class="flex h-14 items-center justify-between gap-2">
                <a href="@yield('back')" class="ui-press -ml-2 flex min-w-0 items-center gap-1 rounded-lg pr-3 text-ink hover:bg-sunken">
                    <span class="grid size-10 place-items-center text-brand-700"><x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" /></span>
                    <h1 class="truncate font-display text-lg font-bold tracking-tight">{{ $pageTitle }}</h1>
                </a>
                <div class="flex shrink-0 items-center gap-1">@yield('header-actions')</div>
            </div>
        @else
            <div class="flex h-16 items-center gap-2.5">
                <a href="{{ url('/') }}" class="shrink-0" aria-label="Dkampus"><img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-8 w-auto"></a>
                <form action="{{ url('/search') }}" method="GET" role="search" class="min-w-0 flex-1">
                    <label class="group relative block">
                        <span class="sr-only">Cari menu</span>
                        <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-[18px] -translate-y-1/2 text-ink-400 group-focus-within:text-brand-700" />
                        <input name="keyword" type="search" value="{{ request('keyword') }}" placeholder="Cari menu" autocomplete="off"
                               class="block h-11 w-full rounded-xl border-0 bg-surface pl-10 pr-3 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line transition placeholder:text-ink-400 focus:ring-2 focus:ring-brand-600">
                    </label>
                </form>
                @auth
                    <a href="{{ url('/chats') }}" aria-label="Chat" class="ui-press grid size-11 shrink-0 place-items-center rounded-xl text-brand-700 shadow-hairline hover:bg-brand-50"><x-ui.icon name="message-circle" class="size-[22px]" /></a>
                    <button type="button" @click="account = true" aria-label="Menu akun" class="ui-press shrink-0 rounded-full"><x-ui.avatar :name="$user->nama_user" /></button>
                @else
                    <x-ui.button :href="route('login')" size="sm" class="shrink-0">Masuk</x-ui.button>
                @endauth
            </div>
        @endif
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 pt-4 md:px-8 md:pt-8 {{ $showNav ? 'pb-28 md:pb-16' : 'pb-10' }}">
        @yield('content')
    </main>

    @auth
        <x-customer.account-sheet />
    @endauth
</div>

@if ($showNav)
    <x-ui.bottom-nav :items="$nav" class="md:hidden" />
@endif

@auth
    <x-ui.confirm name="logout" icon="log-out" title="Konfirmasi Logout" description="Apakah Anda yakin ingin keluar dari aplikasi?" :action="route('logout')" confirm="Logout" />
    @include('layouts.partials.fcm')
@endauth
@endsection
