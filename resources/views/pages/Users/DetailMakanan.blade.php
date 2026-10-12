@extends('layouts.customer')

@php
    $diskon = (float) ($diskon ?? 0);
    $hargaAsli = (int) ($hargaAsli ?? 0);
    $rp = fn ($n) => number_format($n, 0, ',', '.');
    $lain = ($CardFood ?? collect())->where('id', '!=', $menu_id)->take(4);
@endphp

@section('page-title', $nama_makanan)
@section('back', url('/detail-warung/'.rawurlencode($umkm_nama)))
@section('hide-nav', '1')

@section('content')
<form action="{{ url('/detail-makanan/'.$menu_id) }}" method="POST" x-data="{ qty: 1 }" class="space-y-8 pb-24 md:pb-0">
    @csrf
    <div class="grid gap-6 md:grid-cols-12 md:gap-10">
        <div class="relative md:col-span-6 lg:col-span-7" x-motion.rise>
            <x-ui.photo :src="Storage::url($images)" :alt="$nama_makanan" class="aspect-[4/3] w-full rounded-3xl shadow-card" />
            <x-customer.favorite-button :menu="(object) ['id' => $menu_id]" class="absolute right-3 top-3 !size-11" />
            @if ($diskon > 0)<span class="absolute left-3 top-3 rounded-lg bg-brand-700 px-2.5 py-1 font-mono text-sm font-bold text-white shadow-hairline">-{{ rtrim(rtrim(number_format($diskon, 1), '0'), '.') }}%</span>@endif
        </div>

        <div class="flex flex-col gap-5 md:col-span-6 lg:col-span-5">
            <div class="space-y-2">
                <a href="{{ url('/detail-warung/'.rawurlencode($umkm_nama)) }}" class="ui-press inline-flex items-center gap-1.5 text-[15px] font-semibold text-brand-700 hover:underline"><x-ui.icon name="store" class="size-4" />{{ $umkm_nama }}</a>
                <h1 class="font-display text-4xl font-extrabold leading-[1.02] tracking-tightest text-ink md:text-5xl">{{ $nama_makanan }}</h1>
                <div class="flex items-center gap-3">@if ($rating)<x-ui.rating :value="$rating" />@endif</div>
            </div>

            <div class="flex items-end justify-between rounded-2xl bg-sunken p-4">
                <span class="font-mono text-xs uppercase tracking-widest text-ink-500">Harga</span>
                <p class="tnum text-right"><span class="font-display text-3xl font-extrabold tracking-tight text-ink">Rp {{ $harga }}</span>@if ($diskon > 0)<span class="ml-2 font-mono text-sm text-ink-500 line-through">{{ $rp($hargaAsli) }}</span>@endif</p>
            </div>

            @if ($deskripsi)<p class="text-[17px] leading-relaxed text-ink-600">{{ $deskripsi }}</p>@endif

            <x-ui.field label="Catatan untuk restoran" for="catatan" hint="Permintaanmu akan disesuaikan dengan kebijakan resto">
                <x-ui.textarea id="catatan" name="catatan" placeholder="Opsional. Mis. ayamnya yang paling besar, sambal dipisah"></x-ui.textarea>
            </x-ui.field>

            {{-- Stepper + Pesan: menempel di bawah layar (mobile), inline di desktop --}}
            <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur md:static md:z-auto md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
                <div class="mx-auto flex max-w-xl items-center gap-3 md:max-w-none">
                    <div class="flex h-12 shrink-0 items-center rounded-xl bg-surface shadow-card" role="group" aria-label="Jumlah">
                        <button type="button" id="decrement" @click="qty = Math.max(1, qty - 1)" :disabled="qty <= 1" aria-label="Kurangi" class="ui-press grid size-12 place-items-center rounded-l-xl text-ink-600 hover:bg-sunken disabled:opacity-40"><x-ui.icon name="minus" class="size-[18px]" /></button>
                        <input id="number" name="quantity" type="number" min="1" x-model.number="qty" readonly class="tnum h-12 w-10 border-0 bg-transparent p-0 text-center font-mono text-base font-semibold text-ink focus:ring-0" aria-live="polite">
                        <button type="button" id="increment" @click="qty++" aria-label="Tambah" class="ui-press grid size-12 place-items-center rounded-r-xl text-brand-700 hover:bg-sunken"><x-ui.icon name="plus" class="size-[18px]" /></button>
                    </div>
                    <x-ui.button type="submit" size="lg" block>Pesan Sekarang</x-ui.button>
                </div>
            </div>
        </div>
    </div>

    @if ($lain->isNotEmpty())
        <section class="space-y-4" aria-label="Menu lain dari warung ini">
            <x-customer.section-head title="Menu lain dari {{ $umkm_nama }}" />
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
                @foreach ($lain as $menu)<x-customer.food-card :menu="$menu" />@endforeach
            </div>
        </section>
    @endif
</form>

{{-- Setelah berhasil menambah ke keranjang, kembali ke halaman sebelumnya --}}
@if (session('status'))
    @push('js')<script>window.history.back();</script>@endpush
@endif
@endsection
