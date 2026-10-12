@extends('layouts.customer')

@php
    $addresses = collect($AddressList ?? [])->map(fn ($a) => ['id' => $a['id'], 'name' => $a['nama_alamat'], 'address' => $a['address']])->values()->all();
    $utama = $alamatUtama ?? null;
@endphp

@section('page-title', 'Jastip')
@section('back', url('/'))
@section('hide-nav', '1')

@section('content')
<form action="{{ route('jastip.order') }}" method="POST" class="mx-auto max-w-3xl space-y-5 pb-28 md:pb-0"
      x-data="{
          warungs: [{ name: '', items: [{ name: '', qty: 1 }] }],
          addr: @js($utama ? ['id' => $utama->id, 'name' => $utama->nama_alamat] : ['id' => '', 'name' => 'Masukan Alamat']),
          addItem(w) { w.items.push({ name: '', qty: 1 }) },
          removeItem(w, i) { if (w.items.length > 1) w.items.splice(i, 1) },
          addWarung() { this.warungs.push({ name: '', items: [{ name: '', qty: 1 }] }) },
          removeWarung(i) { if (this.warungs.length > 1) this.warungs.splice(i, 1) },
      }">
    @csrf
    <x-ui.page-header title="Dkampus Jasa Titip" subtitle="Titip beli makanan atau barang, kurir yang antar." class="hidden md:flex" />
    <div class="md:hidden">
        <p class="font-mono text-xs uppercase tracking-widest text-ink-500">Jastip</p>
        <h1 class="font-display text-3xl font-extrabold leading-tight tracking-tightest text-ink">Dkampus Jasa Titip</h1>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-brick-50 p-3 text-[15px] text-brick-700" role="alert"><ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    {{-- Jenis jastip --}}
    <fieldset class="flex items-center justify-between gap-3">
        <legend class="sr-only">Jenis Jastip</legend>
        <span class="font-semibold text-ink-700">Jenis Jastip</span>
        <div class="inline-flex rounded-xl bg-sunken p-1 shadow-inset">
            @foreach (['makanan' => 'Makanan', 'barang' => 'Barang'] as $v => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="jenisJastip" id="{{ $v === 'makanan' ? 'jenisJastip' : 'jenisJastipBarang' }}" value="{{ $v }}" class="peer sr-only" @checked(old('jenisJastip', 'makanan') === $v)>
                    <span class="ui-press block min-w-24 rounded-lg px-4 py-2 text-center text-[15px] font-semibold text-ink-500 peer-checked:bg-surface peer-checked:text-ink peer-checked:shadow-card peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-brand-500">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </fieldset>

    {{-- Warung dan menu --}}
    <div id="warungContainer" class="space-y-4">
        <template x-for="(w, wi) in warungs" :key="wi">
            <x-ui.card class="warung space-y-4">
                <div class="flex items-end gap-2">
                    <div class="min-w-0 flex-1 space-y-1.5">
                        <label class="block text-sm font-semibold text-ink-700" :for="'warung' + wi">Nama Warung</label>
                        <x-ui.input ::id="'warung' + wi" name="warung[]" x-model="w.name" placeholder="Nama UMKM pilihanmu" required />
                    </div>
                    <x-ui.button variant="ghost" class="!px-0 size-12 shrink-0" x-show="warungs.length > 1" @click="removeWarung(wi)" aria-label="Hapus warung"><x-ui.icon name="trash-2" class="size-5" /></x-ui.button>
                </div>
                <div class="menuContainer space-y-2">
                    <p class="text-sm font-semibold text-ink-700">Menu / Barang</p>
                    <template x-for="(it, ii) in w.items" :key="ii">
                        <div class="menu flex items-center gap-2">
                            <input type="text" :name="'nama_menu[' + wi + '][]'" x-model="it.name" placeholder="Nama Menu/Barang" required class="block h-12 min-w-0 flex-1 rounded-xl border-0 bg-surface px-4 text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line placeholder:text-ink-400 hover:ring-line-strong focus:ring-2 focus:ring-brand-600">
                            <input type="number" :name="'kuantitas[' + wi + '][]'" x-model.number="it.qty" min="1" placeholder="Jumlah" required aria-label="Jumlah" class="tnum block h-12 w-20 rounded-xl border-0 bg-surface px-3 text-center font-mono text-[15px] text-ink shadow-inset ring-1 ring-inset ring-line hover:ring-line-strong focus:ring-2 focus:ring-brand-600">
                            <x-ui.button variant="ghost" class="!px-0 size-12 shrink-0" x-show="w.items.length > 1" @click="removeItem(w, ii)" aria-label="Hapus menu"><x-ui.icon name="minus" class="size-5" /></x-ui.button>
                        </div>
                    </template>
                    <x-ui.button variant="soft" size="sm" icon="plus" @click="addItem(w)">Tambah Menu</x-ui.button>
                </div>
            </x-ui.card>
        </template>
        <x-ui.button variant="secondary" icon="plus" @click="addWarung()">Tambah Warung</x-ui.button>
    </div>

    <x-ui.card class="space-y-4">
        <x-ui.field label="Alamat Warung" for="alamat" hint="Cari alamat warung agar jarak dan ongkir akurat.">
            <x-ui.input id="alamat" name="alamat" icon="map-pin" placeholder="Ketik untuk mencari alamat" autocomplete="off" />
        </x-ui.field>
        <input type="hidden" id="geo" name="geo">
        <input type="hidden" id="link" name="link">
        <x-ui.field label="Catatan" for="catatan"><x-ui.textarea id="catatan" name="catatan" placeholder="Opsional"></x-ui.textarea></x-ui.field>
    </x-ui.card>

    {{-- Alamat tujuan + kirim --}}
    <input type="hidden" id="selected_address_id" name="selected_address_id" :value="addr.id">
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur md:static md:z-auto md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
        <div class="mx-auto flex max-w-3xl items-center gap-3">
            <button type="button" id="alamatshow" data-modal-open="alamat-jastip" class="ui-press flex min-w-0 flex-1 items-center gap-3 rounded-xl bg-surface p-2.5 text-left shadow-card hover:bg-sunken">
                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-800"><x-ui.icon name="map-pin" class="size-[18px]" /></span>
                <span class="min-w-0 flex-1"><span class="block font-mono text-[10px] uppercase tracking-widest text-ink-500">Diantar ke</span><span class="block truncate text-[15px] font-semibold text-ink" id="selected_address" x-text="addr.name"></span></span>
            </button>
            <x-ui.button type="submit" size="lg" class="shrink-0">Kirim</x-ui.button>
        </div>
    </div>

    <x-ui.modal name="alamat-jastip" size="md" icon="map-pin" title="Alamat Pengantaran" description="Pilih alamat tujuan pesananmu.">
        @if (count($addresses))
            <ul class="space-y-2">
                @foreach ($addresses as $a)
                    <li><button type="button" class="address-button ui-press flex w-full items-start gap-3 rounded-xl p-3 text-left shadow-hairline hover:bg-sunken" data-id="{{ $a['id'] }}" data-name="{{ $a['name'] }}" @click="addr = { id: '{{ $a['id'] }}', name: @js($a['name']) }; dkModal.close('alamat-jastip')">
                        <x-ui.icon name="map-pin" class="mt-0.5 size-5 shrink-0 text-brand-600" /><span class="min-w-0"><span class="block font-semibold text-ink">{{ $a['name'] }}</span><span class="block text-sm text-ink-500">{{ $a['address'] }}</span></span>
                    </button></li>
                @endforeach
            </ul>
        @else
            <p class="text-[15px] text-ink-600">Silahkan tambahkan alamat dulu.</p>
        @endif
        <x-slot:actions><x-ui.button :href="url('/daftar-alamat')" variant="secondary" icon="plus" block>Tambah / Kelola Alamat</x-ui.button></x-slot:actions>
    </x-ui.modal>
</form>
@endsection

@push('js')
<x-customer.maps-script />
<script>
    // Google Places untuk alamat warung (aktif bila GOOGLE_MAPS_API_KEY terisi)
    function initAutocomplete() {
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('alamat'));
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry || !place.place_id) return;
            document.getElementById('geo').value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
            document.getElementById('link').value = place.url || '';
        });
    }
    window.initAutocomplete = initAutocomplete;
</script>
@endpush
