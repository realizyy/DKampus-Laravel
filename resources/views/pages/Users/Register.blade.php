@extends('layouts.auth')

@section('back', route('login'))
@section('panel-title', 'Satu akun, semua warung kampus.')
@section('panel-text', 'Daftar sekarang, pilih warung favoritmu, dan biarkan kurir Dsquad mengantar sampai ke kosan.')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink">Daftar</h1>
<p class="mt-2 text-[15px] text-ink-500">Buat akun Dkampus. Kami kirim kode verifikasi ke emailmu.</p>

<form id="formRegister" method="POST" action="{{ route('register') }}" class="mt-8 space-y-4" novalidate x-data="{ ok: false }">
    @csrf
    @if (session('error'))
        <div class="rounded-xl bg-brick-50 p-3 text-[15px] text-brick-700" role="alert">{{ session('error') }}</div>
    @endif

    <x-ui.field label="Nama Lengkap" for="nama" :error="$errors->first('nama')">
        <x-ui.input id="nama" name="nama" :value="old('nama')" placeholder="Nama lengkap" icon="user" autocomplete="name" :error="$errors->has('nama')" required />
    </x-ui.field>
    <x-ui.field label="Email" for="email" :error="$errors->first('email')">
        <x-ui.input type="email" id="email" name="email" :value="old('email')" placeholder="email@gmail.com" icon="mail" autocomplete="email" :error="$errors->has('email')" required />
    </x-ui.field>
    <x-ui.field label="Nomor Telepon" for="no_telp" :error="$errors->first('no_telp')">
        <x-ui.input type="tel" id="no_telp" name="no_telp" :value="old('no_telp')" placeholder="08xxxxxxxxxx" icon="phone" autocomplete="tel" maxlength="13" :error="$errors->has('no_telp')" required />
    </x-ui.field>
    <x-ui.field label="Kata Sandi" for="password" :error="$errors->first('password')" hint="Minimal 8 karakter.">
        <x-ui.password id="password" name="password" placeholder="Buat kata sandi" autocomplete="new-password" :error="$errors->has('password')" required />
    </x-ui.field>
    <x-ui.field label="Konfirmasi Kata Sandi" for="password_confirmation">
        <x-ui.password id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="new-password" required />
    </x-ui.field>

    <p class="text-center text-sm leading-snug text-ink-500">
        Dengan melanjutkan, artinya Anda setuju dengan
        <a href="{{ route('terms') }}" class="font-semibold text-brand-700 underline underline-offset-2">Syarat dan Ketentuan</a> dan juga
        <a href="{{ route('privacy') }}" class="font-semibold text-brand-700 underline underline-offset-2">Kebijakan Privasi</a> kami.
    </p>

    {{-- Konfirmasi email sebelum kirim: kode verifikasi dikirim ke alamat ini --}}
    <x-ui.button type="button" size="lg" block @click="$el.form.reportValidity() && (document.getElementById('validation_email').textContent = $el.form.email.value, dkModal.open('confirm-email'))">Daftar</x-ui.button>
</form>

{{-- Modal di luar <form>: <dialog> berisi <form method=dialog> tidak boleh bersarang di dalam form lain --}}
<x-ui.modal name="confirm-email" tone="brand" icon="mail" title="Apakah email Anda sudah benar?" description="Kode verifikasi akan dikirim ke alamat ini.">
    <p id="validation_email" class="break-all rounded-xl bg-sunken p-3 text-center font-mono text-sm font-semibold text-ink"></p>
    <x-slot:actions>
        <x-ui.button type="button" block onclick="this.disabled = true; document.getElementById('formRegister').requestSubmit()">Ya, kirim kode</x-ui.button>
        <x-ui.button variant="outline" data-modal-close block>Salah, ubah email</x-ui.button>
    </x-slot:actions>
</x-ui.modal>

<p class="mt-6 text-center text-[15px] text-ink-600">
    Sudah mempunyai akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800">Masuk</a>
</p>
@endsection
