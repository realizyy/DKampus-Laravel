@extends('layouts.auth')

@section('back', route('login'))

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink">Kata Sandi Baru</h1>
<p class="mt-2 text-[15px] leading-snug text-ink-500">Silakan masukkan dan konfirmasi kata sandi baru Anda.</p>

<form method="POST" action="{{ route('reset.update') }}" class="mt-8 space-y-5" novalidate>
    @csrf
    <x-ui.field label="Kata Sandi Baru" for="password" :error="$errors->first('password')" hint="Minimal 8 karakter.">
        <x-ui.password id="password" name="password" placeholder="Masukkan kata sandi baru" autocomplete="new-password" :error="$errors->has('password')" required autofocus />
    </x-ui.field>
    <x-ui.field label="Konfirmasi Kata Sandi" for="password_confirmation">
        <x-ui.password id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" autocomplete="new-password" required />
    </x-ui.field>
    <p class="flex items-center gap-2 text-sm text-ink-500"><x-ui.icon name="info" class="size-4 shrink-0" />Anda perlu login setelah mereset.</p>
    <x-ui.button type="submit" size="lg" block>Simpan</x-ui.button>
</form>
@endsection
