@extends('layouts.admin')

@php
    $openTime = $umkm->open_time ? date('H:i', strtotime($umkm->open_time)) : '';
    $closeTime = $umkm->close_time ? date('H:i', strtotime($umkm->close_time)) : '';
    $vip = (string) old('edit_vip', (int) $umkm->vip);
    $categories = $products->pluck('category')->filter()->unique()->values();
    $allUmkms = collect([$umkm]);
@endphp

@section('page-title', $umkm->nama_umkm)
@section('subtitle', 'Detail resto: informasi, lokasi, dan daftar produk.')
@section('back', route('umkm'))

@section('header-actions')
    <x-ui.button variant="danger-soft" size="sm" icon="trash-2" data-modal-open="delete-umkm" data-nama="{{ $umkm->nama_umkm }}" data-url="{{ route('umkmDestroy', $umkm->id) }}"><span class="hidden sm:inline">Hapus Resto</span><span class="sm:hidden">Hapus</span></x-ui.button>
@endsection

@section('content')
<div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-12">
    {{-- Informasi Resto (Figma: DETAIL RESTO / EDIT RESTO). Nama field edit_* mengikuti UmkmController::update --}}
    <section class="rounded-2xl bg-surface p-5 shadow-card lg:col-span-7 lg:p-6" aria-labelledby="info-title">
        <h2 id="info-title" class="font-display text-xl font-bold tracking-tight text-ink">Informasi Resto</h2>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-brick-50 p-3 text-sm text-brick-700 shadow-hairline" role="alert">
                <ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('umkmUpdate', $umkm->id) }}" enctype="multipart/form-data" id="umkm-form" class="mt-5 space-y-5">
            @csrf
            @method('PUT')
            {{-- Tautan peta ikut diperbarui saat alamat dipilih dari saran Google --}}
            <input type="hidden" name="edit_link" id="edit_link" value="{{ old('edit_link', $umkm->link) }}">

            <x-ui.field label="Gambar Resto" for="logo_umkm">
                <x-ui.image-input name="logo_umkm" id="logo_umkm" label="Pilih gambar resto" icon="store" :current="$umkm->logo_umkm ? Storage::url($umkm->logo_umkm) : null" hint="Biarkan kosong untuk memakai gambar lama" />
            </x-ui.field>

            <x-ui.field label="Nama Resto" for="edit_nama_umkm" required :error="$errors->first('edit_nama_umkm')">
                <x-ui.input type="text" name="edit_nama_umkm" id="edit_nama_umkm" :value="old('edit_nama_umkm', $umkm->nama_umkm)" required :error="$errors->has('edit_nama_umkm')" />
            </x-ui.field>

            <x-ui.field label="Alamat" for="edit_alamat" required :error="$errors->first('edit_alamat')" hint="Ketik alamat lalu pilih dari saran supaya lokasi di peta ikut terisi.">
                <x-ui.input type="text" name="edit_alamat" id="edit_alamat" icon="map-pin" :value="old('edit_alamat', $umkm->alamat)" required autocomplete="off"
                            data-places data-geo-target="#edit_geo" data-link-target="#edit_link" :error="$errors->has('edit_alamat')" />
            </x-ui.field>

            <x-ui.field label="Koordinat" for="edit_geo" :error="$errors->first('edit_geo')" hint="Format lintang,bujur. Terisi otomatis saat alamat dipilih dari saran.">
                <x-ui.input type="text" name="edit_geo" id="edit_geo" :value="old('edit_geo', $umkm->geo)" class="font-mono" placeholder="-6.9738,107.6301" :error="$errors->has('edit_geo')" />
            </x-ui.field>

            <div class="grid grid-cols-2 gap-3">
                <x-ui.field label="Jam Buka" for="edit_open_time" required :error="$errors->first('edit_open_time')">
                    <x-ui.input type="time" name="edit_open_time" id="edit_open_time" :value="old('edit_open_time', $openTime)" required class="font-mono" :error="$errors->has('edit_open_time')" />
                </x-ui.field>
                <x-ui.field label="Jam Tutup" for="edit_close_time" required :error="$errors->first('edit_close_time')">
                    <x-ui.input type="time" name="edit_close_time" id="edit_close_time" :value="old('edit_close_time', $closeTime)" required class="font-mono" :error="$errors->has('edit_close_time')" />
                </x-ui.field>
            </div>

            <x-ui.field label="No. Telp Resto" for="edit_no_telp_umkm" required :error="$errors->first('edit_no_telp_umkm')">
                <x-ui.input type="tel" name="edit_no_telp_umkm" id="edit_no_telp_umkm" icon="phone" :value="old('edit_no_telp_umkm', $umkm->no_telp_umkm)" required class="font-mono" :error="$errors->has('edit_no_telp_umkm')" />
            </x-ui.field>

            <fieldset>
                <legend class="mb-1.5 text-sm font-semibold text-ink-700">VIP</legend>
                <div class="inline-flex rounded-xl bg-sunken p-1 shadow-inset">
                    @foreach ([['1', 'Ya'], ['0', 'Tidak']] as [$value, $label])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="edit_vip" value="{{ $value }}" id="{{ $value === '1' ? 'vip-ya' : 'vip-tidak' }}" class="peer sr-only" @checked($vip === $value) required>
                            <span class="ui-press flex h-9 min-w-20 items-center justify-center rounded-lg px-4 text-sm font-semibold text-ink-600 peer-checked:bg-surface peer-checked:text-ink peer-checked:shadow-card peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-brand-500">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1.5 text-sm text-ink-500">Resto VIP tampil sebagai unggulan dan mendapat benefit lainnya.</p>
                @error('edit_vip')<p class="mt-1 text-sm text-brick-700" role="alert">{{ $message }}</p>@enderror
            </fieldset>

            <dl class="grid grid-cols-2 gap-3 rounded-xl bg-sunken p-3 text-sm shadow-inset">
                <div><dt class="text-ink-500">Tanggal Registrasi</dt><dd class="mt-0.5 font-mono text-ink">{{ $umkm->created_at?->locale('id')->translatedFormat('j F Y') ?? '—' }}</dd></div>
                <div><dt class="text-ink-500">Kategori</dt><dd class="mt-0.5 flex flex-wrap gap-1">@forelse ($categories as $category)<x-ui.badge>{{ \App\Models\Menu::CATEGORIES[$category] ?? ucfirst($category) }}</x-ui.badge>@empty<span class="text-ink-500">Belum ada produk</span>@endforelse</dd></div>
            </dl>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button :href="route('umkm')" variant="outline">Kembali</x-ui.button>
                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </section>

    {{-- Daftar Produk --}}
    <section class="rounded-2xl bg-surface shadow-card lg:col-span-5" aria-labelledby="products-title">
        <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
            <div>
                <h2 id="products-title" class="font-display text-xl font-bold tracking-tight text-ink">Daftar Produk</h2>
                <p class="font-mono text-xs text-ink-500">{{ $products->count() }} produk</p>
            </div>
            <x-ui.button size="sm" icon="plus" data-modal-open="add-product">Tambah</x-ui.button>
        </div>

        @if ($products->isEmpty())
            <x-ui.empty-state icon="utensils" title="Belum ada produk" description="Tambahkan menu pertama agar resto ini bisa dipesan.">
                <x-ui.button size="sm" icon="plus" data-modal-open="add-product">Tambah Produk</x-ui.button>
            </x-ui.empty-state>
        @else
            <ul class="divide-y divide-line">
                @foreach ($products as $product)
                    @php($final = $product->harga * (1 - ((float) $product->diskon) / 100))
                    <li class="flex items-center gap-3 px-5 py-3.5">
                        <x-ui.photo :src="Storage::url($product->image)" :alt="$product->nama_makanan" class="size-16 shrink-0 rounded-xl" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-ink">{{ $product->nama_makanan }}</p>
                            <p class="text-sm text-ink-500">{{ \App\Models\Menu::CATEGORIES[$product->category] ?? ($product->category ?: 'Tanpa kategori') }}</p>
                            <p class="tnum mt-0.5 flex flex-wrap items-center gap-x-2 font-mono text-sm text-ink">
                                Rp {{ number_format($final, 0, ',', '.') }}
                                @if ($product->diskon > 0)
                                    <span class="text-xs text-ink-400 line-through">{{ number_format($product->harga, 0, ',', '.') }}</span>
                                    <x-ui.badge tone="brand">Diskon {{ rtrim(rtrim(number_format($product->diskon, 2, ',', ''), '0'), ',') }}%</x-ui.badge>
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" data-modal-open="edit-product" data-id="{{ $product->id }}" data-umkm="{{ $product->data_umkm_id }}" data-nama_makanan="{{ $product->nama_makanan }}"
                                    data-deskripsi="{{ $product->deskripsi }}" data-harga="{{ $product->harga }}" data-promo="{{ $product->diskon }}" data-category="{{ $product->category }}"
                                    data-update-url="{{ route('productUpdate', $product->id) }}" data-image="{{ Storage::url($product->image) }}"
                                    class="ui-press grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink" aria-label="Edit {{ $product->nama_makanan }}"><x-ui.icon name="pencil" class="size-[18px]" /></button>
                            <button type="button" data-modal-open="delete-product" data-nama="{{ $product->nama_makanan }}" data-url="{{ route('productDestroy', $product->id) }}"
                                    class="ui-press grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-brick-50 hover:text-brick-700" aria-label="Hapus {{ $product->nama_makanan }}"><x-ui.icon name="trash-2" class="size-[18px]" /></button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>

@include('pages.Admin.partials.product-modals', ['umkms' => $allUmkms, 'fixedUmkm' => $umkm])

{{-- Konfirmasi hapus resto --}}
<x-ui.modal name="delete-umkm" tone="danger" title="Hapus resto ini?" description="Resto dan semua produknya dihapus permanen. Riwayat transaksi tidak ikut terhapus.">
    <p class="rounded-xl bg-sunken px-3 py-2 text-[15px] font-semibold text-ink">{{ $umkm->nama_umkm }}</p>
    <x-slot:actions>
        <form method="POST" action="{{ route('umkmDestroy', $umkm->id) }}" class="contents">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger" block>Hapus</x-ui.button>
        </form>
        <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>

<x-admin.maps-script />
@endsection
