<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-press inline-flex h-11 select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-surface px-4 text-[15px] font-semibold text-ink shadow-card hover:bg-sunken active:bg-line']) }}>
    {{ $slot }}
</button>
