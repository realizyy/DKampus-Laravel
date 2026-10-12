{{--
    Unggah gambar dengan pratinjau (logo resto, gambar produk). <input type=file> asli tetap ada (sr-only) supaya form,
    fokus keyboard, dan validasi bawaan bekerja. $current = URL gambar yang sudah tersimpan (ditampilkan sebelum memilih file baru).
--}}
@props(['name', 'id' => null, 'required' => false, 'label' => 'Pilih gambar', 'hint' => 'JPG atau PNG, maksimal 2 MB', 'current' => null, 'icon' => 'image', 'error' => false])
@php($id = $id ?? $name)
<div x-data="{ src: @js($current), file: '' }" {{ $attributes->class('relative') }}>
    <input type="file" name="{{ $name }}" id="{{ $id }}" accept="image/jpeg,image/png,image/gif,image/webp" @required($required) class="peer sr-only"
           @change="const f = $event.target.files[0]; file = f ? f.name : ''; if (f) { src = URL.createObjectURL(f) }">
    <label for="{{ $id }}" class="ui-press flex cursor-pointer items-center gap-4 rounded-2xl bg-surface p-3 shadow-inset ring-1 ring-inset {{ $error ? 'ring-brick-600' : 'ring-line hover:ring-line-strong' }} hover:bg-canvas peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-500">
        <span class="relative grid size-20 shrink-0 place-items-center overflow-hidden rounded-xl bg-sunken text-ink-300">
            <x-ui.icon :name="$icon" class="size-7" x-show="!src" />
            <img x-show="src" x-cloak :src="src" alt="" class="absolute inset-0 size-full object-cover" @@error="src = null">
        </span>
        <span class="min-w-0">
            <span class="flex items-center gap-1.5 text-[15px] font-semibold text-ink"><x-ui.icon name="upload" class="size-4 text-brand-700" /><span x-text="src ? 'Ganti gambar' : '{{ $label }}'">{{ $label }}</span></span>
            <span class="mt-0.5 block truncate font-mono text-xs text-ink-500" x-text="file || '{{ $hint }}'">{{ $hint }}</span>
        </span>
    </label>
</div>
