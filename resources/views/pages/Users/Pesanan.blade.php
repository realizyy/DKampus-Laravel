@extends('layouts.customer')

@php
    $items = collect($data ?? [])->values();
    $menus = \App\Models\Menu::whereIn('id', $items->pluck('id'))->get()->keyBy('id');
    $cart = $items->map(fn ($i) => [
        'id' => $i['id'],
        'name' => optional($menus->get($i['id']))->nama_makanan ?? ($i['nama'] ?? '-'),
        'note' => ($i['catatan'] ?? '-') !== '-' ? $i['catatan'] : '',
        'price' => (int) $i['harga'],
        'orig' => (int) ($i['harga_asli'] ?? $i['harga']),
        'qty' => (int) $i['jumlah'],
        'img' => optional($menus->get($i['id']))->image ? \Illuminate\Support\Facades\Storage::url($menus->get($i['id'])->image) : '',
    ])->all();
    $utama = $alamatUtama;
    $addresses = collect($AddressList ?? [])->map(fn ($a) => ['id' => $a['id'], 'name' => $a['nama_alamat'], 'address' => $a['address']])->values()->all();
@endphp

@section('page-title', 'Pesanan')

@section('content')
<div class="space-y-5">
    <x-customer.order-tabs active="pesanan" />

    @if ($items->isEmpty())
        <x-ui.card class="!p-0"><x-ui.empty-state icon="shopping-bag" title="Ups, pesanan kamu kosong nih" description="Pilih menu dari warung favoritmu, lalu pesanannya muncul di sini.">
            <x-ui.button :href="url('/')" icon-right="arrow-right">Cari makanan</x-ui.button>
        </x-ui.empty-state></x-ui.card>
    @else
        <div x-data="{
                items: @js($cart), addr: @js($utama ? ['id' => $utama->id, 'name' => $utama->nama_alamat] : ['id' => '', 'name' => 'Masukan Alamat']), removing: '',
                get total() { return this.items.reduce((s, i) => s + i.price * i.qty, 0) },
                rp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID') },
                async save(item, qty) {
                    const before = item.qty; item.qty = qty;
                    try {
                        const r = await fetch('{{ url('/pesanan/update-quantity') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: new URLSearchParams({ id: item.id, quantity: qty }) });
                        if (!r.ok || r.redirected) throw new Error(r.status);
                    } catch (e) { item.qty = before; window.DKToast && DKToast.error('Jumlah belum bisa diubah. Coba lagi.') }
                },
                minus(item) { if (item.qty <= 1) { document.getElementById('deleteInput').value = item.id; dkModal.open('hapus-item') } else this.save(item, item.qty - 1) },
             }" class="grid items-start gap-5 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">

            <x-ui.card :padded="false" class="overflow-hidden" x-motion.rise>
                <div class="border-b border-line/70 p-4"><x-customer.store-banner :name="$namaUMKM" /></div>
                <ul class="divide-y divide-line/70">
                    <template x-for="item in items" :key="item.id">
                        <li class="flex gap-3 p-4">
                            <div class="relative size-20 shrink-0 overflow-hidden rounded-xl bg-sunken"><img :src="item.img" alt="" class="size-full object-cover" onerror="this.remove()"></div>
                            <div class="flex min-w-0 flex-1 flex-col justify-between">
                                <div>
                                    <p class="line-clamp-1 font-display text-base font-bold leading-tight tracking-tight text-ink" x-text="item.name"></p>
                                    <p class="line-clamp-1 text-[13px] text-ink-500" x-show="item.note" x-text="'Catatan: ' + item.note"></p>
                                </div>
                                <p class="tnum font-mono text-sm font-semibold text-brand-700"><span x-text="rp(item.price)"></span><span class="ml-1.5 text-[11px] font-normal text-ink-500 line-through" x-show="item.orig > item.price" x-text="rp(item.orig)"></span></p>
                            </div>
                            <div class="flex shrink-0 items-center self-center rounded-xl bg-surface shadow-card" role="group" aria-label="Jumlah">
                                <button type="button" @click="minus(item)" class="ui-press decrement-btn grid size-10 place-items-center rounded-l-xl text-ink-600 hover:bg-sunken" aria-label="Kurangi"><x-ui.icon name="minus" class="size-4" /></button>
                                <span class="tnum w-8 text-center font-mono text-[15px] font-semibold" x-text="item.qty" aria-live="polite"></span>
                                <button type="button" @click="save(item, item.qty + 1)" class="ui-press increment-btn grid size-10 place-items-center rounded-r-xl text-brand-700 hover:bg-sunken" aria-label="Tambah"><x-ui.icon name="plus" class="size-4" /></button>
                            </div>
                        </li>
                    </template>
                </ul>
            </x-ui.card>

            {{-- Ringkasan + alamat + Pesan: menempel di atas bottom-nav (mobile), panel lengket (desktop) --}}
            <form action="{{ route('checkout') }}" method="POST" id="formCheckout"
                  class="fixed inset-x-0 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] z-20 border-t border-line bg-surface/95 p-3 backdrop-blur lg:sticky lg:top-24 lg:bottom-auto lg:z-auto lg:rounded-2xl lg:border-0 lg:bg-surface lg:p-5 lg:shadow-card">
                @csrf
                <input type="hidden" id="selected_address_id" name="selected_address_id" :value="addr.id">
                <button type="button" data-modal-open="alamat" class="ui-press mb-3 flex w-full items-center gap-3 rounded-xl bg-surface p-3 text-left shadow-card hover:bg-sunken">
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-800"><x-ui.icon name="map-pin" class="size-5" /></span>
                    <span class="min-w-0 flex-1"><span class="block font-mono text-[11px] uppercase tracking-widest text-ink-500">Diantar ke</span><span class="block truncate font-semibold text-ink" id="selected_address" x-text="addr.name"></span></span>
                    <x-ui.icon name="chevron-right" class="size-5 shrink-0 text-ink-400" />
                </button>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-ink-500">Total Harga</p>
                        <p id="total_harga" class="tnum font-display text-2xl font-extrabold tracking-tight text-ink" x-text="rp(total)"></p>
                    </div>
                    <x-ui.button type="submit" size="lg" class="min-w-32">Pesan</x-ui.button>
                </div>
            </form>

            {{-- Pilih alamat --}}
            <x-ui.modal name="alamat" size="md" icon="map-pin" title="Alamat Pengantaran" description="Pilih alamat tujuan pesananmu.">
                @if (count($addresses))
                    <ul class="space-y-2">
                        @foreach ($addresses as $a)
                            <li>
                                <button type="button" class="address-button ui-press flex w-full items-start gap-3 rounded-xl p-3 text-left shadow-hairline hover:bg-sunken" data-id="{{ $a['id'] }}" data-name="{{ $a['name'] }}"
                                        @click="addr = { id: '{{ $a['id'] }}', name: @js($a['name']) }; dkModal.close('alamat')">
                                    <x-ui.icon name="map-pin" class="mt-0.5 size-5 shrink-0 text-brand-600" />
                                    <span class="min-w-0"><span class="block font-semibold text-ink">{{ $a['name'] }}</span><span class="block text-sm text-ink-500">{{ $a['address'] }}</span></span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-[15px] text-ink-600">Silahkan tambahkan alamat dulu.</p>
                @endif
                <x-slot:actions>
                    <x-ui.button :href="url('/daftar-alamat')" variant="secondary" icon="plus" block>Tambah / Kelola Alamat</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
        </div>

        {{-- Konfirmasi hapus saat jumlah menjadi 0 --}}
        <x-ui.modal name="hapus-item" tone="danger" title="Hapus item?" description="Apakah Anda yakin ingin menghapus menu ini dari pesanan?">
            <x-slot:actions>
                <x-ui.button variant="danger" block onclick="document.querySelector('.delete-form').submit()">Ya, hapus</x-ui.button>
                <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
            </x-slot:actions>
        </x-ui.modal>
        <form action="{{ route('cart.delete') }}" method="POST" class="delete-form hidden">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id" id="deleteInput">
        </form>
    @endif
</div>
@endsection
