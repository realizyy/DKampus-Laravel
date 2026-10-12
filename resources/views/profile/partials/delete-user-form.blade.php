<section aria-labelledby="profile-delete-title" class="rounded-2xl bg-brick-50/60 p-5 shadow-[0_0_0_1px_theme(colors.brick.100)]">
    <header>
        <h2 id="profile-delete-title" class="font-display text-xl font-bold tracking-tight text-brick-700">Hapus Akun</h2>
        <p class="mt-1 text-[15px] text-ink-600">Setelah dihapus, akun dan seluruh datanya hilang permanen. Simpan dulu data yang masih kamu perlukan.</p>
    </header>

    <x-ui.button variant="danger-soft" icon="trash-2" class="mt-4" data-modal-open="confirm-user-deletion">Hapus Akun</x-ui.button>

    {{-- Galat kata sandi (error bag userDeletion) membuka kembali dialog saat halaman dimuat ulang --}}
    <x-ui.modal name="confirm-user-deletion" tone="danger" title="Yakin ingin menghapus akun?" description="Masukkan kata sandi untuk memastikan akun ini benar-benar ingin dihapus permanen.">
        <form method="post" action="{{ route('profile.destroy') }}" id="delete-account-form" class="space-y-4">
            @csrf
            @method('delete')
            <x-ui.field label="Kata Sandi" for="delete_password" :error="$errors->userDeletion->first('password')">
                <x-ui.password name="password" id="delete_password" autocomplete="current-password" placeholder="Kata sandi" :error="$errors->userDeletion->has('password')" />
            </x-ui.field>
        </form>
        <x-slot:actions>
            <x-ui.button type="submit" form="delete-account-form" variant="danger" block>Hapus Akun</x-ui.button>
            <x-ui.button variant="outline" data-modal-close block>Batal</x-ui.button>
        </x-slot:actions>
    </x-ui.modal>

    @if ($errors->userDeletion->isNotEmpty())
        @push('js')
            <script>window.addEventListener('DOMContentLoaded', function () { dkModal.open('confirm-user-deletion'); });</script>
        @endpush
    @endif
</section>
