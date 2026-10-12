@props(['label' => null, 'for' => null, 'hint' => null, 'error' => null, 'required' => false])
<div {{ $attributes->class('space-y-1.5') }}>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="block text-sm font-semibold text-ink-700">
            {{ $label }}@if ($required)<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span>@endif
        </label>
    @endif
    {{ $slot }}
    @if ($error)
        <p class="flex items-center gap-1.5 text-sm text-brick-700" role="alert">
            <x-ui.icon name="circle-alert" class="size-4" />{{ $error }}
        </p>
    @elseif ($hint)
        <p class="text-sm text-ink-500">{{ $hint }}</p>
    @endif
</div>
