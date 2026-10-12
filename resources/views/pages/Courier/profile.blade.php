@extends('layouts.courier')

@section('page-title', 'Profil')

@section('content')
@php
    $user = Auth::user();
    $passwordErrors = $errors->updatePassword->all();
    $phoneErrors = $errors->updatePhone->all();
    $rows = [
        ['icon' => 'key-round', 'label' => 'Ubah Kata Sandi', 'hint' => 'Ganti kata sandi akunmu', 'open' => 'password'],
        ['icon' => 'phone', 'label' => 'Ubah Nomor Telepon', 'hint' => $user->no_telp ?? '-', 'open' => 'phone'],
    ];
@endphp

@if (session('status') === 'password-updated' || session('status') === 'phone-updated')
    <div class="mb-4 flex items-center gap-2 rounded-xl bg-moss-100 p-3 text-[15px] font-semibold text-moss-700" role="status">
        <x-ui.icon name="circle-check" class="size-5" />
        {{ session('status') === 'password-updated' ? 'Kata sandi berhasil diubah.' : 'Nomor telepon berhasil diubah.' }}
    </div>
@endif
@if ($passwordErrors || $phoneErrors)
    <div class="mb-4 rounded-xl bg-brick-50 p-3 text-[15px] text-brick-700" role="alert">
        <ul class="list-disc space-y-0.5 pl-5">
            @foreach (array_merge($passwordErrors, $phoneErrors) as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid items-start gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
    <x-ui.card class="flex flex-col items-center py-8 text-center" x-motion.rise>
        <x-ui.avatar :name="$user->nama_user" size="xl" />
        <h2 class="mt-4 font-display text-2xl font-extrabold tracking-tight text-ink">{{ $user->nama_user ?? '-' }}</h2>
        <p class="mt-1 text-[15px] text-ink-500">{{ $user->email ?? '-' }}</p>
        <p class="tnum font-mono text-sm text-ink-600">{{ $user->no_telp ?? '-' }}</p>
        <x-ui.badge tone="brand" class="mt-3">Kurir DKampus</x-ui.badge>
    </x-ui.card>

    <div class="space-y-4">
        <section aria-labelledby="akun-title" x-motion.rise="{ delay: 0.05 }">
            <h3 id="akun-title" class="mb-2 px-1 font-mono text-[11px] uppercase tracking-widest text-ink-500">Pengaturan Akun</h3>
            <x-ui.card :padded="false" class="divide-y divide-line/70 overflow-hidden">
                @foreach ($rows as $row)
                    <button type="button" data-modal-open="{{ $row['open'] }}" class="ui-press flex w-full items-center gap-3 p-4 text-left hover:bg-sunken">
                        <span class="grid size-10 place-items-center rounded-lg bg-brand-100 text-brand-800"><x-ui.icon :name="$row['icon']" class="size-5" /></span>
                        <span class="min-w-0 flex-1"><span class="block font-semibold text-ink">{{ $row['label'] }}</span><span class="block truncate font-mono text-xs text-ink-500">{{ $row['hint'] }}</span></span>
                        <x-ui.icon name="chevron-right" class="size-5 text-ink-400" />
                    </button>
                @endforeach
                <div class="flex items-center gap-3 p-4 opacity-60" aria-disabled="true">
                    <span class="grid size-10 place-items-center rounded-lg bg-sunken text-ink-500"><x-ui.icon name="camera" class="size-5" /></span>
                    <span class="flex-1"><span class="block font-semibold text-ink">Foto Profil</span><span class="block font-mono text-xs text-ink-500">Segera hadir</span></span>
                </div>
            </x-ui.card>
        </section>

        <section aria-labelledby="seputar-title" x-motion.rise="{ delay: 0.1 }">
            <h3 id="seputar-title" class="mb-2 px-1 font-mono text-[11px] uppercase tracking-widest text-ink-500">Seputar Dkampus</h3>
            <x-ui.card :padded="false" class="divide-y divide-line/70 overflow-hidden">
                @foreach ([['Tentang Kami', '/tentang-kami', 'info'], ['Syarat dan Ketentuan', '/syarat-ketentuan', 'file-text']] as [$label, $href, $icon])
                    <a href="{{ $href }}" class="ui-press flex items-center gap-3 p-4 hover:bg-sunken">
                        <span class="grid size-10 place-items-center rounded-lg bg-sunken text-ink-600"><x-ui.icon :name="$icon" class="size-5" /></span>
                        <span class="flex-1 font-semibold text-ink">{{ $label }}</span>
                        <x-ui.icon name="chevron-right" class="size-5 text-ink-400" />
                    </a>
                @endforeach
            </x-ui.card>
        </section>

        <x-ui.button variant="danger-soft" icon="log-out" data-modal-open="logout" block>Logout</x-ui.button>
    </div>
</div>

<x-ui.modal name="password" size="md" title="Ubah Kata Sandi" icon="key-round">
    <form class="space-y-4" method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')
        <x-ui.field label="Kata sandi lama" for="password" required>
            <x-ui.input type="password" id="password" name="current_password" autocomplete="current-password" required />
        </x-ui.field>
        <x-ui.field label="Kata sandi baru" for="new-password" required>
            <x-ui.input type="password" id="new-password" name="password" autocomplete="new-password" required />
        </x-ui.field>
        <x-ui.field label="Konfirmasi kata sandi baru" for="confirm-password" required>
            <x-ui.input type="password" id="confirm-password" name="password_confirmation" autocomplete="new-password" required />
        </x-ui.field>
        <div class="flex flex-col gap-2 pt-1">
            <x-ui.button type="submit" block>Simpan</x-ui.button>
            <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
        </div>
    </form>
</x-ui.modal>

<x-ui.modal name="phone" size="md" title="Ubah Nomor Telepon" icon="phone">
    <form class="space-y-4" method="POST" action="{{ route('cour.profile.phone') }}">
        @csrf
        @method('PATCH')
        <x-ui.field label="Nomor telepon baru" for="no-telp" required>
            <x-ui.input type="tel" id="no-telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}" maxlength="13" required />
        </x-ui.field>
        <div class="flex flex-col gap-2 pt-1">
            <x-ui.button type="submit" block>Simpan</x-ui.button>
            <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
        </div>
    </form>
</x-ui.modal>

@if ($passwordErrors || $phoneErrors)
    @push('js')
        <script>
            // Buka kembali modal yang formnya gagal divalidasi
            document.addEventListener('DOMContentLoaded', function () { dkModal.open({{ $passwordErrors ? "'password'" : "'phone'" }}); });
        </script>
    @endpush
@endif
@endsection
