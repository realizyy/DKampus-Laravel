@props(['error' => false])
<div class="group relative">
    <select {{ $attributes->class([
        'block h-12 w-full appearance-none rounded-xl border-0 bg-surface bg-none pl-4 pr-10 text-[15px] text-ink shadow-inset ring-1 ring-inset transition focus:ring-2 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-400',
        'ring-line hover:ring-line-strong focus:ring-brand-600' => ! $error,
        'ring-brick-600 focus:ring-brick-600' => $error,
    ]) }}>{{ $slot }}</select>
    <x-ui.icon name="chevron-down" class="pointer-events-none absolute right-3.5 top-1/2 size-[18px] -translate-y-1/2 text-ink-400" />
</div>
