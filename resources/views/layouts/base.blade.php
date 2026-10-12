<!DOCTYPE html>
<html lang="id" data-theme="dkampus">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@hasSection('title')@yield('title')@else{{ $Title ?? 'DKampus' }}@endif · DKampus</title>
    {{-- Elemen x-motion disembunyikan sampai dianimasikan; failsafe 2,5 dtk bila JS gagal --}}
    <script>document.documentElement.classList.add('js');setTimeout(function(){document.documentElement.classList.add('motion-failsafe')},2500)</script>
    @include('layouts.partials.head-assets')
    @stack('head')
</head>

<body class="min-h-dvh @yield('body-class')">
    @yield('shell')
    <x-ui.toaster />
    <x-ui.flash />
    @stack('js')
</body>

</html>
