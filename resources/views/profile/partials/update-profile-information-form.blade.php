<section aria-labelledby="profile-info-title">
    <header>
        <h2 id="profile-info-title" class="font-display text-xl font-bold tracking-tight text-ink">Pengaturan Akun</h2>
        <p class="mt-1 text-[15px] text-ink-500">Perbarui nama dan alamat email akunmu.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <x-ui.field label="Nama Lengkap" for="nama_user" required :error="$errors->first('nama_user')">
            <x-ui.input type="text" name="nama_user" id="nama_user" icon="user" :value="old('nama_user', $user->nama_user)" required autocomplete="name" :error="$errors->has('nama_user')" />
        </x-ui.field>

        <x-ui.field label="Email" for="email" required :error="$errors->first('email')">
            <x-ui.input type="email" name="email" id="email" icon="mail" :value="old('email', $user->email)" required autocomplete="username" :error="$errors->has('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-ink-600">
                    Alamat email belum terverifikasi.
                    <button form="send-verification" class="font-semibold text-brand-700 underline underline-offset-2 hover:text-brand-800">Kirim ulang email verifikasi</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm font-semibold text-moss-700">Tautan verifikasi baru sudah dikirim ke emailmu.</p>
                @endif
            @endif
        </x-ui.field>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit">Simpan</x-ui.button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="flex items-center gap-1.5 text-sm font-semibold text-moss-700" role="status">
                    <x-ui.icon name="circle-check" class="size-4" />Tersimpan
                </p>
            @endif
        </div>
    </form>
</section>
