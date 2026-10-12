{{-- Ringkas: bintang + angka mono, seperti kartu Figma (★ 4.7) --}}
@props(['value' => 0])
<span {{ $attributes->class('inline-flex items-center gap-1 text-sm font-semibold text-ink-700') }}>
    <x-ui.icon name="star" class="size-3.5 fill-brand-500 text-brand-500" />
    <span class="tnum font-mono text-[13px]">{{ number_format((float) $value, 1) }}</span>
</span>
