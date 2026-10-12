<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#FBF7F1">
<link rel="icon" type="image/svg+xml" href="{{ asset('logoDkampus.svg') }}">
<link rel="icon" type="image/png" href="{{ asset('logoDkampus.png') }}">
<link rel="apple-touch-icon" href="{{ asset('logoDkampus.png') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- Hash bundle lama untuk deploy produksi; hanya dimuat bila file-nya ada --}}
@if (file_exists(public_path('build/assets/app-c9100832.css')))
    <link rel="stylesheet" href="/build/assets/app-c9100832.css">
@endif
@if (file_exists(public_path('build/assets/app-5db491ae.js')))
    <script type="module" src="/build/assets/app-5db491ae.js"></script>
@endif
