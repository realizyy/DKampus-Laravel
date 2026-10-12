{{-- Profil & pengaturan akun (Figma: PROFILE ADMIN). Satu view untuk semua role; kerangka halaman mengikuti role. --}}
@php
    $role = $user->role ?? 'customer';
    $Title = 'Profil';
    $roleLabel = ['admin' => 'Admin DKampus', 'courier' => 'Driver DKampus'][$role] ?? 'Customer DKampus';
@endphp
@extends(match ($role) { 'admin' => 'layouts.admin', 'courier' => 'layouts.courier', default => 'layouts.customer' })

@section('page-title', 'Profil')
@section('subtitle', 'Kelola informasi akun dan keamanan.')
@if ($role === 'customer')
    @section('back', url('/'))
@endif

@section('content')
<div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-12">
    {{-- Kolom kiri: ringkasan + tautan --}}
    <div class="space-y-4 lg:sticky lg:top-6 lg:col-span-4">
        <x-ui.card class="flex flex-col items-center py-8 text-center" x-motion.rise>
            <x-ui.avatar :name="$user->nama_user" size="xl" />
            <h2 class="mt-4 font-display text-2xl font-extrabold tracking-tight text-ink">{{ $user->nama_user ?: '—' }}</h2>
            <p class="mt-1 break-all text-[15px] text-ink-500">{{ $user->email }}</p>
            <p class="tnum font-mono text-sm text-ink-600">{{ $user->no_telp ?: '—' }}</p>
            <x-ui.badge tone="brand" class="mt-3">{{ $roleLabel }}</x-ui.badge>
        </x-ui.card>

        <section aria-labelledby="seputar-title" x-motion.rise="{ delay: 0.05 }">
            <h3 id="seputar-title" class="mb-2 px-1 font-mono text-[11px] uppercase tracking-widest text-ink-500">Seputar Dkampus</h3>
            <x-ui.card :padded="false" class="divide-y divide-line/70 overflow-hidden">
                @foreach ([['Tentang Kami', '/tentang-kami', 'info'], ['Syarat dan Ketentuan', '/syarat-ketentuan', 'file-text'], ['Kebijakan Privasi', '/kebijakan-privasi', 'shield-check']] as [$label, $href, $icon])
                    <a href="{{ url($href) }}" class="ui-press flex items-center gap-3 p-4 hover:bg-sunken">
                        <span class="grid size-10 place-items-center rounded-lg bg-sunken text-ink-600"><x-ui.icon :name="$icon" class="size-5" /></span>
                        <span class="flex-1 font-semibold text-ink">{{ $label }}</span>
                        <x-ui.icon name="chevron-right" class="size-5 text-ink-400" />
                    </a>
                @endforeach
                <button type="button" data-modal-open="logout-profile" class="ui-press flex w-full items-center gap-3 p-4 text-left hover:bg-brick-50">
                    <span class="grid size-10 place-items-center rounded-lg bg-brick-50 text-brick-700"><x-ui.icon name="log-out" class="size-5" /></span>
                    <span class="flex-1 font-semibold text-brick-700">Keluar</span>
                </button>
            </x-ui.card>
        </section>
    </div>

    {{-- Kolom kanan: form --}}
    <div class="space-y-4 lg:col-span-8">
        <x-ui.card class="p-5 md:p-6" x-motion.rise>
            @include('profile.partials.update-profile-information-form')
        </x-ui.card>
        <x-ui.card class="p-5 md:p-6" x-motion.rise="{ delay: 0.05 }">
            @include('profile.partials.update-password-form')
        </x-ui.card>
        @include('profile.partials.delete-user-form')
    </div>
</div>

<x-ui.confirm name="logout-profile" icon="log-out" title="Konfirmasi Logout" description="Apakah Anda yakin ingin keluar dari aplikasi?" :action="route('logout')" confirm="Logout" />
@endsection
