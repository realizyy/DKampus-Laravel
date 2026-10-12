@props(['padded' => true, 'interactive' => false, 'tag' => 'div'])
<{{ $tag }} {{ $attributes->class([
    'rounded-2xl bg-surface shadow-card',
    'p-4' => $padded,
    'ui-press block hover:-translate-y-px hover:shadow-pop' => $interactive,
]) }}>{{ $slot }}</{{ $tag }}>
