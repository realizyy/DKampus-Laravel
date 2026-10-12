@extends('layouts.base')

@php
    // Navigasi kurir (Figma: Home, Order, History, Profile). Aktif ditentukan dari nama route.
    // $navActive memungkinkan pratinjau lokal (/_ui/courier/*) menandai tab tanpa route aslinya.
    $current = $navActive ?? optional(request()->route())->getName();
    $nav = [
        ['label' => 'Home', 'href' => route('dashboardCourier'), 'icon' => 'house', 'active' => in_array($current, ['dashboardCourier', 'chatpage', 'room.chat.courier'], true)],
        ['label' => 'Order', 'href' => route('courierorder'), 'icon' => 'shopping-bag', 'active' => $current === 'courierorder'],
        ['label' => 'History', 'href' => route('cour.history'), 'icon' => 'clipboard-list', 'active' => in_array($current, ['cour.history', 'historydetail'], true)],
        ['label' => 'Profile', 'href' => route('profile'), 'icon' => 'user', 'active' => $current === 'profile'],
    ];
    $pageTitle = trim($__env->yieldContent('page-title')) ?: ($Title ?? 'Dsquad Freelancer');
    // Halaman imersif (mis. room chat) menyembunyikan bottom-nav lewat @section('hide-nav')
    $hideNav = $__env->hasSection('hide-nav');
@endphp

@section('shell')
<div class="min-h-dvh md:pl-[4.5rem]">
    {{-- Rail desktop --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-[4.5rem] flex-col items-center border-r border-line bg-surface/90 py-4 backdrop-blur md:flex">
        <a href="{{ route('dashboardCourier') }}" class="ui-press mb-5 grid size-11 place-items-center rounded-xl hover:bg-sunken" aria-label="DKampus Kurir">
            <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-7 w-auto">
        </a>
        <nav aria-label="Navigasi kurir" class="flex flex-1 flex-col items-center gap-1.5">
            @foreach ($nav as $item)
                <x-ui.tooltip :text="$item['label']" position="right">
                    <a href="{{ $item['href'] }}" aria-label="{{ $item['label'] }}" @if ($item['active']) aria-current="page" @endif
                       class="ui-press grid size-11 place-items-center rounded-xl {{ $item['active'] ? 'bg-brand-100 text-brand-800 shadow-hairline' : 'text-ink-500 hover:bg-sunken hover:text-ink' }}">
                        <x-ui.icon :name="$item['icon']" class="size-[22px]" />
                    </a>
                </x-ui.tooltip>
            @endforeach
        </nav>
        <x-ui.tooltip text="Keluar" position="right">
            <button type="button" data-modal-open="logout" aria-label="Keluar" class="ui-press grid size-11 place-items-center rounded-xl text-ink-500 hover:bg-brick-50 hover:text-brick-700">
                <x-ui.icon name="log-out" class="size-[22px]" />
            </button>
        </x-ui.tooltip>
    </aside>

    <div class="mx-auto flex min-h-dvh max-w-6xl flex-col">
        <header class="sticky top-0 z-20 border-b border-line/70 bg-canvas/85 px-4 pt-[env(safe-area-inset-top)] backdrop-blur md:static md:border-0 md:bg-transparent md:px-8 md:pt-9 md:backdrop-blur-none">
            {{-- Mobile: Figma — brand di Home, judul + tombol kembali di halaman dalam --}}
            <div class="flex h-14 items-center justify-between gap-3 md:hidden">
                @hasSection('back')
                    <a href="@yield('back')" class="ui-press -ml-2 flex min-w-0 items-center gap-1 rounded-lg pr-3 text-ink hover:bg-sunken">
                        <span class="grid size-10 place-items-center text-brand-700"><x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" /></span>
                        <h1 class="truncate font-display text-lg font-bold tracking-tight">{{ $pageTitle }}</h1>
                    </a>
                @else
                    <div class="flex min-w-0 items-center gap-2.5">
                        <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-7 w-auto">
                        <h1 class="truncate font-display text-lg font-extrabold tracking-tight text-brand-700">Dsquad Freelancer</h1>
                    </div>
                @endif
                <div class="flex shrink-0 items-center gap-1">@yield('header-actions')</div>
            </div>
            {{-- Desktop: judul besar + aksi --}}
            <x-ui.page-header :title="$pageTitle" class="hidden md:flex">@yield('header-actions')</x-ui.page-header>
        </header>

        <main class="flex-1 px-4 pt-4 md:px-8 md:pb-12 md:pt-6 {{ $hideNav ? 'pb-4' : 'pb-28' }}">
            @yield('content')
        </main>
    </div>
</div>

@unless ($hideNav)
    <x-ui.bottom-nav :items="$nav" class="md:hidden" />
@endunless

<x-ui.confirm name="logout" icon="log-out" title="Konfirmasi Logout" description="Apakah Anda yakin ingin keluar dari aplikasi?" :action="route('logout')" confirm="Logout" />

@include('layouts.partials.fcm')
@endsection
