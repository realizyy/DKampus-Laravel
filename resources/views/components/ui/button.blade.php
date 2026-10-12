@props([
    'variant' => 'primary', // primary | secondary | soft | ghost | outline | danger
    'size' => 'md',         // sm | md | lg
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'iconRight' => null,
    'loading' => false,
    'block' => false,
])
@php
    $base = 'ui-press relative inline-flex select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl font-semibold tracking-[-0.005em]';
    $variants = [
        // Isi oranye tua (brand-700) supaya teks putih lolos kontras 4,5:1
        'primary' => 'bg-brand-700 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.16),0_0_0_1px_rgb(124_54_18/0.7)] hover:bg-brand-800 active:bg-brand-900',
        'secondary' => 'bg-surface text-ink shadow-card hover:bg-sunken active:bg-line',
        'soft' => 'bg-brand-100 text-brand-800 hover:bg-brand-200 active:bg-brand-300',
        'ghost' => 'text-ink-600 hover:bg-sunken hover:text-ink active:bg-line',
        'outline' => 'bg-transparent text-ink shadow-[0_0_0_1px_theme(colors.line.strong)] hover:bg-sunken active:bg-line',
        'danger' => 'bg-brick-600 text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.14),0_0_0_1px_rgb(143_50_32/0.7)] hover:bg-brick-700 active:bg-brick-700',
    ];
    $sizes = [
        'sm' => 'h-9 px-3 text-sm',
        'md' => 'h-11 px-4 text-[15px]',
        'lg' => 'h-12 px-5 text-base',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']).($block ? ' w-full' : '');
@endphp
@if ($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)<x-ui.icon :name="$icon" class="size-[18px]" />@endif
    {{ $slot }}
    @if ($iconRight)<x-ui.icon :name="$iconRight" class="size-[18px]" />@endif
</a>
@else
<button {{ $attributes->merge(['class' => $classes, 'type' => $type]) }} @disabled($loading) @if ($loading) aria-busy="true" @endif>
    @if ($loading)
        <span class="d-loading d-loading-spinner d-loading-sm" aria-hidden="true"></span>
    @elseif ($icon)
        <x-ui.icon :name="$icon" class="size-[18px]" />
    @endif
    {{ $slot }}
    @if ($iconRight && ! $loading)<x-ui.icon :name="$iconRight" class="size-[18px]" />@endif
</button>
@endif
