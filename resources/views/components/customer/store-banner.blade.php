{{-- Kepala ringkasan pesanan: ikon toko + nama UMKM --}}
@props(['name'])
<div {{ $attributes->class('flex items-center gap-3') }}>
    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800"><x-ui.icon name="store" class="size-5" /></span>
    <h2 class="min-w-0 truncate font-display text-xl font-bold tracking-tight text-ink">{{ $name }}</h2>
</div>
