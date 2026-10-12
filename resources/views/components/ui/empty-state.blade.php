@props(['icon' => 'package', 'title', 'description' => null])
<div {{ $attributes->class('flex flex-col items-center px-6 py-12 text-center') }} x-motion.rise>
    <span class="grid size-14 place-items-center rounded-2xl bg-sunken text-ink-400 shadow-hairline">
        <x-ui.icon :name="$icon" class="size-6" />
    </span>
    <h2 class="mt-4 font-display text-xl font-bold tracking-tight text-ink">{{ $title }}</h2>
    @if ($description)<p class="mt-1 max-w-[28ch] text-[15px] leading-snug text-ink-500">{{ $description }}</p>@endif
    @if (! $slot->isEmpty())<div class="mt-5">{{ $slot }}</div>@endif
</div>
