{{-- Panel akun (Menu Utama): profil, alamat utama, Pengaturan Akun, Seputar Dkampus, Logout. Dibuka lewat x-data "account" di layout. --}}
@php
    $user = auth()->user();
    $alamat = optional($user->addresses->where('utama', 1)->first())->nama_alamat;
    $icon = fn (string $title) => match (true) {
        str_contains($title, 'Alamat') => 'map-pin',
        str_contains($title, 'Sandi') => 'key-round',
        str_contains($title, 'Tentang') => 'info',
        str_contains($title, 'Syarat') => 'file-text',
        default => 'chevron-right',
    };
    $groups = [
        'Pengaturan Akun' => \App\Models\HomeModel::pengaturanAkun(),
        'Seputar Dkampus' => \App\Models\HomeModel::seputarDkampus(),
    ];
@endphp
<div x-cloak>
    <div x-show="account" x-transition.opacity.duration.200ms class="fixed inset-0 z-[90] bg-ink/40 backdrop-blur-[2px]" @click="account = false" aria-hidden="true"></div>
    <aside x-show="account" x-trap.noscroll.inert="account" role="dialog" aria-modal="true" aria-label="Menu utama"
           x-transition:enter="transition duration-300 ease-out-quint" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
           class="fixed inset-y-0 right-0 z-[91] flex w-[min(24rem,100vw)] flex-col overflow-y-auto bg-canvas shadow-pop">
        <div class="flex items-center justify-between px-5 pb-2 pt-[max(1.25rem,env(safe-area-inset-top))]">
            <h2 class="font-display text-xl font-extrabold tracking-tight">Menu Utama</h2>
            <button type="button" @click="account = false" class="ui-press grid size-10 place-items-center rounded-lg text-ink-500 hover:bg-sunken hover:text-ink" aria-label="Tutup menu"><x-ui.icon name="x" class="size-5" /></button>
        </div>

        <x-ui.card class="mx-5 mt-3 flex items-center gap-3">
            <x-ui.avatar :name="$user->nama_user" size="lg" />
            <div class="min-w-0">
                <p class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $user->nama_user }}</p>
                <p class="truncate text-sm text-ink-500">{{ $user->email }}</p>
                @if ($alamat)<p class="mt-0.5 flex items-center gap-1 truncate font-mono text-xs text-ink-600"><x-ui.icon name="map-pin" class="size-3.5 shrink-0 text-brand-600" />{{ $alamat }}</p>@endif
            </div>
        </x-ui.card>

        @foreach ($groups as $label => $items)
            <section class="mt-6 px-5">
                <h3 class="mb-2 px-1 font-mono text-[11px] uppercase tracking-widest text-ink-500">{{ $label }}</h3>
                <x-ui.card :padded="false" class="divide-y divide-line/70 overflow-hidden">
                    @foreach ($items as $item)
                        <a href="{{ $item['Url'] }}" class="ui-press flex items-center gap-3 p-3.5 hover:bg-sunken">
                            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-brand-100 text-brand-800"><x-ui.icon :name="$icon($item['Title'])" class="size-5" /></span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold text-ink">{{ $item['Title'] }}</span>
                                <span class="block truncate text-sm text-ink-500">{{ $item['Desc'] }}</span>
                            </span>
                            <x-ui.icon name="chevron-right" class="size-5 shrink-0 text-ink-400" />
                        </a>
                    @endforeach
                </x-ui.card>
            </section>
        @endforeach

        <div class="mt-auto p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
            <x-ui.button variant="danger-soft" icon="log-out" block @click="account = false; dkModal.open('logout')">Keluar</x-ui.button>
        </div>
    </aside>
</div>
