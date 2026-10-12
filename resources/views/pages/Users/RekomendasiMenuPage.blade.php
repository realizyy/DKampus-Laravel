@extends('layouts.customer')

@section('page-title', 'Rekomendasi Menu')
@section('back', url('/'))

@section('content')
<div class="space-y-6">
    <x-ui.page-header title="Rekomendasi Menu Terbaik" :subtitle="$RekomendasiMenu->count().' menu pilihan'" class="hidden md:flex" />
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 xl:grid-cols-5" x-motion.rise.stagger>
        @foreach ($RekomendasiMenu as $menu)
            <x-customer.food-card :menu="$menu" />
        @endforeach
    </div>
</div>
@endsection
