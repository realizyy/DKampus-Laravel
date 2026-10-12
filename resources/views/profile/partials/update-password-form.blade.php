<section aria-labelledby="profile-password-title">
    <header>
        <h2 id="profile-password-title" class="font-display text-xl font-bold tracking-tight text-ink">Ubah Kata Sandi</h2>
        <p class="mt-1 text-[15px] text-ink-500">Pakai kata sandi yang panjang dan acak supaya akunmu tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <x-ui.field label="Kata Sandi Saat Ini" for="current_password" :error="$errors->updatePassword->first('current_password')">
            <x-ui.password name="current_password" id="current_password" autocomplete="current-password" :error="$errors->updatePassword->has('current_password')" />
        </x-ui.field>

        <x-ui.field label="Kata Sandi Baru" for="password" :error="$errors->updatePassword->first('password')">
            <x-ui.password name="password" id="password" autocomplete="new-password" :error="$errors->updatePassword->has('password')" />
        </x-ui.field>

        <x-ui.field label="Konfirmasi Kata Sandi Baru" for="password_confirmation" :error="$errors->updatePassword->first('password_confirmation')">
            <x-ui.password name="password_confirmation" id="password_confirmation" autocomplete="new-password" :error="$errors->updatePassword->has('password_confirmation')" />
        </x-ui.field>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit">Simpan</x-ui.button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="flex items-center gap-1.5 text-sm font-semibold text-moss-700" role="status">
                    <x-ui.icon name="circle-check" class="size-4" />Kata sandi diubah
                </p>
            @endif
        </div>
    </form>
</section>
