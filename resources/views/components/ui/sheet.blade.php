{{--
    Panel samping berbasis <dialog> native: slide-over kanan di desktop, bottom sheet di mobile.
    Buka/tutup sama seperti x-ui.modal: [data-modal-open="nama"], dkModal.open('nama'), [data-modal-close], Esc, klik latar.
    Event "modal:opening" (detail = dataset pemicu) dipakai halaman untuk mengisi panel dari baris yang diklik.
    Slot default = isi panel (scroll); slot "footer" = aksi yang menempel di bawah.
--}}
@props(['name', 'title' => null, 'description' => null])
<dialog id="modal-{{ $name }}" class="ui-sheet" aria-labelledby="modal-{{ $name }}-title" {{ $attributes }}>
    <div class="flex max-h-[92dvh] flex-col bg-surface md:h-dvh md:max-h-none">
        <header class="flex items-start justify-between gap-3 border-b border-line px-5 py-4">
            <div class="min-w-0">
                <h2 id="modal-{{ $name }}-title" class="truncate font-display text-xl font-bold tracking-tight text-ink">{{ $title }}</h2>
                @if ($description)<p id="modal-{{ $name }}-description" class="mt-0.5 text-sm text-ink-500">{{ $description }}</p>@endif
            </div>
            <button type="button" data-modal-close class="ui-press -mr-1.5 -mt-1 grid size-10 shrink-0 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink" aria-label="Tutup">
                <x-ui.icon name="x" class="size-5" />
            </button>
        </header>
        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">{{ $slot }}</div>
        @isset($footer)
            <footer class="border-t border-line bg-surface px-5 pb-[max(1rem,env(safe-area-inset-bottom))] pt-4">{{ $footer }}</footer>
        @endisset
    </div>
</dialog>
