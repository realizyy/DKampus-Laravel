{{-- Judul halaman desktop (display, tracking rapat) + slot aksi di kanan --}}
@props(['title', 'subtitle' => null])
<div {{ $attributes->class('flex items-end justify-between gap-4') }}>
    <div class="min-w-0">
        <h1 class="font-display text-3xl font-extrabold leading-tight tracking-tightest text-ink md:text-4xl">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-1 text-[15px] text-ink-500">{{ $subtitle }}</p>@endif
    </div>
    @if (! $slot->isEmpty())<div class="flex shrink-0 items-center gap-2">{{ $slot }}</div>@endif
</div>
