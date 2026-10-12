@extends('layouts.base')

{{--
    Layout auth (Figma: Masuk, Registrasi, Verifikasi, Atur ulang, Berhasil).
    Mobile: bar atas (kembali + logo) lalu form di tengah. Desktop: split 60/40 dengan panel merek di kiri.
    Section: content (wajib), back (URL tombol kembali; default beranda), panel-title/panel-text (opsional).
--}}
@section('shell')
<div class="min-h-dvh md:grid md:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
    {{-- Panel merek (desktop) --}}
    <aside class="relative hidden overflow-hidden bg-brand-700 text-canvas md:flex md:flex-col md:justify-between md:p-12 lg:p-16" aria-hidden="true">
        <img src="{{ asset('logoDkampus.svg') }}" alt="" class="pointer-events-none absolute -bottom-24 -right-20 w-[34rem] max-w-none opacity-[0.14] brightness-0 invert">
        <div class="relative flex items-center gap-3">
            <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-9 w-auto brightness-0 invert">
            <span class="font-display text-xl font-extrabold tracking-tight">Dkampus</span>
        </div>
        <div class="relative max-w-xl">
            <h2 class="font-display text-5xl font-extrabold leading-[0.95] tracking-tightest xl:text-7xl">@hasSection('panel-title')@yield('panel-title')@else Selamat datang di Dkampus.@endif</h2>
            <p class="mt-6 max-w-md text-lg leading-relaxed text-brand-100">@hasSection('panel-text')@yield('panel-text')@else Jelajahi kelezatan kuliner di sekitar Telkom University dengan mudah dan cepat!@endif</p>
        </div>
        <p class="relative font-mono text-xs uppercase tracking-widest text-brand-200">Telkom University · Bandung</p>
    </aside>

    <main class="flex min-h-dvh flex-col px-5 pb-8 pt-[max(1.25rem,env(safe-area-inset-top))] md:px-12 md:py-10">
        <header class="flex items-center justify-between">
            <a href="@hasSection('back')@yield('back')@else{{ url('/') }}@endif" class="ui-press -ml-2 grid size-11 place-items-center rounded-xl text-brand-700 hover:bg-sunken" aria-label="Kembali">
                <x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" />
            </a>
            <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Dkampus">
                <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-8 w-auto">
                <span class="font-display text-base font-extrabold tracking-tight text-ink md:hidden">Dkampus</span>
            </a>
        </header>

        <div class="mx-auto flex w-full max-w-[26rem] flex-1 flex-col justify-center py-8" x-motion.rise>
            @yield('content')
        </div>

        <footer class="mx-auto hidden w-full max-w-[26rem] justify-center gap-4 text-sm text-ink-500 md:flex">
            <a href="{{ route('terms') }}" class="hover:text-ink hover:underline">Syarat dan Ketentuan</a>
            <a href="{{ route('privacy') }}" class="hover:text-ink hover:underline">Kebijakan Privasi</a>
        </footer>
    </main>
</div>
@endsection
