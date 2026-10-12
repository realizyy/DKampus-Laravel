@extends('layouts.customer')

@php
    $top = $menus->first();
    $maxDiskon = $menus->max('diskon');
    $rp = fn ($n) => number_format($n, 0, ',', '.');
@endphp

@section('page-title', 'Promo')

@section('content')
<div class="space-y-10 md:space-y-12">
    {{-- Promo Spesial (Figma): banner + ringkasan --}}
    <section class="grid gap-4 md:grid-cols-12" aria-label="Promo spesial">
        <div class="relative isolate overflow-hidden rounded-3xl bg-brand-700 text-white md:col-span-8" x-motion.rise>
            @if ($top)
                <x-ui.photo :src="Storage::url($top->image)" :alt="$top->nama_makanan" class="absolute inset-y-0 right-0 -z-10 hidden w-1/2 md:block" />
                <div class="absolute inset-y-0 right-0 -z-10 hidden w-1/2 bg-gradient-to-r from-brand-700 to-transparent md:block" aria-hidden="true"></div>
            @endif
            <div class="flex min-h-56 max-w-md flex-col justify-end gap-2 p-6 md:min-h-72 md:p-10">
                <p class="font-mono text-xs uppercase tracking-widest text-white">Promo Spesial</p>
                <h1 class="font-display text-4xl font-extrabold leading-[0.98] tracking-tightest md:text-5xl">Terbaru! Ada Promo Nikmat! Gaskeun!</h1>
                @if ($top)<p class="text-[17px] text-white">{{ $top->nama_makanan }} · {{ optional($top->data_umkm)->nama_umkm }}</p>@endif
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 md:col-span-4 md:grid-cols-1" x-motion.rise.stagger>
            <x-ui.stat label="Menu diskon" :value="$menus->count()" meta="hari ini" icon="ticket-percent" />
            <x-ui.stat label="Diskon terbesar" :value="$maxDiskon ? rtrim(rtrim(number_format($maxDiskon, 1), '0'), '.').'%' : '-'" meta="dari warung kampus" icon="flame" />
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <nav class="-mx-4 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] md:mx-0 md:px-0" aria-label="Kategori promo">
            <x-ui.chip :href="url('/promo')" :active="! $activeCategory || strtolower($activeCategory) === 'semua'">Semua</x-ui.chip>
            @foreach ($categories as $cat)
                <x-ui.chip :href="url('/promo/category/'.rawurlencode(strtolower($cat)))" :active="strtolower((string) $activeCategory) === strtolower($cat)">{{ ucfirst($cat) }}</x-ui.chip>
            @endforeach
        </nav>
    @endif

    @if ($menus->isEmpty())
        <x-ui.card class="!p-0"><x-ui.empty-state icon="ticket-percent" title="Belum ada promo" description="Promo dari warung sekitar kampus akan muncul di sini."><x-ui.button :href="url('/')" variant="secondary">Ke beranda</x-ui.button></x-ui.empty-state></x-ui.card>
    @else
        <section class="space-y-4" aria-label="Promo untukmu">
            <x-customer.section-head title="Promo Untukmu" />
            <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4" x-motion.rise.stagger>
                @foreach ($umkms as $umkm)<x-customer.umkm-card :umkm="$umkm" :promo="true" />@endforeach
            </div>
        </section>

        <section class="space-y-4" aria-label="Menu diskon">
            <x-customer.section-head title="Menu Diskon" />
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5" x-motion.rise.stagger>
                @foreach ($menus as $menu)<x-customer.food-card :menu="$menu" />@endforeach
            </div>
        </section>
    @endif
</div>
@endsection
