{{--
    Modal berbasis <dialog> native (focus trap, Esc, inert latar dari browser) dengan primitif DaisyUI.
    Buka: <button data-modal-open="nama"> atau dkModal.open('nama'). Tutup: data-modal-close atau Esc.
    Varian Figma: tone success (Konfirmasi), danger (Hapus/Logout), brand.
    Event "modal:opening" (detail = dataset pemicu) memungkinkan halaman mengisi isi modal dari tombol yang diklik.
--}}
@props(['name', 'title' => null, 'description' => null, 'tone' => 'brand', 'icon' => null, 'dismissible' => true, 'size' => 'sm'])
@php
    $tones = [
        'success' => ['bg-moss-100 text-moss-700', 'circle-check'],
        'danger' => ['bg-brick-100 text-brick-700', 'trash-2'],
        'brand' => ['bg-brand-100 text-brand-800', 'info'],
        'neutral' => ['bg-sunken text-ink-600', 'info'],
    ];
    [$toneClass, $defaultIcon] = $tones[$tone] ?? $tones['brand'];
    $width = $size === 'md' ? 'w-[min(28rem,calc(100vw-2rem))]' : 'w-[min(21.5rem,calc(100vw-2rem))]';
@endphp
<dialog id="modal-{{ $name }}" class="d-modal" aria-labelledby="modal-{{ $name }}-title" {{ $attributes }}>
    <div class="d-modal-box {{ $width }} max-h-[calc(100dvh-2rem)] max-w-none overflow-y-auto rounded-2xl bg-surface p-5 shadow-pop">
        <div class="flex items-start justify-between gap-3">
            <span class="grid size-11 place-items-center rounded-full {{ $toneClass }}">
                <x-ui.icon :name="$icon ?? $defaultIcon" class="size-5" />
            </span>
            @if ($dismissible)
                <button type="button" data-modal-close class="ui-press -mr-1.5 -mt-1.5 grid size-9 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink" aria-label="Tutup">
                    <x-ui.icon name="x" class="size-5" />
                </button>
            @endif
        </div>
        @if ($title)
            <h2 id="modal-{{ $name }}-title" class="mt-3 font-display text-lg font-bold tracking-tight text-ink">{{ $title }}</h2>
        @endif
        @if ($description)
            <p class="mt-1 text-[15px] leading-snug text-ink-500">{{ $description }}</p>
        @endif
        @if (! $slot->isEmpty())
            <div class="mt-4">{{ $slot }}</div>
        @endif
        @isset($actions)
            <div class="mt-5 flex flex-col gap-2">{{ $actions }}</div>
        @endisset
    </div>
    @if ($dismissible)
        <form method="dialog" class="d-modal-backdrop bg-ink/40"><button aria-label="Tutup">tutup</button></form>
    @endif
</dialog>
