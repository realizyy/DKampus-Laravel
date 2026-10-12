{{-- Status order/akun dengan warna konsisten. Label asli dari data dipertahankan (mis. "Waiting For Driver"). --}}
@props(['value', 'label' => null])
@php
    $key = strtolower(trim((string) $value));
    $tone = match (true) {
        in_array($key, ['completed', 'selesai', 'aktif', 'active', 'success'], true) => 'moss',
        in_array($key, ['on delivery', 'ondelivery', 'diantar', 'delivery'], true) => 'teal',
        in_array($key, ['searching', 'waiting for driver', 'menunggu', 'pending', 'proses'], true) => 'amber',
        in_array($key, ['canceled', 'cancelled', 'batal', 'dibatalkan', 'non aktif', 'nonaktif', 'inactive'], true) => 'brick',
        default => 'neutral',
    };
@endphp
<x-ui.badge :tone="$tone" dot {{ $attributes }}>{{ $label ?? $value }}</x-ui.badge>
