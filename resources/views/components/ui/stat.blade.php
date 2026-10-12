@props(['label', 'value', 'meta' => null, 'icon' => null])
<div {{ $attributes->class('rounded-2xl bg-surface p-4 shadow-card') }}>
    <div class="flex items-center justify-between text-sm font-semibold text-ink-500">
        <span>{{ $label }}</span>
        @if ($icon)<x-ui.icon :name="$icon" class="size-[18px] text-ink-400" />@endif
    </div>
    <p class="tnum mt-2 font-display text-4xl font-bold tracking-tightest text-ink">{{ $value }}</p>
    @if ($meta)<p class="mt-1 font-mono text-xs text-ink-500">{{ $meta }}</p>@endif
</div>
