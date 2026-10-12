{{-- Pilih file: <input type=file> asli (sr-only, tetap bisa difokus/diisi form) + tombol berupa <label> + nama file --}}
@props(['name', 'id' => null, 'accept' => '.jpg,.jpeg,.png', 'required' => false, 'label' => 'Pilih File'])
@php($id = $id ?? $name)
<div x-data="{ file: '' }" {{ $attributes->class('flex items-center gap-3') }}>
    <input type="file" name="{{ $name }}" id="{{ $id }}" accept="{{ $accept }}" @required($required) class="peer sr-only"
           @change="file = $event.target.files[0]?.name ?? ''">
    <label for="{{ $id }}" class="ui-press inline-flex h-11 shrink-0 cursor-pointer items-center gap-2 rounded-xl bg-surface px-4 text-[15px] font-semibold text-ink shadow-card hover:bg-sunken peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-500">
        <x-ui.icon name="paperclip" class="size-[18px]" />{{ $label }}
    </label>
    <span class="min-w-0 flex-1 truncate font-mono text-xs text-ink-500" x-text="file || 'Belum ada file'" aria-live="polite"></span>
</div>
