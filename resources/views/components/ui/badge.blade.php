@props(['tone' => 'neutral', 'dot' => false])
@php
    $tones = [
        'neutral' => 'bg-sunken text-ink-600',
        'brand' => 'bg-brand-100 text-brand-800',
        'moss' => 'bg-moss-100 text-moss-700',
        'brick' => 'bg-brick-100 text-brick-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'teal' => 'bg-teal-100 text-teal-700',
    ];
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-xs font-semibold leading-5', $tones[$tone] ?? $tones['neutral']]) }}>
    @if ($dot)<span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>@endif
    {{ $slot }}
</span>
