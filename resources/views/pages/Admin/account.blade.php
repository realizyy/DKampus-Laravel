@extends('layouts.admin')

@php
    $roleLabels = ['customer' => 'Customer', 'courier' => 'Driver', 'admin' => 'Admin'];
    $me = auth()->id();
    // Indeks ringan untuk penyaringan di klien (baris tetap dirender server, jadi halaman bekerja tanpa JS)
    $index = $users->map(fn ($u) => [
        'id' => $u->id,
        'role' => $u->role,
        'off' => (bool) $u->restriction,
        'hay' => mb_strtolower($u->nama_user.' '.$u->email.' '.$u->no_telp),
    ])->values();
    $counts = $users->countBy('role');
    $grid = 'md:grid-cols-[minmax(0,2.4fr)_6.5rem_9.5rem_8.5rem_7rem_5.5rem]';
@endphp

@section('page-title', 'Kelola Akun')
@section('subtitle', 'Atur role dan status akun customer, driver, dan admin.')

@section('content')
<div x-data="accountList(@js($index))" class="space-y-4">
    {{-- Pencarian + filter --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="lg:w-96">
            <x-ui.input type="search" icon="search" x-model="q" placeholder="Cari akun" aria-label="Cari akun" autocomplete="off" />
        </div>
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0 lg:pb-0">
            <x-ui.chip x-on:click="role = 'all'" ::aria-pressed="role === 'all'" ::class="role === 'all' && '!bg-ink !text-canvas'">Semua <span class="font-mono text-xs opacity-75">{{ $users->count() }}</span></x-ui.chip>
            @foreach ($roleLabels as $value => $label)
                <x-ui.chip x-on:click="role = '{{ $value }}'" ::aria-pressed="role === '{{ $value }}'" ::class="role === '{{ $value }}' && '!bg-ink !text-canvas'">{{ $label }} <span class="font-mono text-xs opacity-75">{{ $counts->get($value, 0) }}</span></x-ui.chip>
            @endforeach
            <span class="mx-1 hidden w-px self-stretch bg-line md:block" aria-hidden="true"></span>
            <x-ui.chip x-on:click="status = status === 'active' ? 'all' : 'active'" ::aria-pressed="status === 'active'" ::class="status === 'active' && '!bg-moss-600 !text-white'">Aktif</x-ui.chip>
            <x-ui.chip x-on:click="status = status === 'off' ? 'all' : 'off'" ::aria-pressed="status === 'off'" ::class="status === 'off' && '!bg-brick-600 !text-white'">Non Aktif</x-ui.chip>
        </div>
    </div>

    <p class="text-sm text-ink-500" aria-live="polite">
        <template x-if="filtered"><span>Hasil pencarian: <span class="font-semibold text-ink" x-text="visibleCount"></span> dari {{ $users->count() }} akun</span></template>
        <template x-if="!filtered"><span>{{ $users->count() }} akun terdaftar</span></template>
    </p>

    <div class="overflow-hidden rounded-2xl bg-surface shadow-card">
        {{-- Judul kolom (desktop) --}}
        <div class="hidden gap-4 border-b border-line bg-sunken/60 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-ink-500 md:grid {{ $grid }}">
            <span>Nama</span><span>Role</span><span>Telepon</span><span>Terdaftar</span><span>Status</span><span class="text-right">Aksi</span>
        </div>

        <ul class="divide-y divide-line">
            @foreach ($users as $u)
                <li x-show="visible({{ $u->id }})" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1.5 px-4 py-3.5 hover:bg-canvas/70 md:gap-x-4 md:px-5 {{ $grid }}">
                    <div class="col-start-1 row-start-1 flex min-w-0 items-center gap-3 md:col-auto md:row-auto">
                        <x-ui.avatar :name="$u->nama_user" />
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">{{ $u->nama_user ?: '—' }}@if ($u->id === $me)<span class="ml-1.5 text-xs font-medium text-ink-500">(Anda)</span>@endif</p>
                            <p class="truncate text-sm text-ink-500">{{ $u->email }}</p>
                        </div>
                    </div>
                    <div class="hidden md:block"><x-ui.badge :tone="$u->role === 'admin' ? 'brand' : ($u->role === 'courier' ? 'teal' : 'neutral')">{{ $roleLabels[$u->role] ?? ucfirst($u->role) }}</x-ui.badge></div>
                    <p class="hidden font-mono text-sm text-ink-600 md:block">{{ $u->no_telp ?: '—' }}</p>
                    <p class="hidden font-mono text-sm text-ink-600 md:block">{{ $u->created_at?->locale('id')->translatedFormat('j M Y') ?? '—' }}</p>
                    <div class="col-start-1 row-start-2 flex items-center gap-2 pl-[3.25rem] md:col-auto md:row-auto md:block md:pl-0">
                        <span class="md:hidden"><x-ui.badge :tone="$u->role === 'admin' ? 'brand' : ($u->role === 'courier' ? 'teal' : 'neutral')">{{ $roleLabels[$u->role] ?? ucfirst($u->role) }}</x-ui.badge></span>
                        <x-ui.status :value="$u->restriction ? 'Non Aktif' : 'Aktif'" />
                    </div>
                    <div class="col-start-2 row-span-2 row-start-1 md:col-auto md:row-auto md:row-span-1 md:text-right">
                        <button type="button" data-modal-open="user" data-id="{{ $u->id }}" data-nama_user="{{ $u->nama_user }}" data-email="{{ $u->email }}" data-no_telp="{{ $u->no_telp }}"
                                data-role="{{ $u->role }}" data-restriction="{{ (int) $u->restriction }}" data-registered="{{ $u->created_at?->locale('id')->translatedFormat('j F Y') }}"
                                data-self="{{ $u->id === $me ? 1 : 0 }}" data-delete-url="{{ route('account.destroy', $u) }}"
                                class="ui-press inline-flex h-9 items-center gap-1 rounded-lg bg-surface px-3 text-sm font-semibold text-ink shadow-hairline hover:bg-sunken" aria-label="Detail akun {{ $u->nama_user }}">
                            Detail<x-ui.icon name="chevron-right" class="size-4 text-ink-400" />
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        <div x-show="visibleCount === 0" x-cloak>
            <x-ui.empty-state icon="search" title="Akun tidak ditemukan" description="Coba kata kunci lain atau ubah filter role dan status." />
        </div>
        @if ($users->isEmpty())
            <x-ui.empty-state icon="users" title="Belum ada akun" description="Akun yang mendaftar akan muncul di sini." />
        @endif
    </div>
</div>

{{-- Panel detail akun: Figma DETAIL AKUN. Hook lama dipertahankan: id_user (diisi JS), nama_user, email, no_telp, role, restriction --}}
<x-ui.sheet name="user" title="Detail Akun" description="Informasi akun dan pengaturan role">
    <form action="{{ route('editUser') }}" method="POST" id="user-form" class="space-y-5">
        @csrf
        @method('POST')
        {{-- Diisi editUser() sesuai baris yang diklik --}}
        <input type="hidden" name="id_user" id="id_user" value="">

        <div class="flex items-center gap-3 rounded-2xl bg-sunken p-3 shadow-inset">
            <x-ui.avatar name="" size="lg" id="user-avatar" />
            <div class="min-w-0">
                <p id="user-heading" class="truncate font-display text-lg font-bold tracking-tight text-ink">—</p>
                <p class="font-mono text-xs text-ink-500">Terdaftar <span id="user-registered">—</span></p>
            </div>
        </div>

        <fieldset class="space-y-4">
            <legend class="mb-1 text-xs font-semibold uppercase tracking-wider text-ink-500">Informasi Akun</legend>
            <x-ui.field label="Nama Lengkap" for="nama_user"><x-ui.input type="text" name="nama_user" id="nama_user" value="" readonly class="!bg-sunken !text-ink-600" /></x-ui.field>
            <x-ui.field label="Email" for="email"><x-ui.input type="email" name="email" id="email" value="" readonly class="!bg-sunken !text-ink-600" /></x-ui.field>
            <x-ui.field label="Nomor Handphone" for="no_telp"><x-ui.input type="text" name="no_telp" id="no_telp" value="" readonly class="!bg-sunken font-mono !text-ink-600" /></x-ui.field>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="mb-1 text-xs font-semibold uppercase tracking-wider text-ink-500">Pengaturan</legend>
            {{-- Hanya role & restriction yang disimpan (AdminController::editUser) --}}
            <x-ui.field label="Role" for="role">
                <x-ui.select name="role" id="role">
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                    <option value="courier">Driver (Courier)</option>
                </x-ui.select>
            </x-ui.field>
            <x-ui.field label="Status akun" for="restriction" hint="Akun Non Aktif tidak bisa masuk ke aplikasi.">
                <x-ui.select name="restriction" id="restriction">
                    <option value="0">Aktif</option>
                    <option value="1">Non Aktif</option>
                </x-ui.select>
            </x-ui.field>
        </fieldset>
    </form>

    <x-slot:footer>
        <div class="flex flex-col gap-2">
            <x-ui.button type="submit" form="user-form" block>Simpan</x-ui.button>
            <x-ui.button variant="danger-soft" id="user-delete" data-modal-open="delete-user" icon="trash-2" block>Hapus Akun</x-ui.button>
        </div>
    </x-slot:footer>
</x-ui.sheet>

{{-- Konfirmasi hapus akun (Figma: HAPUS AKUN). Action diisi JS sesuai akun yang dipilih --}}
<x-ui.modal name="delete-user" tone="danger" title="Hapus akun ini?" description="Akun dan datanya (alamat, favorit, rating) dihapus permanen. Akun yang punya riwayat transaksi tidak bisa dihapus; nonaktifkan saja.">
    <p class="rounded-xl bg-sunken px-3 py-2 text-[15px] font-semibold text-ink" id="delete-user-name">—</p>
    <x-slot:actions>
        <form method="POST" action="#" id="delete-user-form" class="contents">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger" block>Hapus</x-ui.button>
        </form>
        <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
@endsection

@push('js')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('accountList', (index) => ({
        index,
        byId: new Map(index.map((u) => [u.id, u])),
        q: '',
        role: 'all',
        status: 'all',
        get filtered() { return this.q.trim() !== '' || this.role !== 'all' || this.status !== 'all'; },
        match(u) {
            const q = this.q.trim().toLowerCase();
            return (this.role === 'all' || u.role === this.role)
                && (this.status === 'all' || (this.status === 'off') === u.off)
                && (q === '' || u.hay.includes(q));
        },
        visible(id) { return this.match(this.byId.get(id)); },
        get visibleCount() { return this.index.filter((u) => this.match(u)).length; },
    }));
});

(function () {
    var byId = function (id) { return document.getElementById(id); };
    function initials(name) {
        return (name || '').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0).toUpperCase(); }).join('') || '·';
    }

    // Isi panel dari tombol "Detail" yang diklik (menggantikan editUser(this) inline)
    byId('modal-user').addEventListener('modal:opening', function (event) {
        var d = event.detail;
        byId('id_user').value = d.id;
        byId('nama_user').value = d.nama_user;
        byId('email').value = d.email;
        byId('no_telp').value = d.no_telp;
        byId('role').value = d.role;
        byId('restriction').value = d.restriction;
        byId('user-heading').textContent = d.nama_user || '—';
        byId('user-registered').textContent = d.registered || '—';
        byId('user-avatar').textContent = initials(d.nama_user);
        byId('user-delete').classList.toggle('hidden', d.self === '1');
        byId('delete-user-form').action = d.deleteUrl;
        byId('delete-user-name').textContent = d.nama_user + ' · ' + d.email;
    });
})();
</script>
@endpush
