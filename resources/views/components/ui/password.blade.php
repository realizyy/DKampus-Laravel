{{-- Input kata sandi dengan tombol tampil/sembunyi (ikon mata, seperti Figma) --}}
@props(['error' => false, 'icon' => 'lock'])
<div x-data="{ show: false }" class="group relative">
    @if ($icon)
        <x-ui.icon :name="$icon" class="pointer-events-none absolute left-3.5 top-1/2 size-[18px] -translate-y-1/2 text-ink-400 transition-colors group-focus-within:text-brand-700" />
    @endif
    <input :type="show ? 'text' : 'password'" type="password" {{ $attributes->class([
        'block h-12 w-full rounded-xl border-0 bg-surface pr-12 text-[15px] text-ink shadow-inset ring-1 ring-inset transition placeholder:text-ink-400 focus:ring-2 disabled:cursor-not-allowed disabled:bg-sunken',
        'pl-11' => $icon,
        'pl-4' => ! $icon,
        'ring-line hover:ring-line-strong focus:ring-brand-600' => ! $error,
        'ring-brick-600 focus:ring-brick-600' => $error,
    ]) }} @if ($error) aria-invalid="true" @endif>
    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="show"
            class="ui-press absolute right-1.5 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink">
        <x-ui.icon name="eye" class="size-[18px]" x-show="!show" />
        <x-ui.icon name="eye-off" class="size-[18px]" x-show="show" x-cloak />
    </button>
</div>
