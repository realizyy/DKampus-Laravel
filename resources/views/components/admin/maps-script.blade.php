{{-- Google Maps Places untuk form alamat resto. Kunci dari config('services.google_maps.admin_key') (.env GOOGLE_MAPS_ADMIN_KEY, cadangan GOOGLE_MAPS_API_KEY);
     tanpa kunci, alamat dan koordinat tetap bisa diisi manual. --}}
@php($key = config('services.google_maps.admin_key'))
{{-- Setiap input [data-places] (dengan data-geo-target / data-link-target) mendapat Autocomplete; place_changed mengisi koordinat dan tautan peta --}}
<script>
window.initAutocomplete = function () {
    document.querySelectorAll('[data-places]').forEach(function (input) {
        var geo = document.querySelector(input.dataset.geoTarget);
        var link = document.querySelector(input.dataset.linkTarget);
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry || !place.place_id) {
                if (window.DKToast) DKToast.error('Lokasi tidak ditemukan. Pilih alamat dari daftar saran.');
                return;
            }
            if (geo) geo.value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
            if (link) link.value = place.url || '';
            input.dispatchEvent(new Event('input', { bubbles: true }));
        });
        // Enter di input alamat memilih saran, bukan mengirim form
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });
    });
};
</script>
@if ($key)
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $key }}&libraries=places&callback={{ $callback ?? 'initAutocomplete' }}" defer></script>
@endif
<style>
    /* Hilangkan atribusi "powered by Google" di daftar saran */
    .pac-container:after { content: none !important; }
    .pac-container { border-radius: 0.75rem; font-family: inherit; box-shadow: 0 0 0 1px rgb(31 26 20 / 0.08), 0 14px 32px -10px rgb(31 26 20 / 0.22); }
</style>
