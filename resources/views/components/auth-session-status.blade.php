@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-teal-100 p-3 text-[15px] font-semibold text-teal-700']) }} role="status">
        {{ $status }}
    </div>
@endif
