@extends('layouts.customer')

@php
    $buka = $open_time ? date('H:i', strtotime($open_time)) : null;
    $tutup = $close_time ? date('H:i', strtotime($close_time)) : null;
    $now = date('H:i');
    $isOpen = ! ($buka && $tutup) || ($buka <= $tutup ? ($now >= $buka && $now <= $tutup) : ($now >= $buka || $now <= $tutup));
@endphp

@section('page-title', $nama_umkm)
@section('back', url('/'))

@section('content')
<div class="space-y-8">
    {{-- Kepala warung: foto + info (asimetris di desktop) --}}
    <section class="grid gap-4 md:grid-cols-12" x-motion.rise>
        <x-ui.photo :src="Storage::url($umkm_img)" :alt="$nama_umkm" icon="store" :grayscale="! $isOpen" class="aspect-[16/9] w-full rounded-3xl shadow-card md:col-span-7 md:aspect-[4/3]" />
        <div class="flex flex-col justify-center gap-3 md:col-span-5 md:pl-4">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge :tone="$isOpen ? 'moss' : 'neutral'" dot>{{ $isOpen ? 'Buka' : 'Tutup' }}</x-ui.badge>
                @if ($rating)<x-ui.rating :value="$rating" />@endif
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-[1.02] tracking-tightest text-ink md:text-5xl">{{ $nama_umkm }}</h1>
            <dl class="space-y-2 text-[15px] text-ink-600">
                <div class="flex items-start gap-2"><dt class="sr-only">Alamat</dt><x-ui.icon name="map-pin" class="mt-0.5 size-[18px] shrink-0 text-brand-600" /><dd>{{ $alamat }}</dd></div>
                <div class="flex items-center gap-2"><dt class="sr-only">Jam buka</dt><x-ui.icon name="clock" class="size-[18px] shrink-0 text-brand-600" /><dd class="tnum font-mono">@if ($buka && $tutup){{ $buka }} - {{ $tutup }}@else Jam belum diatur @endif</dd></div>
            </dl>
        </div>
    </section>

    {{-- Daftar menu --}}
    <section class="space-y-4" aria-label="Menu">
        <x-customer.section-head title="Menu" />
        @if ($CardFood->isEmpty())
            <x-ui.card class="!p-0"><x-ui.empty-state icon="utensils" title="Belum ada menu" description="Warung ini belum menambahkan menu." /></x-ui.card>
        @else
            <div class="grid gap-3 md:grid-cols-2 md:gap-4" x-motion.rise.stagger>
                @foreach ($CardFood as $menu)
                    <x-customer.menu-row :menu="$menu" />
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
