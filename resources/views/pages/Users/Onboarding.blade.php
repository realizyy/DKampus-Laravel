@extends('layouts.auth')

@section('back', url('/'))

@section('content')
{{-- Splash (Figma): layar oranye dengan logo putih, sekali per sesi --}}
<div x-data="{ splash: false, init() { try { if (!sessionStorage.getItem('dk.splash')) { this.splash = true; sessionStorage.setItem('dk.splash', '1'); setTimeout(() => this.splash = false, 1300) } } catch (e) {} } }">
    <div x-show="splash" x-cloak x-transition:leave="transition duration-500 ease-out-quint" x-transition:leave-end="opacity-0 scale-105"
         class="fixed inset-0 z-[80] grid place-items-center bg-brand-500">
        <img src="{{ asset('logoDkampus.svg') }}" alt="Dkampus" class="h-28 w-auto brightness-0 invert">
    </div>

    <div class="overflow-hidden rounded-3xl bg-sunken shadow-hairline md:hidden">
        <img src="{{ asset('bannerWelcome.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover object-right">
    </div>

    <h1 class="mt-8 font-display text-4xl font-extrabold leading-[1.02] tracking-tightest text-ink md:mt-0">Selamat Datang di Dkampus</h1>
    <p class="mt-3 text-[17px] leading-relaxed text-ink-600">Jelajahi kelezatan kuliner di sekitar Telkom University dengan mudah dan cepat!</p>

    <div class="mt-8 space-y-3">
        <x-ui.button :href="route('login')" size="lg" block>Masuk</x-ui.button>
        <x-ui.button :href="route('register')" size="lg" variant="outline" block>Daftar</x-ui.button>
        <x-ui.button :href="url('/')" variant="ghost" block>Lihat warung dulu</x-ui.button>
    </div>
</div>
@endsection
