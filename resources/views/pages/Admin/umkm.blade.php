@extends('layouts.admin')

@php
    $grid = 'md:grid-cols-[minmax(0,2.8fr)_9rem_5.5rem_5rem_9.5rem]';
    $filterUrl = fn (string $kat = '') => route('umkm', array_filter(['q' => $q, 'kategori' => $kat]));
@endphp

@section('page-title', 'Kelola Resto')
@section('subtitle', 'Resto yang terdaftar, jam buka, dan produknya.')

@section('header-actions')
    <x-ui.button href="{{ route('umkm.create') }}" icon="plus" size="sm"><span class="hidden sm:inline">Tambah Resto</span><span class="sm:hidden">Tambah</span></x-ui.button>
@endsection

@section('content')
<div class="space-y-4">
    @if ($errors->any())
        <div class="rounded-2xl bg-brick-50 p-4 text-sm text-brick-700 shadow-hairline" role="alert">
            <ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Pencarian (server) + chip kategori (Figma: Semua Kategori, Aneka Nasi, …) --}}
    <form method="GET" action="{{ route('umkm') }}" role="search" class="lg:w-[28rem]">
        @if ($kategori !== '')<input type="hidden" name="kategori" value="{{ $kategori }}">@endif
        <x-ui.input type="search" name="q" icon="search" :value="$q" placeholder="Cari nama atau alamat resto" aria-label="Cari resto" autocomplete="off" />
    </form>
    <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 md:mx-0 md:flex-wrap md:overflow-visible md:px-0 md:pb-0">
        <x-ui.chip :href="$filterUrl()" :active="$kategori === ''">Semua Kategori</x-ui.chip>
        @foreach (\App\Models\Menu::CATEGORIES as $value => $label)
            <x-ui.chip :href="$filterUrl($value)" :active="$kategori === $value">{{ $label }}</x-ui.chip>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl bg-surface shadow-card">
        <div class="flex items-center justify-between border-b border-line px-4 py-3 md:px-5">
            <h2 class="font-display text-lg font-bold tracking-tight text-ink">Daftar Resto</h2>
            <span class="font-mono text-xs text-ink-500">{{ $umkms->total() }} resto</span>
        </div>
        <div class="hidden gap-4 border-b border-line bg-sunken/60 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-ink-500 md:grid {{ $grid }}">
            <span>Resto</span><span>Jam buka</span><span>Produk</span><span>Rating</span><span class="text-right">Aksi</span>
        </div>

        <ul class="divide-y divide-line">
            @foreach ($umkms as $umkm)
                <li class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1.5 px-4 py-3.5 hover:bg-canvas/70 md:gap-x-4 md:px-5 {{ $grid }}">
                    <a href="{{ route('umkmEdit', $umkm) }}" class="col-start-1 row-start-1 flex min-w-0 items-center gap-3 md:col-auto md:row-auto">
                        <x-ui.photo :src="Storage::url($umkm->logo_umkm)" :alt="$umkm->nama_umkm" icon="store" class="size-12 shrink-0 rounded-xl" />
                        <span class="min-w-0">
                            <span class="flex items-center gap-2">
                                <span class="truncate font-semibold text-ink">{{ $umkm->nama_umkm }}</span>
                                @if ($umkm->vip)<x-ui.badge tone="brand" class="shrink-0">VIP</x-ui.badge>@endif
                            </span>
                            <span class="block truncate text-sm text-ink-500">{{ $umkm->alamat }}</span>
                        </span>
                    </a>
                    <p class="hidden font-mono text-sm text-ink-600 md:block">{{ $umkm->open_time ? date('H:i', strtotime($umkm->open_time)) : '--:--' }} – {{ $umkm->close_time ? date('H:i', strtotime($umkm->close_time)) : '--:--' }}</p>
                    <p class="hidden font-mono text-sm text-ink-600 md:block">{{ $umkm->menu_count }}</p>
                    <p class="hidden items-center gap-1 font-mono text-sm text-ink-600 md:flex">@if ($umkm->rating)<x-ui.icon name="star" class="size-3.5 text-brand-500" />{{ $umkm->rating }}@else—@endif</p>
                    <p class="col-start-1 row-start-2 pl-[3.75rem] font-mono text-xs text-ink-500 md:hidden">
                        {{ $umkm->open_time ? date('H:i', strtotime($umkm->open_time)) : '--:--' }}–{{ $umkm->close_time ? date('H:i', strtotime($umkm->close_time)) : '--:--' }} · {{ $umkm->menu_count }} produk
                    </p>
                    <div class="col-start-2 row-span-2 row-start-1 flex items-center justify-end gap-1.5 md:col-auto md:row-auto md:row-span-1">
                        <a href="{{ route('umkmEdit', $umkm) }}" class="ui-press hidden h-9 items-center gap-1 rounded-lg bg-surface px-3 text-sm font-semibold text-ink shadow-hairline hover:bg-sunken md:inline-flex" aria-label="Detail {{ $umkm->nama_umkm }}">Detail<x-ui.icon name="chevron-right" class="size-4 text-ink-400" /></a>
                        <button type="button" data-modal-open="delete-umkm" data-nama="{{ $umkm->nama_umkm }}" data-url="{{ route('umkmDestroy', $umkm->id) }}"
                                class="ui-press grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-brick-50 hover:text-brick-700" aria-label="Hapus {{ $umkm->nama_umkm }}">
                            <x-ui.icon name="trash-2" class="size-[18px]" />
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($umkms->isEmpty())
            @if ($q !== '' || $kategori !== '')
                <x-ui.empty-state icon="search" title="Resto tidak ditemukan" description="Coba kata kunci lain atau pilih Semua Kategori.">
                    <x-ui.button :href="route('umkm')" variant="outline" size="sm">Hapus filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="store" title="Belum ada resto" description="Tambahkan resto pertama beserta produknya.">
                    <x-ui.button :href="route('umkm.create')" icon="plus" size="sm">Tambah Resto</x-ui.button>
                </x-ui.empty-state>
            @endif
        @endif
    </div>

    <div>{{ $umkms->links() }}</div>
</div>

{{-- Konfirmasi hapus resto (Figma: HAPUS RESTO). Action diisi JS sesuai baris --}}
<x-ui.modal name="delete-umkm" tone="danger" title="Hapus resto ini?" description="Resto dan semua produknya dihapus permanen. Riwayat transaksi tidak ikut terhapus.">
    <p class="rounded-xl bg-sunken px-3 py-2 text-[15px] font-semibold text-ink" id="delete-umkm-name">—</p>
    <x-slot:actions>
        <form method="POST" action="#" id="delete-umkm-form" class="contents">
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
document.getElementById('modal-delete-umkm').addEventListener('modal:opening', function (event) {
    document.getElementById('delete-umkm-name').textContent = event.detail.nama;
    document.getElementById('delete-umkm-form').action = event.detail.url;
});
</script>
@endpush
