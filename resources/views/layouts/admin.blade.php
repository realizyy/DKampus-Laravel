@extends('layouts.base')

@php
    // Navigasi admin. Desktop = sidebar 240px; mobile (Figma) = bottom-nav Home, Akun, Resto, Transaksi, Profil.
    // $navActive memungkinkan pratinjau lokal (/_ui/admin/*) menandai menu tanpa route aslinya.
    $is = fn (string ...$names) => isset($navActive) ? in_array($navActive, $names, true) : request()->routeIs(...$names);
    $adminUser = auth()->user();
    $sidebar = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'layout-dashboard', 'active' => $is('dashboard')],
        ['label' => 'Akun', 'href' => route('account'), 'icon' => 'users', 'active' => $is('account')],
        ['label' => 'Resto', 'href' => route('umkm'), 'icon' => 'store', 'active' => $is('umkm', 'umkmEdit', 'umkm.create')],
        ['label' => 'Produk', 'href' => route('product'), 'icon' => 'utensils', 'active' => $is('product', 'product.edit')],
        ['label' => 'Transaksi', 'href' => route('transaction'), 'icon' => 'receipt', 'active' => $is('transaction')],
        ['label' => 'Chat', 'href' => route('chatpage.admin'), 'icon' => 'message-circle', 'active' => $is('chatpage.admin')],
    ];
    $bottom = [
        ['label' => 'Home', 'href' => route('dashboard'), 'icon' => 'house', 'active' => $is('dashboard', 'chatpage.admin', 'product', 'product.edit')],
        ['label' => 'Akun', 'href' => route('account'), 'icon' => 'users', 'active' => $is('account')],
        ['label' => 'Resto', 'href' => route('umkm'), 'icon' => 'store', 'active' => $is('umkm', 'umkmEdit', 'umkm.create')],
        ['label' => 'Transaksi', 'href' => route('transaction'), 'icon' => 'receipt', 'active' => $is('transaction')],
        ['label' => 'Profil', 'href' => route('profile.edit'), 'icon' => 'user', 'active' => $is('profile.edit')],
    ];
    $palette = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'hint' => 'ringkasan', 'icon' => 'layout-dashboard'],
        ['label' => 'Kelola Akun', 'href' => route('account'), 'hint' => 'customer, driver, admin'],
        ['label' => 'Kelola Resto', 'href' => route('umkm'), 'hint' => 'daftar resto'],
        ['label' => 'Tambah Resto', 'href' => route('umkm.create'), 'hint' => 'resto baru'],
        ['label' => 'Produk', 'href' => route('product'), 'hint' => 'semua menu'],
        ['label' => 'Transaksi', 'href' => route('transaction'), 'hint' => 'riwayat order'],
        ['label' => 'Chat Customer Service', 'href' => route('chatpage.admin'), 'hint' => 'pesan masuk'],
        ['label' => 'Profil Admin', 'href' => route('profile.edit'), 'hint' => 'akun saya'],
    ];
    $pageTitle = trim($__env->yieldContent('page-title')) ?: ($Title ?? 'Admin');
    $hideNav = $__env->hasSection('hide-nav');
@endphp

@section('title', $pageTitle.' · Admin')

