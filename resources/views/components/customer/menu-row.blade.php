{{-- Baris menu di halaman warung (Figma RESTO): foto, nama, deskripsi, harga (coret bila diskon), rating, tombol Beli --}}
@props(['menu'])
@php
    $diskon = (float) ($menu->diskon ?? 0);
    $akhir = $diskon > 0 ? (int) round($menu->harga * (1 - $diskon / 100)) : (int) $menu->harga;
    $rp = fn ($n) => number_format($n, 0, ',', '.');
@endphp
<article {{ $attributes->class('relative flex gap-3 rounded-2xl bg-surface p-3 shadow-card') }}>
    <div class="relative shrink-0">
        <x-ui.photo :src="Storage::url($menu->image)" :alt="$menu->nama_makanan" class="size-24 rounded-xl md:size-28" />
        @if ($diskon > 0)<span class="absolute left-1.5 top-1.5 rounded-md bg-brand-700 px-1.5 py-0.5 font-mono text-[11px] font-bold text-white">-{{ rtrim(rtrim(number_format($diskon, 1), '0'), '.') }}%</span>@endif
    </div>
    <div class="flex min-w-0 flex-1 flex-col">
        <div class="flex items-start justify-between gap-2">
            <h3 class="line-clamp-1 font-display text-base font-bold leading-tight tracking-tight text-ink">{{ $menu->nama_makanan }}</h3>
            <x-customer.favorite-button :menu="$menu" class="-mr-1 -mt-1 !size-8 shrink-0 !shadow-none" />
        </div>
        @if ($menu->deskripsi)<p class="mt-0.5 line-clamp-2 text-[13px] leading-snug text-ink-500">{{ $menu->deskripsi }}</p>@endif
        <div class="mt-auto flex items-end justify-between gap-2 pt-2">
            <div>
                <p class="tnum font-mono text-sm font-semibold text-ink">{{ $rp($akhir) }}</p>
                @if ($diskon > 0)<p class="tnum font-mono text-[11px] text-ink-500 line-through">{{ $rp($menu->harga) }}</p>@endif
            </div>
            <div class="flex items-center gap-2.5">
                @if ($menu->rating)<x-ui.rating :value="$menu->rating" />@endif
                <x-ui.button :href="url('/detail-makanan/'.rawurlencode($menu->nama_makanan))" size="sm">Beli</x-ui.button>
            </div>
        </div>
    </div>
</article>
