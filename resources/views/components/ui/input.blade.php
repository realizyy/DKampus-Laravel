@props(['icon' => null, 'error' => false, 'type' => 'text'])
<div class="group relative">
    @if ($icon)
        <x-ui.icon :name="$icon" class="pointer-events-none absolute left-3.5 top-1/2 size-[18px] -translate-y-1/2 text-ink-400 transition-colors group-focus-within:text-brand-700" />
    @endif
    <input type="{{ $type }}" {{ $attributes->class([
        'block h-12 w-full rounded-xl border-0 bg-surface pr-4 text-[15px] text-ink shadow-inset ring-1 ring-inset transition placeholder:text-ink-400 focus:ring-2 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-400',
        'pl-11' => $icon,
        'pl-4' => ! $icon,
        'ring-line hover:ring-line-strong focus:ring-brand-600' => ! $error,
        'ring-brick-600 focus:ring-brick-600' => $error,
    ]) }} @if ($error) aria-invalid="true" @endif>
</div>
