@props(['title', 'icon'])
<a href="{{ url('/kategori/'.strtolower($title)) }}" {{ $attributes->class('ui-press group flex w-[4.75rem] shrink-0 snap-start flex-col items-center gap-1.5 text-center md:w-auto') }}>
    <span class="grid size-[4.25rem] place-items-center rounded-2xl bg-surface shadow-card group-hover:-translate-y-px group-hover:shadow-pop md:size-20 transition"><img src="{{ asset(ltrim($icon, '/')) }}" alt="" class="size-9 md:size-10" loading="lazy"></span>
    <span class="text-[13px] font-semibold leading-tight text-ink-700">{{ $title }}</span>
</a>
