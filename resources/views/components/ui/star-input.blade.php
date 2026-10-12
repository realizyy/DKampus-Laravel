{{-- Rating bintang 1-5 (klik atau panah kiri/kanan). Mengisi <input type=hidden name=...> --}}
@props(['name', 'value' => 0, 'label' => 'Rating'])
<div x-data="{ v: {{ (int) $value }}, hover: 0 }" {{ $attributes->class('inline-flex items-center gap-1') }} role="radiogroup" aria-label="{{ $label }}">
    <input type="hidden" name="{{ $name }}" :value="v">
    @for ($i = 1; $i <= 5; $i++)
        <button type="button" role="radio" :aria-checked="v === {{ $i }}" aria-label="{{ $i }} bintang"
                @click="v = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                @keydown.arrow-right.prevent="v = Math.min(5, v + 1)" @keydown.arrow-left.prevent="v = Math.max(1, v - 1)"
                class="ui-press grid size-11 place-items-center rounded-lg hover:bg-sunken">
            <x-ui.icon name="star" class="size-7 transition-colors" x-bind:class="(hover || v) >= {{ $i }} ? 'fill-brand-500 text-brand-500' : 'text-ink-300'" />
        </button>
    @endfor
</div>
