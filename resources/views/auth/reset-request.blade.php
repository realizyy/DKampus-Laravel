@extends('layouts.auth')

@section('back', route('login'))
@section('panel-title', 'Lupa kata sandi? Tenang.')
@section('panel-text', 'Kami kirim kode ke emailmu, lalu kamu bisa membuat kata sandi baru.')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink">Atur Ulang Kata Sandi</h1>
<p class="mt-2 text-[15px] leading-snug text-ink-500">Masukkan alamat email Anda di bawah ini, dan kami akan mengirimkan kode untuk mereset kata sandi.</p>

<form method="POST" action="{{ route('reset.send') }}" class="mt-8 space-y-5" novalidate>
    @csrf
    <x-ui.field label="Email" for="email" :error="$errors->first('email')">
        <x-ui.input type="email" id="email" name="email" :value="old('email')" placeholder="Masukkan email" icon="mail" autocomplete="email" :error="$errors->has('email')" required autofocus />
    </x-ui.field>
    <x-ui.button type="submit" size="lg" block>Kirim Kode</x-ui.button>
</form>
@endsection
