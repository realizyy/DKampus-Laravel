@extends('layouts.auth')

@section('back', route('login'))

@section('content')
<div class="text-center">
    <span class="mx-auto grid size-20 place-items-center rounded-full bg-moss-100 text-moss-700 shadow-hairline" x-data x-motion.pop>
        <x-ui.icon name="circle-check" class="size-10" :stroke="2" />
    </span>
    <h1 class="mt-6 font-display text-3xl font-extrabold tracking-tightest text-ink">{{ $heading }}</h1>
    <p class="mx-auto mt-3 max-w-[30ch] text-[15px] leading-relaxed text-ink-500">{{ $message }}</p>
    <x-ui.button :href="route('login')" size="lg" block class="mt-8">{{ $cta }}</x-ui.button>
</div>
@endsection
