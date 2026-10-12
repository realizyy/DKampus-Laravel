<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Form Edit Menu</h1>

                    @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-100 p-3 text-sm text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-100 p-3 text-sm text-green-700">{{ session('success') }}</div>
                    @endif

                    {{-- Nama field (edit_*) mengikuti MenuController::update --}}
                    <form action="{{ route('productUpdate', $model->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_umkm">Nama UMKM:</label>
                                <select name="edit_umkm" id="edit_umkm" class="w-full px-3 py-2 border rounded-md nama_umkm text-black">
                                    @foreach ($umkm as $id => $nama)
                                    <option value="{{ $id }}" @selected(old('edit_umkm', $model->data_umkm_id) == $id)>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="edit_nama_makanan">Nama Makanan:</label>
                                <input type="text" name="edit_nama_makanan" id="edit_nama_makanan" value="{{ old('edit_nama_makanan', $model->nama_makanan) }}" class="text-black w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="edit_deskripsi">Deskripsi:</label>
                                <textarea name="edit_deskripsi" id="edit_deskripsi" class="text-black w-full rounded-md" required>{{ old('edit_deskripsi', $model->deskripsi) }}</textarea>
                            </div>
                            <div>
                                <label for="edit_harga">Harga:</label>
                                <input type="number" name="edit_harga" id="edit_harga" value="{{ old('edit_harga', $model->harga) }}" min="0" class="text-black w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="edit_promo">Diskon (%):</label>
                                <input type="number" name="edit_promo" id="edit_promo" value="{{ old('edit_promo', $model->diskon) }}" min="0" max="100" step="any" class="text-black w-full rounded-md">
                            </div>
                            <div>
                                <label for="edit_category">Kategori:</label>
                                <select name="edit_category" id="edit_category" class="w-full px-3 py-2 border rounded-md text-black" required>
                                    <option value="">-- Pilih kategori --</option>
                                    @foreach (['minuman' => 'Minuman', 'jajanan' => 'Jajanan', 'aneka nasi' => 'Aneka Nasi', 'ayam & bebek' => 'Ayam & Bebek', 'cepat saji' => 'Cepat Saji', 'roti' => 'Roti', 'bakso & soto' => 'Bakso & Soto', 'bakmie' => 'Bakmie', 'mie' => 'Mie', 'sate' => 'Sate'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('edit_category', $model->category) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="edit_image">Image Makanan:</label>
                                <input type="file" name="edit_image" id="edit_image" class="w-full" accept="image/*" onchange="previewEditImage()">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                                <div class="mt-2">
                                    <h2 class="text-sm font-semibold">Gambar saat ini / pratinjau:</h2>
                                    <img id="edit-preview" src="{{ Storage::url($model->image) }}" alt="Preview" class="mx-2 mt-1 max-w-xs">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                            <a href="{{ route('product') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewEditImage() {
            var input = document.getElementById('edit_image');
            var preview = document.getElementById('edit-preview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
