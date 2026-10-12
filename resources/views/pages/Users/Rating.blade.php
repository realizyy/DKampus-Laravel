@extends('layouts.customer')

@section('page-title', 'Rating')
@section('back', url('pesanan/status'))
@section('hide-nav', '1')

@section('content')
@php
    $umkm = $dataUMKM->nama_umkm ?? 'Jastip';
    $aspects = ['Pengiriman', 'Harga', 'Rasa', 'Kebersihan', 'Kualitas'];
@endphp
<form action="{{ route('rating.store') }}" method="POST" class="mx-auto max-w-2xl space-y-5 pb-24 md:pb-0" x-data="{ aspects: [] }">
    @csrf
    <input type="hidden" name="history_id" value="{{ $data->id }}">

    <x-ui.card class="flex items-center gap-4" x-motion.rise>
        <x-ui.photo :src="Storage::url($dataUMKM->logo_umkm ?? '')" alt="{{ $umkm }}" icon="store" class="size-20 shrink-0 rounded-2xl" />
        <div class="min-w-0">
            <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Beri rating untuk</p>
            <h1 class="font-display text-2xl font-extrabold leading-tight tracking-tightest text-ink">{{ $umkm }}</h1>
        </div>
    </x-ui.card>

    <x-ui.card class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="font-semibold text-ink">Rating Pelayanan</p>
            <x-ui.star-input name="rating_pelayanan" label="Rating pelayanan" />
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-line/70 pt-5">
            <p class="font-semibold text-ink">Rating Makanan</p>
            <x-ui.star-input name="rating_makanan" label="Rating makanan" />
        </div>
        @error('rating_pelayanan')<p class="text-sm text-brick-700" role="alert">{{ $message }}</p>@enderror
        @error('rating_makanan')<p class="text-sm text-brick-700" role="alert">{{ $message }}</p>@enderror
    </x-ui.card>

    <x-ui.card class="space-y-3">
        <p class="font-semibold text-ink">Apa yang harus ditingkatkan?</p>
        <div class="flex flex-wrap gap-2">
            @foreach ($aspects as $a)
                <label class="cursor-pointer">
                    <input type="checkbox" name="aspects[]" value="{{ $a }}" class="peer sr-only" x-model="aspects">
                    <span class="ui-press inline-flex h-10 items-center rounded-xl bg-surface px-4 text-[15px] font-semibold text-ink-600 shadow-hairline hover:bg-sunken peer-checked:bg-ink peer-checked:text-canvas peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-500">{{ $a }}</span>
                </label>
            @endforeach
        </div>
    </x-ui.card>

    @if (! empty($Orders))
        <x-ui.card class="space-y-2">
            <p class="font-semibold text-ink">Pesananmu</p>
            <ul class="space-y-1 text-[15px] text-ink-700">
                @foreach ($Orders as $o)<li><span class="tnum font-mono font-semibold">{{ $o['quantity'] }}×</span> {{ $o['name'] }}</li>@endforeach
            </ul>
        </x-ui.card>
    @endif

    <x-ui.field label="Review" for="review"><x-ui.textarea id="review" name="review" placeholder="Ceritakan pengalamanmu (opsional)">{{ old('review') }}</x-ui.textarea></x-ui.field>

    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur md:static md:z-auto md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
        <x-ui.button type="submit" size="lg" block>Kirim</x-ui.button>
    </div>
</form>
@endsection
