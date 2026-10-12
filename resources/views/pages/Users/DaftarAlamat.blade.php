@extends('layouts.customer')

@section('page-title', 'Daftar Alamat')
@section('back', url('/'))
@section('hide-nav', '1')

@section('header-actions')
    <x-ui.button size="sm" icon="plus" id="tambahAlamat" data-modal-open="tambah-alamat">Tambah</x-ui.button>
@endsection

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <x-ui.page-header title="Daftar Alamat" subtitle="Atur alamat tujuan pengantaran pesananmu." class="hidden md:flex">
        <x-ui.button icon="plus" data-modal-open="tambah-alamat">Tambah Alamat</x-ui.button>
    </x-ui.page-header>

    @if ($errors->any())
        <div class="rounded-xl bg-brick-50 p-3 text-[15px] text-brick-700" role="alert"><ul class="list-disc space-y-0.5 pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @if ($alamatUser->isEmpty())
        <x-ui.card class="!p-0"><x-ui.empty-state icon="map-pin" title="Belum ada alamat" description="Tambahkan alamat agar pesananmu bisa diantar."><x-ui.button icon="plus" data-modal-open="tambah-alamat">Tambah Alamat</x-ui.button></x-ui.empty-state></x-ui.card>
    @else
        <ul class="grid gap-3 md:grid-cols-2" x-motion.rise.stagger>
            @foreach ($alamatUser as $key => $alamat)
                <li class="flex flex-col rounded-2xl bg-surface p-4 shadow-card">
                    <div id="openModalViewDetails{{ $key }}" class="flex-1 space-y-1">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-display text-lg font-bold tracking-tight text-ink">{{ $alamat['nama_alamat'] }}</h2>
                            @if ($alamat['utama'] == 1)<x-ui.badge tone="brand" dot>Alamat Utama</x-ui.badge>@endif
                        </div>
                        <p class="text-[15px] leading-snug text-ink-600">{{ $alamat['address'] }}</p>
                        @if (! empty($alamat['notes']))<p class="text-sm text-ink-500">Catatan: {{ $alamat['notes'] }}</p>@endif
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-line/70 pt-3">
                        @if ($alamat['utama'] != 1)
                            <form action="{{ route('alamatUtama') }}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $alamat['id'] }}" name="id">
                                <input type="hidden" value="{{ $alamat['user_id'] }}" name="custId">
                                <x-ui.button type="submit" size="sm" variant="soft">Set As Default</x-ui.button>
                            </form>
                        @endif
                        @if (! empty($alamat['link']))
                            <x-ui.button :href="$alamat['link']" target="_blank" rel="noopener" size="sm" variant="ghost" icon="external-link">Lihat di Maps</x-ui.button>
                        @endif
                        <x-ui.button size="sm" variant="danger-soft" icon="trash-2" class="ml-auto" data-modal-open="hapus-alamat" data-alamat-id="{{ $alamat['id'] }}" data-alamat-user="{{ $alamat['user_id'] }}" data-alamat-nama="{{ $alamat['nama_alamat'] }}">Hapus</x-ui.button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

{{-- Tambah alamat --}}
<x-ui.modal name="tambah-alamat" size="md" icon="map-pin" title="Tambah Alamat" description="Cari alamat lengkap atau tulis manual.">
    <form action="{{ route('daftar.alamat') }}" method="POST" class="space-y-4" id="tambahAlamatModal">
        @csrf
        <x-ui.field label="Nama" for="namaAlamat" hint="Mis. Kost Pondok Eden" required><x-ui.input id="namaAlamat" name="namaAlamat" :value="old('namaAlamat')" required /></x-ui.field>
        <x-ui.field label="Alamat Lengkap" for="autocomplete" required><x-ui.input id="autocomplete" name="address" :value="old('address')" icon="search" placeholder="Ketik untuk mencari alamat" autocomplete="off" required /></x-ui.field>
        <x-ui.field label="Catatan" for="notes" hint="Tuliskan alamat lengkap jika tidak terdaftar"><x-ui.textarea id="notes" name="notes" placeholder="Opsional">{{ old('notes') }}</x-ui.textarea></x-ui.field>
        <input type="hidden" id="linkGmaps" name="link">
        <input type="hidden" id="geoMaps" name="geo">
        <div class="flex flex-col gap-2 pt-1">
            <x-ui.button type="submit" block>Simpan</x-ui.button>
            <x-ui.button variant="outline" data-modal-close block>Tutup</x-ui.button>
        </div>
    </form>
</x-ui.modal>

{{-- Hapus alamat (modal di luar form; form dikirim dari tombol) --}}
<x-ui.modal name="hapus-alamat" tone="danger" title="Hapus alamat?" description="Alamat ini akan dihapus dari daftarmu.">
    <p id="hapus-alamat-nama" class="rounded-xl bg-sunken p-3 text-center font-semibold text-ink"></p>
    <x-slot:actions>
        <x-ui.button variant="danger" block onclick="document.getElementById('formHapusAlamat').submit()">Hapus</x-ui.button>
        <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
<form id="formHapusAlamat" action="{{ route('delete.alamat') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="id" id="hapusAlamatId">
    <input type="hidden" name="custId" id="hapusAlamatUser">
</form>
@endsection

@push('js')
<x-customer.maps-script />
<script>
    // Isi modal hapus dari tombol yang diklik
    document.getElementById('modal-hapus-alamat').addEventListener('modal:opening', function (event) {
        var d = event.detail || {};
        document.getElementById('hapusAlamatId').value = d.alamatId || '';
        document.getElementById('hapusAlamatUser').value = d.alamatUser || '';
        document.getElementById('hapus-alamat-nama').textContent = d.alamatNama || '';
    });
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () { dkModal.open('tambah-alamat'); });
    @endif

    // Google Places Autocomplete (aktif bila GOOGLE_MAPS_API_KEY terisi)
    function initAutocomplete() {
        var input = document.getElementById('autocomplete');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry || !place.place_id) return;
            document.getElementById('linkGmaps').value = place.url || '';
            document.getElementById('geoMaps').value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
        });
    }
    window.initAutocomplete = initAutocomplete;
</script>
@endpush
