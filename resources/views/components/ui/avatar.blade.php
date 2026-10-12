@props(['name' => '', 'src' => null, 'size' => 'md'])
@php
    $sizes = ['sm' => 'size-8 text-xs', 'md' => 'size-10 text-sm', 'lg' => 'size-14 text-lg', 'xl' => 'size-20 text-2xl'];
    $initials = collect(preg_split('/\s+/', trim($name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp
<span {{ $attributes->class(['relative inline-grid shrink-0 place-items-center overflow-hidden rounded-full bg-brand-100 font-display font-bold text-brand-800 shadow-hairline', $sizes[$size] ?? $sizes['md']]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="" class="size-full object-cover">
    @else
        {{ $initials !== '' ? $initials : '·' }}
    @endif
</span>
