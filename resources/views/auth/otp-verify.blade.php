@extends('layouts.auth')

@php($reset = $purpose === 'reset')

@section('back', $reset ? route('reset.request') : route('login'))
@section('panel-title', $reset ? 'Hampir selesai.' : 'Satu langkah lagi.')
@section('panel-text', $reset ? 'Masukkan kode dari email untuk membuat kata sandi baru.' : 'Verifikasi emailmu agar akunmu aman dan kurir bisa menghubungimu.')

@section('content')
<h1 class="font-display text-4xl font-extrabold tracking-tightest text-ink">Verifikasi Akun</h1>
<p class="mt-2 text-[15px] leading-snug text-ink-500">Kode telah dikirim ke <span class="break-all font-semibold text-ink">{{ $email }}</span>.<br>Masukkan kode untuk {{ $reset ? 'mereset kata sandi' : 'memverifikasi akun' }} Anda.</p>

@if (session('status'))
    <div class="mt-5 rounded-xl bg-teal-100 p-3 text-[15px] font-semibold text-teal-700" role="status">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('otp.verify') }}" class="mt-7 space-y-3">
    @csrf
    <x-ui.otp name="code" :error="$errors->has('code')" />
    @error('code')
        <p class="flex items-center gap-1.5 text-sm text-brick-700" role="alert"><x-ui.icon name="circle-alert" class="size-4" />{{ $message }}</p>
    @enderror
    <x-ui.button type="submit" size="lg" block class="mt-2">Verifikasi</x-ui.button>
</form>

<div class="mt-8 text-center" x-data="{ left: {{ (int) $resendIn }}, init() { if (this.left > 0) { const t = setInterval(() => { if (--this.left <= 0) clearInterval(t) }, 1000) } }, get mmss() { return String(Math.floor(this.left / 60)).padStart(2, '0') + ':' + String(this.left % 60).padStart(2, '0') } }">
    <p class="text-[15px] text-ink-500">Tidak Menerima Kode?</p>
    <form method="POST" action="{{ route('otp.resend') }}" class="mt-1">
        @csrf
        <button type="submit" :disabled="left > 0" class="ui-press text-[15px] font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800 disabled:no-underline disabled:opacity-60">Kirim Ulang Kode</button>
    </form>
    <p class="tnum mt-1 font-mono text-sm text-ink-500" x-show="left > 0" aria-live="off">Kirim ulang kode dalam <span x-text="mmss"></span></p>
</div>
@endsection
