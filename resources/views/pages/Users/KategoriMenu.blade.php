@extends('layouts.customer')

@section('page-title', 'Kategori '.ucfirst($Kategori))
@section('back', url('/'))

@section('content')
<div class="space-y-6">
    <x-ui.page-header :title="'Kategori '.ucfirst($Kategori)" :subtitle="$menus->count().' menu ditemukan'" class="hidden md:flex" />
    @if ($menus->isEmpty())
        <x-ui.card class="!p-0"><x-ui.empty-state icon="utensils" title="Belum ada menu" description="Menu untuk kategori ini belum tersedia." >
            <x-ui.button :href="url('/')" variant="secondary">Kembali ke beranda</x-ui.button>
        </x-ui.empty-state></x-ui.card>
    @else
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5" x-motion.rise.stagger>
            @foreach ($menus as $menu)
                <x-customer.food-card :menu="$menu" />
            @endforeach
        </div>
    @endif
</div>
@endsection
