{{-- Layout komponen <x-guest-layout> (view Breeze auth/*): memakai layout auth baru --}}
@extends('layouts.auth')

@section('content')
    {{ $slot }}
@endsection
