@extends('layouts.admin')

@section('page-title', 'Edit Produk')
@section('subtitle', $model->nama_makanan)
@section('back', route('product'))

@section('content')
<section class="mx-auto max-w-2xl rounded-2xl bg-surface p-5 shadow-card md:p-6" aria-labelledby="edit-title">
    <h2 id="edit-title" class="font-display text-xl font-bold tracking-tight text-ink">Form Edit Menu</h2>

    @if ($errors->any())
        <div class="mt-4 rounded-xl bg-brick-50 p-3 text-sm text-brick-700 shadow-hairline" role="alert">
            <ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Nama field (edit_*) mengikuti MenuController::update --}}
    <form action="{{ route('productUpdate', $model->id) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-5">
        @csrf
        @method('PUT')

        <x-ui.field label="Gambar Produk" for="edit_image">
            <x-ui.image-input name="edit_image" id="edit_image" label="Pilih gambar produk" icon="utensils" :current="$model->image ? Storage::url($model->image) : null" hint="Biarkan kosong untuk memakai gambar lama" />
        </x-ui.field>

        <x-ui.field label="Nama UMKM" for="edit_umkm" required :error="$errors->first('edit_umkm')">
            <x-ui.select name="edit_umkm" id="edit_umkm" required>
                @foreach ($umkm as $id => $nama)
                    <option value="{{ $id }}" @selected(old('edit_umkm', $model->data_umkm_id) == $id)>{{ $nama }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>

        <x-ui.field label="Nama Makanan" for="edit_nama_makanan" required :error="$errors->first('edit_nama_makanan')">
            <x-ui.input type="text" name="edit_nama_makanan" id="edit_nama_makanan" :value="old('edit_nama_makanan', $model->nama_makanan)" required :error="$errors->has('edit_nama_makanan')" />
        </x-ui.field>

        <x-ui.field label="Deskripsi" for="edit_deskripsi" required :error="$errors->first('edit_deskripsi')">
            <x-ui.textarea name="edit_deskripsi" id="edit_deskripsi" rows="4" required :error="$errors->has('edit_deskripsi')">{{ old('edit_deskripsi', $model->deskripsi) }}</x-ui.textarea>
        </x-ui.field>

        <div class="grid grid-cols-2 gap-3">
            <x-ui.field label="Harga (Rp)" for="edit_harga" required :error="$errors->first('edit_harga')">
                <x-ui.input type="number" name="edit_harga" id="edit_harga" :value="old('edit_harga', $model->harga)" min="0" step="500" required inputmode="numeric" class="font-mono" :error="$errors->has('edit_harga')" />
            </x-ui.field>
            <x-ui.field label="Diskon (%)" for="edit_promo" :error="$errors->first('edit_promo')">
                <x-ui.input type="number" name="edit_promo" id="edit_promo" :value="old('edit_promo', $model->diskon)" min="0" max="100" step="any" inputmode="decimal" class="font-mono" :error="$errors->has('edit_promo')" />
            </x-ui.field>
        </div>

        <x-ui.field label="Kategori" for="edit_category" required :error="$errors->first('edit_category')">
            <x-ui.select name="edit_category" id="edit_category" required>
                <option value="">Pilih kategori</option>
                @foreach (\App\Models\Menu::CATEGORIES as $value => $label)
                    <option value="{{ $value }}" @selected(old('edit_category', $model->category) === $value)>{{ $label }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-ui.button :href="route('product')" variant="outline">Batal</x-ui.button>
            <x-ui.button type="submit">Simpan</x-ui.button>
        </div>
    </form>
</section>
@endsection
