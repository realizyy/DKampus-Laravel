@extends('layouts.admin')

@php
    $grid = 'md:grid-cols-[minmax(0,2.6fr)_9rem_11rem_6.5rem]';
    $filtered = $q !== '' || $kategori !== '' || $umkmId > 0;
@endphp

@section('page-title', 'Produk')
@section('subtitle', 'Semua menu dari seluruh resto.')

@section('header-actions')
    <x-ui.button icon="plus" size="sm" data-modal-open="add-product"><span class="hidden sm:inline">Tambah Produk</span><span class="sm:hidden">Tambah</span></x-ui.button>
@endsection

@section('content')
<div class="space-y-4">
    @if ($errors->any())
        <div class="rounded-2xl bg-brick-50 p-4 text-sm text-brick-700 shadow-hairline" role="alert">
            <ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Pencarian + filter (server). Perubahan pilihan langsung menerapkan filter --}}
    <form method="GET" action="{{ route('product') }}" role="search" class="grid gap-3 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
        <x-ui.input type="search" name="q" icon="search" :value="$q" placeholder="Cari nama makanan" aria-label="Cari produk" autocomplete="off" />
        <x-ui.select name="umkm" id="filter-umkm" aria-label="Filter resto" onchange="this.form.requestSubmit()">
            <option value="">Semua resto</option>
            @foreach ($umkms as $umkm)<option value="{{ $umkm->id }}" @selected($umkmId === $umkm->id)>{{ $umkm->nama_umkm }}</option>@endforeach
        </x-ui.select>
        <x-ui.select name="kategori" id="filter-kategori" aria-label="Filter kategori" onchange="this.form.requestSubmit()">
            <option value="">Semua kategori</option>
            @foreach (\App\Models\Menu::CATEGORIES as $value => $label)<option value="{{ $value }}" @selected($kategori === $value)>{{ $label }}</option>@endforeach
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary" class="md:h-12">Terapkan</x-ui.button>
    </form>

    <div class="overflow-hidden rounded-2xl bg-surface shadow-card">
        <div class="flex items-center justify-between border-b border-line px-4 py-3 md:px-5">
            <h2 class="font-display text-lg font-bold tracking-tight text-ink">Daftar Produk</h2>
            <span class="font-mono text-xs text-ink-500">{{ $menus->total() }} produk</span>
        </div>
        <div class="hidden gap-4 border-b border-line bg-sunken/60 px-5 py-2.5 text-xs font-semibold uppercase tracking-wider text-ink-500 md:grid {{ $grid }}">
            <span>Produk</span><span>Kategori</span><span>Harga</span><span class="text-right">Aksi</span>
        </div>

        <ul class="divide-y divide-line">
            @foreach ($menus as $menu)
                @php($final = $menu->harga * (1 - ((float) $menu->diskon) / 100))
                <li class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1 px-4 py-3.5 hover:bg-canvas/70 md:gap-x-4 md:px-5 {{ $grid }}">
                    <div class="col-start-1 row-start-1 flex min-w-0 items-center gap-3 md:col-auto md:row-auto">
                        <x-ui.photo :src="Storage::url($menu->image)" :alt="$menu->nama_makanan" class="size-14 shrink-0 rounded-xl" />
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">{{ $menu->nama_makanan }}</p>
                            <p class="truncate text-sm text-ink-500" title="{{ $menu->deskripsi }}">{{ $menu->data_umkm->nama_umkm ?? 'Resto dihapus' }}</p>
                        </div>
                    </div>
                    <p class="hidden text-sm text-ink-600 md:block">{{ \App\Models\Menu::CATEGORIES[$menu->category] ?? ($menu->category ?: '—') }}</p>
                    <p class="tnum col-start-1 row-start-2 flex flex-wrap items-center gap-x-2 pl-[4.25rem] font-mono text-sm text-ink md:col-auto md:row-auto md:pl-0" title="Harga setelah diskon">
                        Rp {{ number_format($final, 0, ',', '.') }}
                        @if ($menu->diskon > 0)
                            <span class="text-xs text-ink-400 line-through">{{ number_format($menu->harga, 0, ',', '.') }}</span>
                            <x-ui.badge tone="brand">{{ rtrim(rtrim(number_format($menu->diskon, 2, ',', ''), '0'), ',') }}%</x-ui.badge>
                        @endif
                        <span class="text-xs text-ink-500 md:hidden">· {{ \App\Models\Menu::CATEGORIES[$menu->category] ?? ($menu->category ?: '—') }}</span>
                    </p>
                    <div class="col-start-2 row-span-2 row-start-1 flex items-center justify-end gap-1 md:col-auto md:row-auto md:row-span-1">
                        <button type="button" data-modal-open="edit-product" data-id="{{ $menu->id }}" data-umkm="{{ $menu->data_umkm_id }}" data-nama_makanan="{{ $menu->nama_makanan }}"
                                data-deskripsi="{{ $menu->deskripsi }}" data-harga="{{ $menu->harga }}" data-promo="{{ $menu->diskon }}" data-category="{{ $menu->category }}"
                                data-update-url="{{ route('productUpdate', $menu->id) }}" data-image="{{ Storage::url($menu->image) }}"
                                class="ui-press grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink" aria-label="Edit {{ $menu->nama_makanan }}"><x-ui.icon name="pencil" class="size-[18px]" /></button>
                        <button type="button" data-modal-open="delete-product" data-nama="{{ $menu->nama_makanan }}" data-url="{{ route('productDestroy', $menu->id) }}"
                                class="ui-press grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-brick-50 hover:text-brick-700" aria-label="Hapus {{ $menu->nama_makanan }}"><x-ui.icon name="trash-2" class="size-[18px]" /></button>
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($menus->isEmpty())
            @if ($filtered)
                <x-ui.empty-state icon="search" title="Produk tidak ditemukan" description="Coba kata kunci lain atau longgarkan filter resto dan kategori.">
                    <x-ui.button :href="route('product')" variant="outline" size="sm">Hapus filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="utensils" title="Belum ada produk" description="Tambahkan menu untuk salah satu resto.">
                    <x-ui.button size="sm" icon="plus" data-modal-open="add-product">Tambah Produk</x-ui.button>
                </x-ui.empty-state>
            @endif
        @endif
    </div>

    <div>{{ $menus->links() }}</div>
</div>

@include('pages.Admin.partials.product-modals', ['umkms' => $umkms])
@endsection
