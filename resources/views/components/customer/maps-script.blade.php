{{-- Google Maps Places (Autocomplete). Kunci dari config('services.google_maps.key') (.env GOOGLE_MAPS_API_KEY); tanpa kunci, input alamat tetap bisa diisi manual. --}}
@php($key = config('services.google_maps.key'))
@if ($key)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $key }}&libraries=places&callback={{ $callback ?? 'initAutocomplete' }}" defer></script>
@endif
