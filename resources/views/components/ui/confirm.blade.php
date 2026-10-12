{{-- Modal konfirmasi siap pakai (Konfirmasi / Hapus / Logout): form POST dengan CSRF + method spoofing. --}}
@props(['name', 'title', 'description' => null, 'action', 'method' => 'POST', 'tone' => 'danger', 'confirm' => 'Ya', 'cancel' => 'Batal', 'icon' => null])
<x-ui.modal :name="$name" :title="$title" :description="$description" :tone="$tone" :icon="$icon">
    {{ $slot }}
    <x-slot:actions>
        <form method="POST" action="{{ $action }}" class="contents">
            @csrf
            @if (! in_array(strtoupper($method), ['GET', 'POST'], true)) @method($method) @endif
            <x-ui.button type="submit" :variant="$tone === 'danger' ? 'danger' : 'primary'" block>{{ $confirm }}</x-ui.button>
        </form>
        <x-ui.button variant="outline" data-modal-close block>{{ $cancel }}</x-ui.button>
    </x-slot:actions>
</x-ui.modal>
