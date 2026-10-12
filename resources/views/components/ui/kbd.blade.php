{{-- Petunjuk pintasan: disembunyikan di perangkat sentuh --}}
<kbd {{ $attributes->class('inline-flex h-5 min-w-5 items-center justify-center rounded-md bg-sunken px-1.5 font-mono text-[11px] font-medium text-ink-500 shadow-[inset_0_-1px_0_rgb(31_26_20/0.12),0_0_0_1px_rgb(31_26_20/0.08)] [@media(pointer:coarse)]:hidden') }}>{{ $slot }}</kbd>
