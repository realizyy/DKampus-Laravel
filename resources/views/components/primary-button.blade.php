<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-press inline-flex h-11 select-none items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-brand-700 px-4 text-[15px] font-semibold tracking-[-0.005em] text-white shadow-[inset_0_1px_0_rgb(255_255_255/0.16),0_0_0_1px_rgb(124_54_18/0.7)] hover:bg-brand-800 active:bg-brand-900']) }}>
    {{ $slot }}
</button>
