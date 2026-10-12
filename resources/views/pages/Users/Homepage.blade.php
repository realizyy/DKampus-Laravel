@extends('layouts.customer')

@section('content')
@php
    $promos = collect($PromoTerlaris ?? [])->take(3)->values();
    $maxDiskon = collect($PromoTerlaris ?? [])->max('DiscountPercent');
    $bulan = now()->translatedFormat('F');
@endphp

<div class="space-y-10 md:space-y-14">
    {{-- Hero promo + Jastip (bento asimetris) --}}
    <section class="grid gap-4 md:grid-cols-12" aria-label="Promo">
        <div class="relative isolate overflow-hidden rounded-3xl bg-brand-700 p-6 text-white md:col-span-8 md:p-10" x-motion.rise>
            <img src="{{ asset('logoDkampus.svg') }}" alt="" class="pointer-events-none absolute -bottom-16 -right-10 -z-10 w-72 max-w-none opacity-[0.12] brightness-0 invert">
            <div class="max-w-md">
                <p class="font-mono text-xs uppercase tracking-widest text-white">Promo · {{ now()->translatedFormat('F Y') }}</p>
                <h1 class="mt-2 font-display text-4xl font-extrabold leading-[0.98] tracking-tightest md:text-6xl">Nikmati Promo di Bulan {{ $bulan }}!</h1>
                <p class="mt-3 text-[17px] leading-snug text-white">{{ $maxDiskon ? 'Diskon sampai '.rtrim(rtrim(number_format($maxDiskon, 1), '0'), '.').'% dari warung sekitar kampus.' : 'Cek warung favoritmu, ada menu baru setiap hari.' }}</p>
                <x-ui.button :href="url('/promo')" variant="secondary" icon-right="arrow-right" class="mt-5">Lihat Semua</x-ui.button>
            </div>
            @if ($promos->isNotEmpty())
                <div class="pointer-events-none absolute -bottom-6 right-6 hidden h-64 w-[24rem] md:block" aria-hidden="true">
                    @foreach ($promos as $i => $promo)
                        <x-ui.photo :src="Storage::url($promo['Img'])" class="absolute bottom-0 h-56 w-40 rounded-2xl shadow-pop ring-4 ring-brand-700"
                                    style="right: {{ $i * 6.5 }}rem; transform: rotate({{ [-7, 3, -2][$i] }}deg) translateY({{ [0, -1.5, 1][$i] }}rem); z-index: {{ 10 - $i }}" />
                    @endforeach
                </div>
            @endif
        </div>

        <a href="{{ url('/jastip') }}" class="ui-press group relative flex flex-col justify-between overflow-hidden rounded-3xl bg-brand-100 p-5 shadow-card hover:shadow-pop md:col-span-4" x-motion.rise="{ delay: 0.06 }">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brand-800">Jastip</p>
                <h2 class="mt-1 font-display text-2xl font-extrabold leading-tight tracking-tightest text-ink">Titip beli apa saja, kurir yang antar.</h2>
            </div>
            <img src="{{ asset('bannerJastip.png') }}" alt="" class="mt-4 h-28 w-full rounded-2xl object-cover shadow-hairline" loading="lazy">
            <span class="mt-4 inline-flex items-center gap-1 font-semibold text-brand-800 group-hover:underline">Coba Jastip <x-ui.icon name="arrow-right" class="size-4" /></span>
        </a>
    </section>

    {{-- Kategori --}}
    <section aria-label="Kategori">
        <div class="-mx-4 flex snap-x gap-2 overflow-x-auto px-4 pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-7 md:gap-x-4 md:gap-y-5 md:overflow-visible md:px-0 lg:grid-cols-9 xl:grid-cols-[repeat(13,minmax(0,1fr))]">
            @foreach ($Carousel as $item)
                <x-customer.category-tile :title="$item['Title']" :icon="$item['Icon']" />
            @endforeach
        </div>
    </section>

    {{-- Resto terdekat --}}
    <section class="space-y-4" aria-label="Resto terdekat">
        <x-customer.section-head title="Resto Terdekat" href="{{ url('/rekomendasi-warung') }}" />
        @if ($RekomendasiWarung->isEmpty())
            <x-ui.card class="!p-0"><x-ui.empty-state icon="store" title="Belum ada warung" description="Warung sekitar kampus akan tampil di sini." /></x-ui.card>
        @else
            <div class="-mx-4 flex snap-x gap-3 overflow-x-auto px-4 pb-2 [scrollbar-width:none] md:mx-0 md:grid md:grid-cols-3 md:gap-4 md:overflow-visible md:px-0 lg:grid-cols-4">
                @foreach ($RekomendasiWarung->take(8) as $i => $umkm)
                    <x-customer.umkm-card :umkm="$umkm" class="w-64 shrink-0 snap-start md:w-auto" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Rekomendasi makanan --}}
    <section class="space-y-4" aria-label="Rekomendasi makanan">
        <x-customer.section-head title="Rekomendasi Makanan" href="{{ url('/rekomendasi-menu') }}" />
        @if ($RekomendasiMakanan->isEmpty())
            <x-ui.card class="!p-0"><x-ui.empty-state icon="utensils" title="Belum ada menu" description="Menu rekomendasi akan tampil di sini." /></x-ui.card>
        @else
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5">
                @foreach ($RekomendasiMakanan as $menu)
                    <x-customer.food-card :menu="$menu" />
                @endforeach
            </div>
        @endif
    </section>
</div>

@auth
    <x-customer.help-fab />
@endauth
@endsection
