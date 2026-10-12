@extends('layouts.admin')

@php
    $firstName = \Illuminate\Support\Str::of((string) auth()->user()->nama_user)->trim()->explode(' ')->first() ?: 'Admin';

    // Firebase mengembalikan null bila node kosong
    $waiting = (int) $dataNTD;
    $onTheRoad = (int) $dataOP;
    $recorded = count($transaction);
    $totalTrx = $recorded + $waiting + $onTheRoad;
    $pct = fn (int $n) => $totalTrx > 0 ? round($n / $totalTrx * 100, 1) : 0;

    $roles = collect($user)->groupBy('role')->map->count();
    $blocked = collect($user)->where('restriction', 1)->count();
    $vip = collect($data_umkm)->where('vip', 1)->count();
@endphp

@section('page-title', 'Halo, '.$firstName)
@section('subtitle', 'Ringkasan akun, resto, dan transaksi DKampus.')

@section('header-actions')
    <x-ui.button href="{{ route('umkm.create') }}" icon="plus" size="sm" class="hidden md:inline-flex">Tambah Resto</x-ui.button>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:gap-4" x-motion.stagger>
    {{-- Aksi utama (Figma: Kelola Akun, Kelola Resto) --}}
    <a href="{{ route('account') }}" class="ui-press group flex items-center justify-between rounded-2xl bg-ink p-5 text-canvas shadow-pop hover:-translate-y-px md:col-span-4">
        <span>
            <span class="block font-display text-xl font-bold tracking-tight">Kelola Akun</span>
            <span class="mt-0.5 block text-sm text-canvas/75">Aktifkan, nonaktifkan, atau ubah role</span>
        </span>
        <span class="grid size-11 place-items-center rounded-xl bg-canvas/10 transition group-hover:translate-x-0.5"><x-ui.icon name="arrow-right" class="size-5" /></span>
    </a>
    <a href="{{ route('umkm') }}" class="ui-press group flex items-center justify-between rounded-2xl bg-brand-700 p-5 text-white shadow-pop hover:-translate-y-px md:col-span-4">
        <span>
            <span class="block font-display text-xl font-bold tracking-tight">Kelola Resto</span>
            <span class="mt-0.5 block text-sm text-white">Resto, menu, dan lokasi</span>
        </span>
        <span class="grid size-11 place-items-center rounded-xl bg-white/15 transition group-hover:translate-x-0.5"><x-ui.icon name="arrow-right" class="size-5" /></span>
    </a>
    <a href="{{ route('chatpage.admin') }}" class="ui-press group flex items-center justify-between rounded-2xl bg-surface p-5 shadow-card hover:-translate-y-px hover:shadow-pop md:col-span-4">
        <span>
            <span class="block font-display text-xl font-bold tracking-tight text-ink">Chat Customer Service</span>
            <span class="mt-0.5 block text-sm text-ink-500">Balas pesan masuk dari customer</span>
        </span>
        <span class="grid size-11 place-items-center rounded-xl bg-sunken text-ink-600 transition group-hover:translate-x-0.5"><x-ui.icon name="message-circle" class="size-5" /></span>
    </a>

    {{-- Transaksi: tile terbesar --}}
    <a href="{{ route('transaction') }}" class="ui-press block rounded-2xl bg-surface p-5 shadow-card hover:shadow-pop md:col-span-7 md:row-span-2 md:p-6">
        <div class="flex items-center justify-between text-sm font-semibold text-ink-500">
            <span>Transaksi</span>
            <span class="inline-flex items-center gap-1 text-brand-700">Lihat semua <x-ui.icon name="arrow-up-right" class="size-4" /></span>
        </div>
        <p class="tnum mt-3 font-display text-6xl font-extrabold leading-none tracking-tightest text-ink md:text-7xl">{{ number_format($totalTrx, 0, ',', '.') }}</p>
        <p class="mt-2 text-[15px] text-ink-500">pesanan tercatat sejak awal</p>

        @if ($totalTrx > 0)
            <div class="mt-7 flex h-3 w-full overflow-hidden rounded-full bg-sunken shadow-inset" role="img" aria-label="Menunggu driver {{ $waiting }}, sedang diantar {{ $onTheRoad }}, riwayat {{ $recorded }}">
                <span class="bg-amber-700" style="width: {{ $pct($waiting) }}%"></span>
                <span class="bg-teal-700" style="width: {{ $pct($onTheRoad) }}%"></span>
                <span class="bg-moss-600" style="width: {{ $pct($recorded) }}%"></span>
            </div>
        @endif
        <dl class="mt-5 grid grid-cols-3 gap-3 text-sm">
            <div class="flex flex-col justify-between">
                <dt class="flex items-center gap-1.5 text-ink-500"><span class="size-2 rounded-full bg-amber-700"></span>Menunggu driver</dt>
                <dd class="tnum mt-1 font-display text-2xl font-bold tracking-tight text-ink">{{ $waiting }}</dd>
            </div>
            <div class="flex flex-col justify-between">
                <dt class="flex items-center gap-1.5 text-ink-500"><span class="size-2 rounded-full bg-teal-700"></span>Sedang diantar</dt>
                <dd class="tnum mt-1 font-display text-2xl font-bold tracking-tight text-ink">{{ $onTheRoad }}</dd>
            </div>
            <div class="flex flex-col justify-between">
                <dt class="flex items-center gap-1.5 text-ink-500"><span class="size-2 rounded-full bg-moss-600"></span>Riwayat</dt>
                <dd class="tnum mt-1 font-display text-2xl font-bold tracking-tight text-ink">{{ $recorded }}</dd>
            </div>
        </dl>
    </a>

    {{-- Akun --}}
    <a href="{{ route('account') }}" class="ui-press block rounded-2xl bg-surface p-5 shadow-card hover:shadow-pop md:col-span-5">
        <div class="flex items-center justify-between text-sm font-semibold text-ink-500">
            <span>Akun terdaftar</span><x-ui.icon name="users" class="size-[18px] text-ink-400" />
        </div>
        <p class="tnum mt-2 font-display text-4xl font-bold tracking-tightest text-ink">{{ count($user) }}</p>
        <p class="mt-2 flex flex-wrap gap-x-3 gap-y-1 font-mono text-xs text-ink-500">
            <span>{{ $roles->get('customer', 0) }} customer</span>
            <span>{{ $roles->get('courier', 0) }} driver</span>
            <span>{{ $roles->get('admin', 0) }} admin</span>
            @if ($blocked > 0)<span class="font-semibold text-brick-700">{{ $blocked }} non aktif</span>@endif
        </p>
    </a>

    {{-- Resto + produk --}}
    <div class="grid grid-cols-2 gap-3 md:col-span-5 md:gap-4">
        <a href="{{ route('umkm') }}" class="ui-press block rounded-2xl bg-surface p-5 shadow-card hover:shadow-pop">
            <div class="flex items-center justify-between text-sm font-semibold text-ink-500"><span>Resto</span><x-ui.icon name="store" class="size-[18px] text-ink-400" /></div>
            <p class="tnum mt-2 font-display text-4xl font-bold tracking-tightest text-ink">{{ count($data_umkm) }}</p>
            <p class="mt-1 font-mono text-xs text-ink-500">{{ $vip }} VIP</p>
        </a>
        <a href="{{ route('product') }}" class="ui-press block rounded-2xl bg-surface p-5 shadow-card hover:shadow-pop">
            <div class="flex items-center justify-between text-sm font-semibold text-ink-500"><span>Produk</span><x-ui.icon name="utensils" class="size-[18px] text-ink-400" /></div>
            <p class="tnum mt-2 font-display text-4xl font-bold tracking-tightest text-ink">{{ count($menu) }}</p>
            <p class="mt-1 font-mono text-xs text-ink-500">dari semua resto</p>
        </a>
    </div>
</div>
@endsection
