@extends('layouts.auth')

@section('back', url('/'))

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink">Masuk</h1>
<p class="mt-2 text-[15px] text-ink-500">Pesan makanan dari warung sekitar kampus.</p>

<form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" novalidate>
    @csrf
    @if (session('success'))
        <div class="flex items-center gap-2 rounded-xl bg-moss-100 p-3 text-[15px] font-semibold text-moss-700" role="status"><x-ui.icon name="circle-check" class="size-5" />{{ session('success') }}</div>
    @endif
    @if (session('status'))
        <div class="rounded-xl bg-teal-100 p-3 text-[15px] font-semibold text-teal-700" role="status">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-brick-50 p-3 text-[15px] text-brick-700" role="alert">{{ session('error') }}</div>
    @endif

    <x-ui.field label="Email" for="email" :error="$errors->first('email')">
        <x-ui.input type="email" id="email" name="email" :value="old('email')" placeholder="Masukkan email" icon="mail" autocomplete="email" :error="$errors->has('email')" required autofocus />
    </x-ui.field>

    <x-ui.field label="Kata Sandi" for="password" :error="$errors->first('password')">
        <x-ui.password id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" :error="$errors->has('password')" required />
        <div class="pt-1 text-right">
            <a href="{{ route('reset.request') }}" class="text-sm font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800">Lupa Kata Sandi?</a>
        </div>
    </x-ui.field>

    <x-ui.button type="submit" size="lg" block>Masuk</x-ui.button>
</form>

<p class="mt-8 text-center text-[15px] text-ink-600">
    Belum mempunyai akun? <a href="{{ route('register') }}" class="font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800">Daftar</a>
</p>
@endsection
