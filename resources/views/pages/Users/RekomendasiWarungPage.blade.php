@extends('layouts.customer')

@section('page-title', 'Rekomendasi Warung')
@section('back', url('/'))

@section('content')
<div class="space-y-6">
    <x-ui.page-header title="Rekomendasi Warung Terbaik" :subtitle="$RekomendasiWarung->count().' warung di sekitar kampus'" class="hidden md:flex" />
    <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4" x-motion.rise.stagger>
        @foreach ($RekomendasiWarung as $umkm)
            <x-customer.umkm-card :umkm="$umkm" />
        @endforeach
    </div>
</div>
@endsection
