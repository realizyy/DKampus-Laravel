@props(['disabled' => false])

{{-- Input Breeze (auth/profil) dengan gaya sistem desain DKampus: lihat juga <x-ui.input> --}}
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'h-12 rounded-xl border-0 bg-surface px-4 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line transition placeholder:text-ink-400 hover:ring-line-strong focus:ring-2 focus:ring-brand-600 disabled:cursor-not-allowed disabled:bg-sunken disabled:text-ink-400']) !!}>
