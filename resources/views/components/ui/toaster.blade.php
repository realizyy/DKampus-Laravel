<div x-data class="pointer-events-none fixed inset-x-0 top-0 z-[100] flex flex-col items-center gap-2 p-4 pt-[max(1rem,env(safe-area-inset-top))] md:items-end" aria-live="polite">
    <template x-for="t in $store.toast.items" :key="t.id">
        <div class="pointer-events-auto flex w-full max-w-sm animate-toast-in items-start gap-3 rounded-xl bg-surface p-3.5 shadow-pop" role="status">
            <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full"
                  :class="{ 'bg-moss-100 text-moss-700': t.type === 'success', 'bg-brick-100 text-brick-700': t.type === 'error', 'bg-teal-100 text-teal-700': t.type === 'info' }">
                <x-ui.icon name="check" class="size-3.5" x-show="t.type === 'success'" :stroke="3" />
                <x-ui.icon name="x" class="size-3.5" x-show="t.type === 'error'" :stroke="3" />
                <x-ui.icon name="info" class="size-3.5" x-show="t.type === 'info'" :stroke="3" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-ink" x-show="t.title" x-text="t.title"></p>
                <p class="text-sm text-ink-600" x-text="t.message"></p>
            </div>
            <button type="button" class="ui-press -mr-1 -mt-1 grid size-8 place-items-center rounded-lg text-ink-400 hover:bg-sunken hover:text-ink" @click="$store.toast.remove(t.id)" aria-label="Tutup notifikasi">
                <x-ui.icon name="x" class="size-4" />
            </button>
        </div>
    </template>
</div>
