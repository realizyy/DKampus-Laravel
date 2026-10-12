@props(['error' => false])
<textarea {{ $attributes->class([
    'block min-h-24 w-full rounded-xl border-0 bg-surface px-4 py-3 text-[15px] text-ink shadow-inset ring-1 ring-inset transition placeholder:text-ink-400 focus:ring-2 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-400',
    'ring-line hover:ring-line-strong focus:ring-brand-600' => ! $error,
    'ring-brick-600 focus:ring-brick-600' => $error,
]) }}>{{ $slot }}</textarea>
