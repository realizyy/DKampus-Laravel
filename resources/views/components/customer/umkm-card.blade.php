{{--
    Kartu warung/UMKM: foto, jam buka, nama, rating. Warung yang sedang tutup tampil abu-abu dengan badge "Tutup".
    $umkm: App\Models\Data_umkm. $jarak opsional (meter).
--}}
@props(['umkm', 'jarak' => null, 'promo' => false])
@php
    $buka = $umkm->open_time ? date('H:i', strtotime($umkm->open_time)) : null;
    $tutup = $umkm->close_time ? date('H:i', strtotime($umkm->close_time)) : null;
    $now = date('H:i');
    $isOpen = ! ($buka && $tutup) || ($buka <= $tutup ? ($now >= $buka && $now <= $tutup) : ($now >= $buka || $now <= $tutup));
    $dist = $jarak === null ? null : ($jarak >= 1000 ? rtrim(rtrim(number_format($jarak / 1000, 2, ',', ''), '0'), ',').' km' : (int) $jarak.' m');
@endphp
<a href="{{ url('/detail-warung/'.rawurlencode($umkm->nama_umkm)) }}" {{ $attributes->class('ui-press group block overflow-hidden rounded-2xl bg-surface shadow-card hover:-translate-y-px hover:shadow-pop') }}>
    <div class="relative">
        <x-ui.photo :src="Storage::url($umkm->logo_umkm)" :alt="$umkm->nama_umkm" icon="store" :grayscale="! $isOpen" class="aspect-[16/10] w-full" />
        <div class="absolute left-2 top-2 flex gap-1.5">
            @unless ($isOpen)<span class="rounded-md bg-ink px-1.5 py-0.5 text-[11px] font-bold text-canvas">Tutup</span>@endunless
            @if ($promo)<span class="rounded-md bg-brand-700 px-1.5 py-0.5 text-[11px] font-bold text-white">PROMO</span>@endif
        </div>
    </div>
    <div class="space-y-1 p-3">
        <p class="flex items-center gap-1 font-mono text-xs text-ink-500"><x-ui.icon name="clock" class="size-3.5" />@if ($buka && $tutup){{ $buka }} - {{ $tutup }}@else Jam belum diatur @endif</p>
        <h3 class="line-clamp-1 font-display text-base font-bold leading-tight tracking-tight text-ink">{{ $umkm->nama_umkm }}</h3>
        <div class="flex items-center gap-3">
            @if ($umkm->rating ?? null)<x-ui.rating :value="$umkm->rating" />@endif
            @if ($dist)<span class="inline-flex items-center gap-1 font-mono text-xs text-ink-500"><x-ui.icon name="navigation" class="size-3.5" />{{ $dist }}</span>@endif
        </div>
    </div>
</a>