@section('shell')
<div class="min-h-dvh md:pl-60">
    {{-- Sidebar desktop --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-60 flex-col border-r border-line bg-surface/90 backdrop-blur md:flex">
        <a href="{{ route('dashboard') }}" class="ui-press mx-3 mt-4 flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-sunken" aria-label="DKampus Admin">
            <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-8 w-auto">
            <span class="font-display text-lg font-extrabold leading-none tracking-tightest text-ink">DKampus</span>
            <span class="rounded-md bg-ink px-1.5 py-0.5 font-mono text-[10px] font-medium uppercase tracking-wider text-canvas">Admin</span>
        </a>

        {{-- Membuka palet lewat pintasan yang sama (Ctrl+K) supaya satu jalur kode --}}
        <button type="button" onclick="window.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', ctrlKey: true }))"
                class="ui-press mx-3 mt-4 flex h-10 items-center justify-between rounded-xl bg-sunken px-3 text-sm text-ink-500 shadow-inset hover:text-ink">
            <span class="flex items-center gap-2"><x-ui.icon name="search" class="size-4" />Cari halaman</span>
            <span class="flex gap-1"><x-ui.kbd x-data x-text="/Mac|iPhone|iPad/.test(navigator.platform) ? '⌘' : 'Ctrl'">⌘</x-ui.kbd><x-ui.kbd>K</x-ui.kbd></span>
        </button>

        <nav aria-label="Navigasi admin" class="mt-5 flex-1 space-y-0.5 overflow-y-auto px-3">
            @foreach ($sidebar as $item)
                <a href="{{ $item['href'] }}" @if ($item['active']) aria-current="page" @endif
                   class="ui-press relative flex h-10 items-center gap-3 rounded-xl px-3 text-[15px] font-semibold {{ $item['active'] ? 'bg-brand-100 text-brand-800 shadow-hairline' : 'text-ink-600 hover:bg-sunken hover:text-ink' }}">
                    @if ($item['active'])<span class="absolute -left-3 top-2 h-6 w-1 rounded-r-full bg-brand-500" aria-hidden="true"></span>@endif
                    <x-ui.icon :name="$item['icon']" class="size-[18px]" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="m-3 rounded-2xl bg-sunken p-2 shadow-inset">
            <a href="{{ route('profile.edit') }}" class="ui-press flex items-center gap-3 rounded-xl p-2 hover:bg-surface">
                <x-ui.avatar :name="$adminUser->nama_user ?? ''" size="md" />
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-bold text-ink">{{ $adminUser->nama_user }}</span>
                    <span class="block truncate font-mono text-[11px] text-ink-500">{{ $adminUser->email }}</span>
                </span>
            </a>
            <button type="button" data-modal-open="logout" class="ui-press mt-1 flex h-9 w-full items-center gap-2 rounded-lg px-3 text-sm font-semibold text-ink-600 hover:bg-brick-50 hover:text-brick-700">
                <x-ui.icon name="log-out" class="size-4" />Keluar
            </button>
        </div>
    </aside>

    <div class="mx-auto flex min-h-dvh max-w-7xl flex-col">
        <header class="sticky top-0 z-20 border-b border-line/70 bg-canvas/85 px-4 pt-[env(safe-area-inset-top)] backdrop-blur md:static md:border-0 md:bg-transparent md:px-8 md:pt-9 md:backdrop-blur-none">
            {{-- Mobile: judul + kembali + aksi --}}
            <div class="flex h-14 items-center justify-between gap-3 md:hidden">
                @hasSection('back')
                    <a href="@yield('back')" class="ui-press -ml-2 flex min-w-0 items-center gap-1 rounded-lg pr-3 text-ink hover:bg-sunken">
                        <span class="grid size-10 place-items-center text-brand-700"><x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" /></span>
                        <h1 class="truncate font-display text-lg font-bold tracking-tight">{{ $pageTitle }}</h1>
                    </a>
                @else
                    <div class="flex min-w-0 items-center gap-2.5">
                        <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-7 w-auto">
                        <h1 class="truncate font-display text-lg font-extrabold tracking-tight text-ink">{{ $pageTitle }}</h1>
                    </div>
                @endif
                <div class="flex shrink-0 items-center gap-1">@yield('header-actions')</div>
            </div>
            {{-- Desktop: judul besar + aksi --}}
            <x-ui.page-header :title="$pageTitle" :subtitle="trim($__env->yieldContent('subtitle')) ?: null" class="hidden md:flex">@yield('header-actions')</x-ui.page-header>
        </header>

        <main class="flex-1 px-4 pt-4 md:px-8 md:pb-14 md:pt-6 {{ $hideNav ? 'pb-4' : 'pb-28' }}">
            @yield('content')
        </main>
    </div>
</div>

@unless ($hideNav)
    <x-ui.bottom-nav :items="$bottom" class="md:hidden" />
@endunless

<x-ui.palette :items="$palette" />
<x-ui.confirm name="logout" icon="log-out" title="Konfirmasi Logout" description="Apakah Anda yakin ingin keluar dari aplikasi?" :action="route('logout')" confirm="Logout" />
@endsection
