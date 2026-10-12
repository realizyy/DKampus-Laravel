@props(['name', 'stroke' => 2])
@php($inner = \App\Support\Icon::inner($name))
@if ($inner !== null)
<svg {{ $attributes->merge(['class' => 'size-5 shrink-0', 'aria-hidden' => 'true']) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round">{!! $inner !!}</svg>
@endif
