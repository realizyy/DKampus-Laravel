@extends('layouts.customer')

@php
    $keyword = trim((string) request('keyword', ''));
    $filter = request('filter', 'menu') === 'umkm' ? 'umkm' : 'menu';
    $count = $filter === 'umkm' ? $umkms->count() : $menus->count();
    $tab = fn ($f) => url('/search').'?'.http_build_query(['keyword' => $keyword, 'filter' => $f]);
@endphp

@section('page-title', 'Pencarian')
@section('back', url('/'))

@section('content')
<div class="space-y-5">
    <div>
        <p class="font-mono text-xs uppercase tracking-widest text-ink-500">Hasil pencarian</p>
        <h1 class="font-display text-3xl font-extrabold tracking-tightest text-ink md:text-4xl">{{ $keyword !== '' ? '“'.$keyword.'”' : 'Semua' }}</h1>
    </div>
    <div class="flex items-center gap-2" role="tablist" aria-label="Jenis hasil">
        <x-ui.chip :href="$tab('menu')" :active="$filter === 'menu'">Menu <span class="tnum font-mono text-xs opacity-80">{{ $menus->count() }}</span></x-ui.chip>
        <x-ui.chip :href="$tab('umkm')" :active="$filter === 'umkm'">Warung <span class="tnum font-mono text-xs opacity-80">{{ $umkms->count() }}</span></x-ui.chip>
    </div>

    @if ($count === 0)
        <x-ui.card class="!p-0"><x-ui.empty-state icon="search" title="Tidak ada hasil" description="Coba kata kunci lain atau jelajahi kategori di beranda.">
            <x-ui.button :href="url('/')" variant="secondary">Ke beranda</x-ui.button>
        </x-ui.empty-state></x-ui.card>
    @elseif ($filter === 'menu')
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5" x-motion.rise.stagger>
            @foreach ($menus as $menu)
                <x-customer.food-card :menu="$menu" :warung="$menu->nama_umkm ?? null" />
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4" x-motion.rise.stagger>
            @foreach ($umkms as $umkm)
                <x-customer.umkm-card :umkm="$umkm" />
            @endforeach
        </div>
    @endif
</div>
@endsection
