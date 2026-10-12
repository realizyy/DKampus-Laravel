{{--
    Palet perintah (Cmd/Ctrl+K). $items: [['label' => 'Kelola Akun', 'href' => '/admin/account', 'hint' => 'akun user', 'icon' => 'users']]
--}}
@props(['items' => []])
<div x-data="palette(@js($items))" x-cloak>
    <div x-show="open" x-transition.opacity.duration.150ms class="fixed inset-0 z-[90] bg-ink/40 backdrop-blur-[2px]" @click="toggle()" aria-hidden="true"></div>
    <div x-show="open" x-trap.noscroll="open" role="dialog" aria-modal="true" aria-label="Palet perintah"
         @keydown.escape.window="open && toggle()" @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go()"
         x-transition:enter="transition duration-200 ease-out-quint" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="fixed left-1/2 top-[14vh] z-[91] w-[min(36rem,calc(100vw-2rem))] -translate-x-1/2 overflow-hidden rounded-2xl bg-surface shadow-pop">
        <div class="flex items-center gap-3 border-b border-line px-4">
            <x-ui.icon name="search" class="size-[18px] text-ink-400" />
            <input x-ref="input" x-model="q" @input="index = 0" type="text" placeholder="Cari halaman atau aksi…" class="h-14 flex-1 border-0 bg-transparent p-0 text-[15px] text-ink placeholder:text-ink-400 focus:outline-none focus:ring-0" autocomplete="off">
            <x-ui.kbd>Esc</x-ui.kbd>
        </div>
        <ul class="max-h-[50vh] overflow-y-auto p-2" role="listbox">
            <template x-for="(item, i) in results" :key="item.href">
                <li role="option" :aria-selected="i === index">
                    <a :href="item.href" @mouseenter="index = i" class="flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-[15px] text-ink" :class="i === index ? 'bg-brand-50 shadow-hairline' : ''">
                        <span class="font-semibold" x-text="item.label"></span>
                        <span class="truncate font-mono text-xs text-ink-500" x-text="item.hint ?? ''"></span>
                    </a>
                </li>
            </template>
            <li x-show="results.length === 0" class="px-3 py-8 text-center text-sm text-ink-500">Tidak ada hasil untuk “<span x-text="q"></span>”</li>
        </ul>
        <div class="flex items-center gap-4 border-t border-line bg-sunken/60 px-4 py-2.5 text-xs text-ink-500">
            <span class="flex items-center gap-1.5"><x-ui.kbd>↑</x-ui.kbd><x-ui.kbd>↓</x-ui.kbd> pilih</span>
            <span class="flex items-center gap-1.5"><x-ui.kbd>↵</x-ui.kbd> buka</span>
        </div>
    </div>
</div>
