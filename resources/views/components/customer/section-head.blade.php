@props(['title', 'href' => null, 'action' => 'Lihat Semua'])
<div {{ $attributes->class('flex items-end justify-between gap-3') }}>
    <h2 class="font-display text-2xl font-extrabold leading-tight tracking-tightest text-ink md:text-3xl">{{ $title }}</h2>
    @if ($href)
        <a href="{{ $href }}" class="ui-press -mb-0.5 inline-flex shrink-0 items-center gap-0.5 rounded-lg py-1 pl-2 text-[15px] font-semibold text-brand-700 hover:text-brand-800">
            {{ $action }}<x-ui.icon name="chevron-right" class="size-4" :stroke="2.5" />
        </a>
    @endif
</div>
