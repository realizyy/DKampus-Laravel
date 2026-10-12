{{--
    Panel tambah/edit produk + konfirmasi hapus, dipakai halaman Produk dan Detail Resto.
    Parameter: $umkms (koleksi resto untuk pilihan), $fixedUmkm (opsional: resto terkunci, mis. dari halaman detail).
    Hook yang dipertahankan: umkm, nama_makanan, deskripsi, image, harga, promo, category (tambah) dan edit_* (ubah), data-update-url.
--}}
@php
    $fixedUmkm = $fixedUmkm ?? null;
    $categories = \App\Models\Menu::CATEGORIES;
@endphp

{{-- Tambah produk --}}
<x-ui.sheet name="add-product" title="Tambah Produk" :description="$fixedUmkm ? $fixedUmkm->nama_umkm : 'Menu baru untuk salah satu resto'">
    <form action="{{ route('save.product') }}" method="POST" enctype="multipart/form-data" id="add-product-form" class="space-y-4">
        @csrf
        @if ($fixedUmkm)
            <input type="hidden" name="umkm" id="umkm" value="{{ $fixedUmkm->id }}">
        @else
            <x-ui.field label="Resto" for="umkm" required>
                <x-ui.select name="umkm" id="umkm" required>
                    @foreach ($umkms as $umkm)<option value="{{ $umkm->id }}" @selected(old('umkm') == $umkm->id)>{{ $umkm->nama_umkm }}</option>@endforeach
                </x-ui.select>
            </x-ui.field>
        @endif
        <x-ui.field label="Gambar Produk" for="image" required><x-ui.image-input name="image" id="image" label="Pilih gambar produk" icon="utensils" required /></x-ui.field>
        <x-ui.field label="Nama Makanan" for="nama_makanan" required><x-ui.input type="text" name="nama_makanan" id="nama_makanan" :value="old('nama_makanan')" required placeholder="Mis. Ayam Bakar Signature" /></x-ui.field>
        <x-ui.field label="Deskripsi" for="deskripsi" required><x-ui.textarea name="deskripsi" id="deskripsi" rows="3" required placeholder="Bahan, rasa, atau ukuran porsi">{{ old('deskripsi') }}</x-ui.textarea></x-ui.field>
        <div class="grid grid-cols-2 gap-3">
            <x-ui.field label="Harga (Rp)" for="harga" required><x-ui.input type="number" name="harga" id="harga" min="0" step="500" :value="old('harga')" required inputmode="numeric" class="font-mono" /></x-ui.field>
            <x-ui.field label="Diskon (%)" for="promo"><x-ui.input type="number" name="promo" id="promo" min="0" max="100" step="any" :value="old('promo')" inputmode="decimal" class="font-mono" /></x-ui.field>
        </div>
        <x-ui.field label="Kategori" for="category" required>
            <x-ui.select name="category" id="category" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $value => $label)<option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>@endforeach
            </x-ui.select>
        </x-ui.field>
    </form>
    <x-slot:footer>
        <div class="flex gap-2">
            <x-ui.button variant="outline" data-modal-close class="flex-1">Batal</x-ui.button>
            <x-ui.button type="submit" form="add-product-form" class="flex-1">Tambah</x-ui.button>
        </div>
    </x-slot:footer>
</x-ui.sheet>

{{-- Edit produk (diisi dari tombol Edit yang diklik) --}}
<x-ui.sheet name="edit-product" title="Edit Produk" description="Kosongkan gambar bila tidak ingin menggantinya">
    <form action="{{ route('productUpdate', ['id' => 0]) }}" method="POST" enctype="multipart/form-data" id="edit-product-form" class="space-y-4">
        @csrf
        @method('PUT')
        <x-ui.field label="Gambar Produk" for="edit_image"><x-ui.image-input name="edit_image" id="edit_image" label="Pilih gambar produk" icon="utensils" hint="Biarkan kosong untuk memakai gambar lama" /></x-ui.field>
        <x-ui.field label="Resto" for="edit_umkm" required>
            <x-ui.select name="edit_umkm" id="edit_umkm" required>
                @foreach ($umkms as $umkm)<option value="{{ $umkm->id }}">{{ $umkm->nama_umkm }}</option>@endforeach
            </x-ui.select>
        </x-ui.field>
        <x-ui.field label="Nama Makanan" for="edit_nama_makanan" required><x-ui.input type="text" name="edit_nama_makanan" id="edit_nama_makanan" required /></x-ui.field>
        <x-ui.field label="Deskripsi" for="edit_deskripsi" required><x-ui.textarea name="edit_deskripsi" id="edit_deskripsi" rows="3" required></x-ui.textarea></x-ui.field>
        <div class="grid grid-cols-2 gap-3">
            <x-ui.field label="Harga (Rp)" for="edit_harga" required><x-ui.input type="number" name="edit_harga" id="edit_harga" min="0" step="500" required inputmode="numeric" class="font-mono" /></x-ui.field>
            <x-ui.field label="Diskon (%)" for="edit_promo"><x-ui.input type="number" name="edit_promo" id="edit_promo" min="0" max="100" step="any" inputmode="decimal" class="font-mono" /></x-ui.field>
        </div>
        <x-ui.field label="Kategori" for="edit_category" required>
            <x-ui.select name="edit_category" id="edit_category" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
            </x-ui.select>
        </x-ui.field>
    </form>
    <x-slot:footer>
        <div class="flex gap-2">
            <x-ui.button variant="outline" data-modal-close class="flex-1">Batal</x-ui.button>
            <x-ui.button type="submit" form="edit-product-form" class="flex-1">Simpan</x-ui.button>
        </div>
    </x-slot:footer>
</x-ui.sheet>

{{-- Konfirmasi hapus produk --}}
<x-ui.modal name="delete-product" tone="danger" title="Hapus produk ini?" description="Produk dihapus permanen dari resto. Resto harus punya minimal satu produk.">
    <p class="rounded-xl bg-sunken px-3 py-2 text-[15px] font-semibold text-ink" id="delete-product-name">—</p>
    <x-slot:actions>
        <form method="POST" action="#" id="delete-product-form" class="contents">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger" block>Hapus</x-ui.button>
        </form>
        <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>

@push('js')
<script>
(function () {
    var byId = function (id) { return document.getElementById(id); };

    // Isi panel edit dari tombol Edit (menggantikan editProduct(this) inline)
    byId('modal-edit-product').addEventListener('modal:opening', function (event) {
        var d = event.detail;
        byId('edit-product-form').action = d.updateUrl;
        byId('edit_umkm').value = d.umkm;
        byId('edit_nama_makanan').value = d.nama_makanan;
        byId('edit_deskripsi').value = d.deskripsi;
        byId('edit_harga').value = d.harga;
        byId('edit_promo').value = d.promo;
        byId('edit_category').value = d.category;
        // Reset pilihan gambar dari edit sebelumnya
        byId('edit_image').value = '';
        var wrap = byId('edit_image').closest('[x-data]');
        if (wrap && window.Alpine) { Alpine.$data(wrap).src = d.image || null; Alpine.$data(wrap).file = ''; }
    });

    byId('modal-delete-product').addEventListener('modal:opening', function (event) {
        byId('delete-product-name').textContent = event.detail.nama;
        byId('delete-product-form').action = event.detail.url;
    });

    // Galat validasi dari kiriman sebelumnya: buka kembali panel tambah supaya isiannya terlihat
    @if ($errors->any() && old('nama_makanan') !== null)
        window.addEventListener('DOMContentLoaded', function () { dkModal.open('add-product'); });
    @endif
})();
</script>
@endpush
