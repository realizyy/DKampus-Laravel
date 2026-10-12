@props(['active' => false, 'href' => null])
@php
    $classes = 'ui-press inline-flex h-9 shrink-0 items-center gap-1.5 rounded-lg px-3 text-sm font-semibold '
        .($active ? 'bg-ink text-canvas shadow-hairline' : 'bg-surface text-ink-600 shadow-hairline hover:bg-sunken hover:text-ink');
@endphp
@if ($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="true" @endif>{{ $slot }}</a>
@else
<button type="button" {{ $attributes->merge(['class' => $classes]) }} aria-pressed="{{ $active ? 'true' : 'false' }}">{{ $slot }}</button>
@endif
