@extends('layouts.admin')

@php
    $categories = \App\Models\Menu::CATEGORIES;
    $oldProducts = collect(old('products', []))->values()->map(fn ($p) => [
        'nama_makanan' => $p['nama_makanan'] ?? '',
        'deskripsi' => $p['deskripsi'] ?? '',
        'harga' => $p['harga'] ?? '',
        'promo' => $p['promo'] ?? '',
        'category' => $p['category'] ?? '',
    ]);
    $vip = (string) old('vip', '0');
@endphp

@section('page-title', 'Tambah Resto')
@section('subtitle', 'Isi informasi resto, tambahkan produk, lalu simpan.')
@section('back', route('umkm'))

@section('content')
<form method="POST" action="{{ route('umkm.save') }}" enctype="multipart/form-data" id="umkmForm" novalidate
      x-data="restoWizard(@js($oldProducts))" x-ref="form" @submit="submitting = true"
      @keydown.enter="if ($event.target.tagName !== 'TEXTAREA' && $event.target.type !== 'submit') { $event.preventDefault(); step < 3 ? next() : $refs.form.requestSubmit() }"
      class="mx-auto max-w-3xl space-y-4">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    {{-- Langkah (Figma: Informasi Resto*, Produk*, Selesai) --}}
    <ol class="flex items-center gap-2" aria-label="Langkah">
        <template x-for="(label, i) in steps" :key="label">
            <li class="flex flex-1 items-center gap-2 last:flex-none">
                <button type="button" @click="jump(i + 1)" :aria-current="step === i + 1 ? 'step' : null"
                        class="ui-press flex items-center gap-2 rounded-xl py-1.5 pr-3 text-left" :class="step === i + 1 ? 'text-ink' : 'text-ink-500'">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full font-mono text-sm font-semibold shadow-hairline"
                          :class="step === i + 1 ? 'bg-brand-700 text-white' : (step > i + 1 ? 'bg-moss-600 text-white' : 'bg-surface text-ink-500')">
                        <template x-if="step > i + 1"><svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></template>
                        <template x-if="step <= i + 1"><span x-text="i + 1"></span></template>
                    </span>
                    <span class="text-sm font-semibold" :class="step === i + 1 ? '' : 'hidden sm:inline'" x-text="label + (i < 2 ? '*' : '')"></span>
                </button>
                <span x-show="i < 2" class="h-px flex-1 bg-line-strong" aria-hidden="true"></span>
            </li>
        </template>
    </ol>

    @if ($errors->any())
        <div class="rounded-2xl bg-brick-50 p-4 text-sm text-brick-700 shadow-hairline" role="alert">
            <p class="font-semibold">Resto belum tersimpan. Periksa isian berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @if ($oldProducts->isNotEmpty())<p class="mt-2">Gambar produk perlu dipilih ulang.</p>@endif
        </div>
    @endif

    {{-- 1. Informasi Resto --}}
    <section x-ref="step1" x-show="step === 1" class="space-y-5 rounded-2xl bg-surface p-5 shadow-card md:p-6" aria-labelledby="step1-title">
        <h2 id="step1-title" class="font-display text-xl font-bold tracking-tight text-ink">Informasi Resto</h2>

        <x-ui.field label="Gambar Resto" for="logo_umkm" required :error="$errors->first('logo_umkm')">
            <x-ui.image-input name="logo_umkm" id="logo_umkm" label="Pilih gambar resto" icon="store" required :error="$errors->has('logo_umkm')" />
        </x-ui.field>

        <x-ui.field label="Nama Resto" for="nama_umkm" required :error="$errors->first('nama_umkm')">
            <x-ui.input type="text" name="nama_umkm" id="nama_umkm" :value="old('nama_umkm')" required placeholder="Mis. Ayam Bakar Maknyos - Sukabirus" :error="$errors->has('nama_umkm')" />
        </x-ui.field>

        {{-- Figma: CARI ALAMAT RESTO / KONFIRMASI ALAMAT. Hook lama: #autocomplete, #link, #geo --}}
        <x-ui.field label="Alamat" for="autocomplete" required :error="$errors->first('alamat')" hint="Ketik alamat lalu pilih dari saran supaya lokasi di peta ikut terisi.">
            <x-ui.input type="text" name="alamat" id="autocomplete" icon="map-pin" :value="old('alamat')" required autocomplete="off" placeholder="Masukkan lokasi"
                        data-places data-geo-target="#geo" data-link-target="#link" :error="$errors->has('alamat')" />
            <input type="hidden" name="link" id="link" value="{{ old('link') }}">
        </x-ui.field>

        <x-ui.field label="Koordinat" for="geo" required :error="$errors->first('geo')" hint="Format lintang,bujur. Terisi otomatis saat alamat dipilih dari saran; bisa juga diisi manual.">
            <x-ui.input type="text" name="geo" id="geo" :value="old('geo')" required class="font-mono" placeholder="-6.9738,107.6301" :error="$errors->has('geo')" />
        </x-ui.field>

        <div class="grid grid-cols-2 gap-3">
            <x-ui.field label="Jam Buka" for="open_time" required :error="$errors->first('open_time')">
                <x-ui.input type="time" name="open_time" id="open_time" :value="old('open_time')" required class="font-mono" :error="$errors->has('open_time')" />
            </x-ui.field>
            <x-ui.field label="Jam Tutup" for="close_time" required :error="$errors->first('close_time')">
                <x-ui.input type="time" name="close_time" id="close_time" :value="old('close_time')" required class="font-mono" :error="$errors->has('close_time')" />
            </x-ui.field>
        </div>

        <x-ui.field label="No. Telp Resto" for="no_telp_umkm" required :error="$errors->first('no_telp_umkm')">
            <x-ui.input type="tel" name="no_telp_umkm" id="no_telp_umkm" icon="phone" :value="old('no_telp_umkm')" required class="font-mono" placeholder="08xxxxxxxxxx" :error="$errors->has('no_telp_umkm')" />
        </x-ui.field>

        <fieldset>
            <legend class="mb-1.5 text-sm font-semibold text-ink-700">VIP</legend>
            <div class="inline-flex rounded-xl bg-sunken p-1 shadow-inset">
                @foreach ([['1', 'Ya'], ['0', 'Tidak']] as [$value, $label])
                    <label class="relative cursor-pointer">
                        <input type="radio" name="vip" value="{{ $value }}" id="{{ $value === '1' ? 'vip-ya' : 'vip-tidak' }}" class="peer sr-only" @checked($vip === $value) required>
                        <span class="ui-press flex h-9 min-w-20 items-center justify-center rounded-lg px-4 text-sm font-semibold text-ink-600 peer-checked:bg-surface peer-checked:text-ink peer-checked:shadow-card peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-brand-500">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <p class="mt-1.5 text-sm text-ink-500">Resto VIP tampil sebagai unggulan dan mendapat benefit lainnya.</p>
        </fieldset>
    </section>

    {{-- 2. Produk --}}
    <section x-ref="step2" x-show="step === 2" x-cloak class="space-y-4" aria-labelledby="step2-title">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 id="step2-title" class="font-display text-xl font-bold tracking-tight text-ink">Produk</h2>
                <p class="text-sm text-ink-500">Minimal satu produk supaya resto bisa dipesan.</p>
            </div>
            <x-ui.button size="sm" icon="plus" @click="addProduct()">Tambah Produk</x-ui.button>
        </div>

        <div x-show="products.length === 0" class="rounded-2xl bg-surface shadow-card">
            <x-ui.empty-state icon="utensils" title="Belum ada produk" description="Tambahkan menu pertama untuk resto ini.">
                <x-ui.button size="sm" icon="plus" @click="addProduct()">Tambah Produk</x-ui.button>
            </x-ui.empty-state>
        </div>

        <template x-for="(p, i) in products" :key="p.key">
            <article class="space-y-4 rounded-2xl bg-surface p-5 shadow-card md:p-6">
                <header class="flex items-center justify-between">
                    <h3 class="font-display text-base font-bold tracking-tight text-ink">Produk <span class="font-mono" x-text="i + 1"></span></h3>
                    <button type="button" @click="removeProduct(i)" class="ui-press inline-flex h-9 items-center gap-1.5 rounded-lg px-2.5 text-sm font-semibold text-ink-500 hover:bg-brick-50 hover:text-brick-700">
                        <x-ui.icon name="trash-2" class="size-4" />Hapus
                    </button>
                </header>

                <div x-data="{ src: null, file: '' }">
                    <span class="mb-1.5 block text-sm font-semibold text-ink-700">Gambar Produk<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span></span>
                    <label class="ui-press flex cursor-pointer items-center gap-4 rounded-2xl bg-surface p-3 shadow-inset ring-1 ring-inset ring-line hover:bg-canvas hover:ring-line-strong">
                        <input type="file" x-bind:name="`products[${i}][image]`" accept="image/jpeg,image/png,image/gif,image/webp" required class="peer sr-only"
                               @change="const f = $event.target.files[0]; file = f ? f.name : ''; if (f) { src = URL.createObjectURL(f) }">
                        <span class="relative grid size-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-sunken text-ink-300 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-brand-500">
                            <x-ui.icon name="utensils" class="size-6" x-show="!src" />
                            <img x-show="src" x-cloak :src="src" alt="" class="absolute inset-0 size-full object-cover" @@error="src = null">
                        </span>
                        <span class="min-w-0">
                            <span class="block text-[15px] font-semibold text-ink" x-text="src ? 'Ganti gambar' : 'Pilih gambar produk'"></span>
                            <span class="block truncate font-mono text-xs text-ink-500" x-text="file || 'JPG atau PNG, maksimal 2 MB'"></span>
                        </span>
                    </label>
                </div>

                <label class="block space-y-1.5">
                    <span class="block text-sm font-semibold text-ink-700">Nama Makanan<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span></span>
                    <x-ui.input type="text" x-bind:name="`products[${i}][nama_makanan]`" x-model="p.nama_makanan" required placeholder="Mis. Ayam Bakar Signature" />
                </label>
                <label class="block space-y-1.5">
                    <span class="block text-sm font-semibold text-ink-700">Deskripsi<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span></span>
                    <x-ui.textarea x-bind:name="`products[${i}][deskripsi]`" x-model="p.deskripsi" rows="2" required placeholder="Bahan, rasa, atau ukuran porsi" />
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block space-y-1.5">
                        <span class="block text-sm font-semibold text-ink-700">Harga (Rp)<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span></span>
                        <x-ui.input type="number" x-bind:name="`products[${i}][harga]`" x-model="p.harga" min="0" step="500" required inputmode="numeric" class="font-mono" />
                    </label>
                    <label class="block space-y-1.5">
                        <span class="block text-sm font-semibold text-ink-700">Diskon (%)</span>
                        <x-ui.input type="number" x-bind:name="`products[${i}][promo]`" x-model="p.promo" min="0" max="100" step="any" inputmode="decimal" class="font-mono" />
                    </label>
                </div>
                <label class="block space-y-1.5">
                    <span class="block text-sm font-semibold text-ink-700">Kategori<span class="ml-0.5 text-brick-600" aria-hidden="true">*</span></span>
                    <x-ui.select x-bind:name="`products[${i}][category]`" x-model="p.category" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </x-ui.select>
                </label>
            </article>
        </template>
    </section>

    {{-- 3. Selesai --}}
    <section x-show="step === 3" x-cloak class="space-y-4 rounded-2xl bg-surface p-5 shadow-card md:p-6" aria-labelledby="step3-title">
        <div>
            <h2 id="step3-title" class="font-display text-xl font-bold tracking-tight text-ink">Selesai</h2>
            <p class="text-sm text-ink-500">Periksa ringkasan di bawah, lalu simpan resto.</p>
        </div>
        <dl class="divide-y divide-line rounded-xl bg-sunken px-4 shadow-inset text-[15px]">
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">Nama Resto</dt><dd class="text-right font-semibold text-ink" x-text="summary.nama || '—'"></dd></div>
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">Alamat</dt><dd class="text-right text-ink" x-text="summary.alamat || '—'"></dd></div>
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">Koordinat</dt><dd class="text-right font-mono text-sm text-ink" x-text="summary.geo || '—'"></dd></div>
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">Jam buka</dt><dd class="text-right font-mono text-sm text-ink" x-text="(summary.buka || '--:--') + ' – ' + (summary.tutup || '--:--')"></dd></div>
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">No. Telp</dt><dd class="text-right font-mono text-sm text-ink" x-text="summary.telp || '—'"></dd></div>
            <div class="flex justify-between gap-4 py-2.5"><dt class="text-ink-500">VIP</dt><dd class="text-right text-ink" x-text="summary.vip === '1' ? 'Ya' : 'Tidak'"></dd></div>
        </dl>
        <div>
            <h3 class="mb-2 text-sm font-semibold text-ink-700">Produk (<span x-text="products.length"></span>)</h3>
            <ul class="divide-y divide-line rounded-xl bg-sunken px-4 shadow-inset">
                <template x-for="p in products" :key="p.key">
                    <li class="flex items-center justify-between gap-3 py-2.5 text-[15px]">
                        <span class="min-w-0 truncate font-semibold text-ink" x-text="p.nama_makanan || 'Tanpa nama'"></span>
                        <span class="shrink-0 font-mono text-sm text-ink-600" x-text="'Rp ' + Number(p.harga || 0).toLocaleString('id-ID')"></span>
                    </li>
                </template>
            </ul>
        </div>
    </section>

    {{-- Navigasi langkah --}}
    <div class="flex items-center justify-between gap-3">
        <x-ui.button variant="outline" x-show="step > 1" @click="go(step - 1)" icon="arrow-left">Kembali</x-ui.button>
        <x-ui.button :href="route('umkm')" variant="ghost" x-show="step === 1">Batal</x-ui.button>
        <x-ui.button x-show="step < 3" @click="next()" icon-right="arrow-right" class="ml-auto">Lanjut</x-ui.button>
        <x-ui.button type="submit" x-show="step === 3" x-cloak ::disabled="submitting" class="ml-auto">Tambah Resto</x-ui.button>
    </div>
</form>

<x-admin.maps-script />
@endsection

@push('js')
<script>
document.addEventListener('alpine:init', () => {
    let seq = 0;
    Alpine.data('restoWizard', (initial) => ({
        step: 1,
        submitting: false,
        steps: ['Informasi Resto', 'Produk', 'Selesai'],
        products: initial.map((p) => ({ key: ++seq, ...p })),
        summary: {},
        // Semua kolom langkah yang sedang tampil valid? (pesan galat bawaan browser muncul di kolom pertama yang salah)
        stepValid(n) {
            return [...this.$refs['step' + n].querySelectorAll('input, select, textarea')].every((f) => f.reportValidity());
        },
        next() {
            if (!this.stepValid(this.step)) return;
            if (this.step === 2 && this.products.length === 0) {
                DKToast.error('Tambahkan minimal satu produk.');
                return;
            }
            this.go(this.step + 1);
        },
        jump(n) {
            // Boleh mundur kapan saja; maju hanya lewat tombol Lanjut supaya tiap langkah tervalidasi
            if (n < this.step) this.go(n);
        },
        go(n) {
            this.step = n;
            if (n === 3) this.review();
            this.$nextTick(() => window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));
        },
        addProduct() {
            this.products.push({ key: ++seq, nama_makanan: '', deskripsi: '', harga: '', promo: '', category: '' });
            this.$nextTick(() => {
                const cards = this.$refs.step2.querySelectorAll('article');
                const last = cards[cards.length - 1];
                last?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                last?.querySelector('input[type=text]')?.focus({ preventScroll: true });
            });
        },
        removeProduct(i) { this.products.splice(i, 1); },
        review() {
            const f = this.$root.elements;
            this.summary = {
                nama: f.nama_umkm.value, alamat: f.alamat.value, geo: f.geo.value,
                buka: f.open_time.value, tutup: f.close_time.value, telp: f.no_telp_umkm.value,
                vip: (this.$root.querySelector('input[name=vip]:checked') || {}).value,
            };
        },
    }));
});
</script>
@endpush
