@extends('layouts.base')

{{-- Halaman statis (Tentang Kami, Syarat, Privasi): header sederhana + kartu teks. Section: content, back (opsional). --}}
@section('shell')
<div class="mx-auto flex min-h-dvh max-w-3xl flex-col px-4 pb-12 pt-[max(1rem,env(safe-area-inset-top))] md:px-8 md:pt-8">
    <header class="flex items-center justify-between">
        <a href="@hasSection('back')@yield('back')@else{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}@endif" class="ui-press -ml-2 flex items-center gap-1 rounded-xl pr-3 text-ink hover:bg-sunken">
            <span class="grid size-11 place-items-center text-brand-700"><x-ui.icon name="chevron-left" class="size-6" :stroke="2.25" /></span>
            <span class="font-semibold">Kembali</span>
        </a>
        <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Dkampus">
            <img src="{{ asset('logoDkampus.svg') }}" alt="" class="h-8 w-auto">
            <span class="font-display text-base font-extrabold tracking-tight">Dkampus</span>
        </a>
    </header>
    <main class="mt-6 flex-1" x-motion.rise>
        @yield('content')
    </main>
    <footer class="mt-10 flex flex-wrap justify-center gap-x-5 gap-y-1 text-sm text-ink-500">
        <a href="{{ route('about') }}" class="hover:text-ink hover:underline">Tentang Kami</a>
        <a href="{{ route('terms') }}" class="hover:text-ink hover:underline">Syarat dan Ketentuan</a>
        <a href="{{ route('privacy') }}" class="hover:text-ink hover:underline">Kebijakan Privasi</a>
    </footer>
</div>
@endsection
