{{--
    Kartu makanan (Figma 161×183: foto, nama, warung, harga, rating; badge diskon).
    $menu: App\Models\Menu. Tautan ke /detail-makanan/{nama}. $warung opsional (nama UMKM) bila relasi belum dimuat.
--}}
@props(['menu', 'warung' => null])
@php
    $diskon = (float) ($menu->diskon ?? 0);
    $harga = (int) $menu->harga;
    $akhir = $diskon > 0 ? (int) round($harga * (1 - $diskon / 100)) : $harga;
    $warung ??= optional($menu->data_umkm)->nama_umkm;
    $rp = fn ($n) => number_format($n, 0, ',', '.');
@endphp
<div {{ $attributes->class('ui-press group relative overflow-hidden rounded-2xl bg-surface shadow-card hover:-translate-y-px hover:shadow-pop') }}>
    <a href="{{ url('/detail-makanan/'.rawurlencode($menu->nama_makanan)) }}" class="absolute inset-0 z-10" aria-label="{{ $menu->nama_makanan }}"></a>
    <x-customer.favorite-button :menu="$menu" class="absolute right-2 top-2 z-20" />
    <div class="relative">
        <x-ui.photo :src="Storage::url($menu->image)" :alt="$menu->nama_makanan" class="aspect-[4/3] w-full" />
        @if ($diskon > 0)
            <span class="absolute left-2 top-2 rounded-md bg-brand-700 px-1.5 py-0.5 font-mono text-[11px] font-bold text-white shadow-hairline">-{{ rtrim(rtrim(number_format($diskon, 1), '0'), '.') }}%</span>
        @endif
    </div>
    <div class="space-y-1.5 p-3">
        <h3 class="line-clamp-1 font-display text-[15px] font-bold leading-tight tracking-tight text-ink">{{ $menu->nama_makanan }}</h3>
        @if ($warung)<p class="line-clamp-1 text-[13px] text-ink-500">{{ $warung }}</p>@endif
        <div class="flex items-end justify-between gap-2">
            <p class="tnum font-mono text-sm font-semibold text-ink">{{ $rp($akhir) }}@if ($diskon > 0)<span class="ml-1.5 text-[11px] font-normal text-ink-500 line-through">{{ $rp($harga) }}</span>@endif</p>
            @if ($menu->rating)<x-ui.rating :value="$menu->rating" />@endif
        </div>
    </div>
</div>
