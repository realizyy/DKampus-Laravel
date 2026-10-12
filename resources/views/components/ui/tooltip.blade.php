@props(['text', 'position' => 'bottom'])
<span {{ $attributes->class(["d-tooltip d-tooltip-{$position} [--tooltip-color:theme(colors.ink.DEFAULT)] [--tooltip-text-color:theme(colors.canvas)] [@media(pointer:coarse)]:before:hidden"]) }} data-tip="{{ $text }}">{{ $slot }}</span>
