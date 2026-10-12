@extends('layouts.customer')

@section('page-title', 'Ubah Kata Sandi')
@section('back', url('/'))

@section('content')
<div class="mx-auto max-w-md space-y-6">
    <x-ui.page-header title="Ubah Kata Sandi" subtitle="Amankan akun dengan mengganti kata sandi." class="hidden md:flex" />

    @if (session('status') === 'password-updated')
        <div class="flex items-center gap-2 rounded-xl bg-moss-100 p-3 text-[15px] font-semibold text-moss-700" role="status"><x-ui.icon name="circle-check" class="size-5" />Kata sandi berhasil diubah.</div>
    @endif

    <x-ui.card class="p-5">
        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <x-ui.field label="Kata Sandi Lama" for="current_password" :error="$errors->updatePassword->first('current_password')" required>
                <x-ui.password id="current_password" name="current_password" autocomplete="current-password" :error="$errors->updatePassword->has('current_password')" required />
            </x-ui.field>
            <x-ui.field label="Kata Sandi Baru" for="password" :error="$errors->updatePassword->first('password')" hint="Minimal 8 karakter." required>
                <x-ui.password id="password" name="password" autocomplete="new-password" :error="$errors->updatePassword->has('password')" required />
            </x-ui.field>
            <x-ui.field label="Konfirmasi Kata Sandi Baru" for="password_confirmation" required>
                <x-ui.password id="password_confirmation" name="password_confirmation" autocomplete="new-password" required />
            </x-ui.field>
            <x-ui.button type="submit" size="lg" block>Ubah Kata Sandi</x-ui.button>
        </form>
    </x-ui.card>
</div>
@endsection
