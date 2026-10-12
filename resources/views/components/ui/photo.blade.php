{{-- Foto dengan fallback: bila gambar gagal dimuat, tampil latar sunken dengan ikon (tidak ada ikon gambar rusak) --}}
@props(['src', 'alt' => '', 'icon' => 'utensils', 'grayscale' => false])
<div {{ $attributes->class(['relative' => ! preg_match('/\\b(absolute|fixed|sticky)\\b/', (string) $attributes->get('class')), 'overflow-hidden bg-sunken']) }}>
    <span class="absolute inset-0 grid place-items-center text-ink-300" aria-hidden="true"><x-ui.icon :name="$icon" class="size-8" /></span>
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" decoding="async" onerror="this.remove()"
         class="relative size-full object-cover {{ $grayscale ? 'grayscale opacity-60' : '' }}">
</div>
