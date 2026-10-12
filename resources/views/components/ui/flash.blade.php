{{-- Meneruskan flash session ke toast (lihat resources/js/lib/toast.js). Pakai satu kali per halaman. --}}
@php
    $flash = array_filter([
        'success' => session('success'),
        'status' => is_string(session('status')) && ! str_ends_with((string) session('status'), '-updated') && session('status') !== 'verification-link-sent' ? session('status') : null,
        'error' => session('error2') ?? (is_string(session('error')) ? session('error') : null),
    ]);
@endphp
@if ($flash)
    <div data-flash='@json($flash)' hidden></div>
@endif
