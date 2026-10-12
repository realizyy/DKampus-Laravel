{{-- Tombol bantuan melayang (CS Help) + modal pertanyaan. Fitur masih tahap pengembangan: tombol kirim nonaktif seperti sebelumnya. --}}
<button type="button" data-modal-open="bantuan" class="ui-press fixed bottom-24 right-4 z-30 inline-flex h-12 items-center gap-2 rounded-2xl bg-ink px-4 text-[15px] font-semibold text-canvas shadow-pop hover:bg-ink-700 md:bottom-6 md:right-8">
    <x-ui.icon name="message-circle" class="size-5" />CS Help
</button>

<x-ui.modal name="bantuan" size="md" icon="message-circle" title="Bantuan" description="Tulis pertanyaan, keluhan, atau saranmu.">
    <form action="{{ route('cs.help') }}" method="POST" class="space-y-4">
        @csrf
        <x-ui.field label="Pertanyaan" for="pertanyaan" hint="*Chat dijawab bot lalu diarahkan ke customer service.">
            <x-ui.input id="pertanyaan" name="pertanyaan" placeholder="Masukkan pertanyaan, keluhan, atau saran kamu" />
        </x-ui.field>
        <p class="flex items-start gap-2 rounded-xl bg-amber-100 p-3 text-sm text-amber-700"><x-ui.icon name="triangle-alert" class="mt-0.5 size-4 shrink-0" />Fitur ini masih dalam tahap pengembangan. Untuk pertanyaan lebih lanjut, hubungi admin lewat WhatsApp.</p>
        <div class="flex flex-col gap-2">
            <x-ui.button type="submit" block disabled>Kirim</x-ui.button>
            <x-ui.button variant="outline" data-modal-close block>Tutup</x-ui.button>
        </div>
    </form>
</x-ui.modal>
