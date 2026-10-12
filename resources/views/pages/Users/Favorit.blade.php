@extends('layouts.customer')

@section('page-title', 'Favorit')

@section('content')
<div class="space-y-6" x-data="{ tab: 'warung' }">
    <x-ui.page-header title="Warung dan Makanan Favorit" subtitle="Simpan yang kamu suka dengan ikon hati." class="hidden md:flex" />
    <h1 class="font-display text-3xl font-extrabold tracking-tightest text-ink md:hidden">Warung dan Makanan Favorit</h1>

    @guest
        <x-ui.card class="!p-0"><x-ui.empty-state icon="heart" title="Masuk untuk melihat favorit" description="Favoritmu tersimpan di akun, jadi tersedia di semua perangkat.">
            <x-ui.button :href="route('login')">Masuk</x-ui.button>
        </x-ui.empty-state></x-ui.card>
    @else
        <div class="flex gap-2" role="tablist" aria-label="Jenis favorit">
            <button type="button" role="tab" :aria-selected="tab === 'warung'" @click="tab = 'warung'" class="ui-press inline-flex h-10 items-center gap-2 rounded-xl px-4 text-[15px] font-semibold shadow-hairline" :class="tab === 'warung' ? 'bg-ink text-canvas' : 'bg-surface text-ink-600 hover:bg-sunken'">Warung Favorit <span class="tnum font-mono text-xs opacity-80">{{ $FavoritWarung->count() }}</span></button>
            <button type="button" role="tab" :aria-selected="tab === 'makanan'" @click="tab = 'makanan'" class="ui-press inline-flex h-10 items-center gap-2 rounded-xl px-4 text-[15px] font-semibold shadow-hairline" :class="tab === 'makanan' ? 'bg-ink text-canvas' : 'bg-surface text-ink-600 hover:bg-sunken'">Makanan Favorit <span class="tnum font-mono text-xs opacity-80">{{ $FavoritMakanan->count() }}</span></button>
        </div>

        <section x-show="tab === 'warung'" role="tabpanel">
            @if ($FavoritWarung->isEmpty())
                <x-ui.card class="!p-0"><x-ui.empty-state icon="store" title="Belum ada warung favorit" description="Tandai makanan dengan hati, warungnya otomatis muncul di sini." /></x-ui.card>
            @else
                <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4">
                    @foreach ($FavoritWarung as $umkm)<x-customer.umkm-card :umkm="$umkm" />@endforeach
                </div>
            @endif
        </section>

        <section x-show="tab === 'makanan'" x-cloak role="tabpanel">
            @if ($FavoritMakanan->isEmpty())
                <x-ui.card class="!p-0"><x-ui.empty-state icon="heart" title="Belum ada makanan favorit" description="Ketuk ikon hati pada menu yang kamu suka." /></x-ui.card>
            @else
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5">
                    @foreach ($FavoritMakanan as $menu)<x-customer.food-card :menu="$menu" />@endforeach
                </div>
            @endif
        </section>

        @if ($FavoritMakanan->isEmpty())
            <section class="space-y-4 pt-4" aria-label="Rekomendasi">
                <x-customer.section-head title="Coba yang ini" href="{{ url('/rekomendasi-menu') }}" />
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
                    @foreach ($Rekomendasi as $menu)<x-customer.food-card :menu="$menu" />@endforeach
                </div>
            </section>
        @endif
    @endguest
</div>
@endsection
