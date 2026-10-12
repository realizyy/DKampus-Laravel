@extends('layouts.base', ['Title' => 'UI Kit'])

@php
    $palette = [
        'brand' => [500 => '#F9832A', 600 => '#E96A10', 700 => '#C2530C', 800 => '#9A4112'],
        'canvas' => ['canvas' => '#FBF7F1', 'surface' => '#FFFDF9', 'sunken' => '#F4EEE4', 'line' => '#E8E0D4'],
        'ink' => ['ink' => '#1F1A14', '700' => '#3A332A', '600' => '#51483C', '500' => '#655B4D', '400' => '#7A6F5F'],
        'status' => ['moss-600' => '#3F6B3A', 'brick-600' => '#B3402A', 'amber-700' => '#8A5A00', 'teal-700' => '#1F6B63'],
    ];
    $nav = [
        ['label' => 'Home', 'href' => '#', 'icon' => 'house', 'active' => true],
        ['label' => 'Order', 'href' => '#', 'icon' => 'shopping-bag'],
        ['label' => 'History', 'href' => '#', 'icon' => 'clipboard-list'],
        ['label' => 'Profile', 'href' => '#', 'icon' => 'user'],
    ];
@endphp

@section('shell')
<div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 md:grid-cols-[13rem_1fr] md:px-8">
    <aside class="md:sticky md:top-10 md:self-start">
        <p class="font-mono text-xs uppercase tracking-widest text-ink-500">DKampus</p>
        <h1 class="mt-1 font-display text-3xl font-extrabold tracking-tightest">UI Kit</h1>
        <p class="mt-2 text-sm text-ink-500">Semua komponen × state. Hanya tersedia di lingkungan lokal.</p>
        <nav class="mt-6 hidden flex-col gap-1 text-sm font-semibold text-ink-600 md:flex" aria-label="Bagian">
            @foreach (['Warna', 'Tipografi', 'Tombol', 'Form', 'Status', 'Kartu', 'Modal & toast', 'Input lanjutan', 'Panel samping', 'Navigasi', 'Lain-lain'] as $s)
                <a href="#{{ \Illuminate\Support\Str::slug($s) }}" class="ui-press rounded-lg px-2.5 py-1.5 hover:bg-sunken hover:text-ink">{{ $s }}</a>
            @endforeach
        </nav>
        <p class="mt-6 hidden text-sm text-ink-500 md:block">Palet perintah: <x-ui.kbd>⌘</x-ui.kbd> <x-ui.kbd>K</x-ui.kbd></p>
    </aside>

    <main class="space-y-14" x-data>
        <section id="warna" class="space-y-4" x-motion.rise>
            <h2 class="font-display text-2xl font-bold tracking-tight">Warna</h2>
            @foreach ($palette as $group => $swatches)
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($swatches as $name => $hex)
                        <div class="overflow-hidden rounded-xl bg-surface shadow-card">
                            <div class="h-14" style="background: {{ $hex }}"></div>
                            <div class="p-2.5">
                                <p class="text-sm font-semibold">{{ $group }} {{ $name }}</p>
                                <p class="font-mono text-xs text-ink-500">{{ $hex }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>

        <section id="tipografi" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Tipografi</h2>
            <x-ui.card class="space-y-3">
                <p class="font-mono text-xs uppercase tracking-widest text-ink-500">Display · Bricolage Grotesque</p>
                <p class="font-display text-6xl font-extrabold leading-none tracking-tightest">Pesan dari kampus, tiba dalam 15 menit.</p>
                <p class="font-mono text-xs uppercase tracking-widest text-ink-500">Body · Source Sans 3</p>
                <p class="max-w-prose text-[17px] leading-relaxed text-ink-700">Jelajahi kelezatan kuliner di sekitar Telkom University dengan mudah dan cepat. Pilih warung, tambahkan catatan untuk restoran, lalu bayar dengan QRIS.</p>
                <p class="font-mono text-xs uppercase tracking-widest text-ink-500">Mono · Geist Mono</p>
                <p class="tnum font-mono text-sm text-ink-700">#TR00000007895675 &nbsp;·&nbsp; Rp 80.600 &nbsp;·&nbsp; Ongkir 3.000</p>
            </x-ui.card>
        </section>

        <section id="tombol" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Tombol</h2>
            <x-ui.card class="space-y-5">
                @foreach (['primary', 'secondary', 'soft', 'outline', 'ghost', 'danger'] as $v)
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="w-20 font-mono text-xs text-ink-500">{{ $v }}</span>
                        <x-ui.button :variant="$v" size="sm">Kecil</x-ui.button>
                        <x-ui.button :variant="$v" icon="plus">Tambah</x-ui.button>
                        <x-ui.button :variant="$v" size="lg" icon-right="arrow-right">Lanjut</x-ui.button>
                        <x-ui.button :variant="$v" disabled>Nonaktif</x-ui.button>
                        <x-ui.button :variant="$v" loading>Memuat</x-ui.button>
                    </div>
                @endforeach
                <p class="text-sm text-ink-500">Hover, tekan (<span class="font-mono">scale .97</span>), dan Tab untuk melihat <span class="font-mono">focus-visible</span>.</p>
            </x-ui.card>
        </section>

        <section id="form" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Form</h2>
            <x-ui.card class="grid gap-5 sm:grid-cols-2">
                <x-ui.field label="Email" for="e1" hint="Kami kirim kode verifikasi ke sini."><x-ui.input id="e1" type="email" icon="mail" placeholder="Masukkan email" /></x-ui.field>
                <x-ui.field label="Kata sandi" for="e2" error="Kata sandi minimal 8 karakter."><x-ui.input id="e2" type="password" icon="lock" :error="true" value="123" /></x-ui.field>
                <x-ui.field label="Kategori" for="e3"><x-ui.select id="e3"><option>Aneka Nasi</option><option>Bakmi</option><option>Seafood</option></x-ui.select></x-ui.field>
                <x-ui.field label="Nonaktif" for="e4"><x-ui.input id="e4" disabled value="Tidak bisa diubah" /></x-ui.field>
                <x-ui.field label="Catatan untuk restoran" for="e5" class="sm:col-span-2"><x-ui.textarea id="e5" placeholder="Opsional"></x-ui.textarea></x-ui.field>
            </x-ui.card>
        </section>

        <section id="status" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Status, chip, rating</h2>
            <x-ui.card class="flex flex-wrap items-center gap-3">
                @foreach (['Completed', 'On Delivery', 'Searching', 'Waiting For Driver', 'Canceled', 'Aktif', 'Non Aktif'] as $s)<x-ui.status :value="$s" />@endforeach
                <x-ui.badge tone="brand">PROMO</x-ui.badge><x-ui.badge>30%</x-ui.badge>
                <span class="mx-2 h-5 w-px bg-line"></span>
                <x-ui.chip :active="true">Semua</x-ui.chip><x-ui.chip>Customer</x-ui.chip><x-ui.chip>Driver</x-ui.chip>
                <span class="mx-2 h-5 w-px bg-line"></span>
                <x-ui.rating :value="4.7" /> <x-ui.avatar name="Yanto Joseph" /> <x-ui.avatar name="Kevin" size="lg" />
            </x-ui.card>
        </section>

        <section id="kartu" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Kartu & statistik</h2>
            <div class="grid gap-3 sm:grid-cols-3">
                <x-ui.stat label="Pesanan hari ini" value="128" meta="+12 dari kemarin" icon="receipt" />
                <x-ui.stat label="Kurir aktif" value="9" meta="3 sedang mengantar" icon="bike" />
                <x-ui.card :interactive="true" tag="a" href="#kartu"><p class="font-display text-lg font-bold">Kartu interaktif</p><p class="text-sm text-ink-500">Angkat 1px + bayangan saat hover.</p></x-ui.card>
            </div>
            <x-ui.card class="!p-0"><x-ui.empty-state icon="package" title="Ups, kamu belum mengambil pesanan" description="Pesanan yang kamu ambil akan muncul di sini." /></x-ui.card>
        </section>

        <section id="modal-toast" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Modal & toast</h2>
            <x-ui.card class="flex flex-wrap items-center gap-3">
                <x-ui.button data-modal-open="demo-konfirmasi">Konfirmasi</x-ui.button>
                <x-ui.button variant="danger" data-modal-open="demo-hapus">Hapus</x-ui.button>
                <x-ui.button variant="outline" data-modal-open="demo-logout">Logout</x-ui.button>
                <span class="mx-2 h-5 w-px bg-line"></span>
                <x-ui.button variant="secondary" onclick="DKToast.success('Pesanan berhasil dikirim')">Toast sukses</x-ui.button>
                <x-ui.button variant="secondary" onclick="DKToast.error('Gagal memuat data pesanan')">Toast error</x-ui.button>
            </x-ui.card>
            <x-ui.modal name="demo-konfirmasi" tone="success" title="Apakah Anda yakin?" description="Pastikan semua informasi sudah benar sebelum melanjutkan.">
                <x-slot:actions>
                    <x-ui.button block data-modal-close>Konfirmasi</x-ui.button>
                    <x-ui.button variant="outline" block data-modal-close>Batal</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
            <x-ui.modal name="demo-hapus" tone="danger" title="Hapus Akun" description="Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak bisa dibatalkan.">
                <x-slot:actions>
                    <x-ui.button variant="danger" block data-modal-close>Hapus</x-ui.button>
                    <x-ui.button variant="outline" block data-modal-close>Batal</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
            <x-ui.modal name="demo-logout" tone="danger" icon="log-out" title="Konfirmasi Logout" description="Apakah Anda yakin ingin keluar dari aplikasi?">
                <x-slot:actions>
                    <x-ui.button variant="danger" block data-modal-close>Logout</x-ui.button>
                    <x-ui.button variant="outline" block data-modal-close>Batal</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
        </section>

        <section id="input-lanjutan" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Input lanjutan</h2>
            <x-ui.card class="grid gap-5 sm:grid-cols-2">
                <x-ui.field label="Kata sandi (tampil/sembunyi)" for="p1"><x-ui.password id="p1" name="demo_password" value="rahasia123" /></x-ui.field>
                <x-ui.field label="Kata sandi salah" for="p2" error="Kata sandi minimal 8 karakter."><x-ui.password id="p2" name="demo_password2" :error="true" /></x-ui.field>
                <x-ui.field label="Kode OTP (6 digit, tempel didukung)" class="sm:col-span-2"><x-ui.otp name="demo_otp" :autosubmit="false" /></x-ui.field>
                <x-ui.field label="Bukti pembayaran (pilih file)" for="f1"><x-ui.file-input name="demo_file" id="f1" /></x-ui.field>
                <x-ui.field label="Rating (klik atau panah kiri/kanan)"><x-ui.star-input name="demo_rating" :value="3" /></x-ui.field>
                <x-ui.field label="Gambar resto (pratinjau)" for="i1" class="sm:col-span-2"><x-ui.image-input name="demo_image" id="i1" label="Pilih gambar resto" icon="store" /></x-ui.field>
            </x-ui.card>
            <div class="grid gap-3 sm:grid-cols-3">
                <x-ui.photo src="/tidak-ada.jpg" alt="Gambar gagal dimuat tampil sebagai ikon" icon="utensils" class="aspect-[4/3] rounded-2xl" />
                <x-ui.photo src="/tidak-ada.jpg" alt="" icon="store" :grayscale="true" class="aspect-[4/3] rounded-2xl" />
                <x-ui.card class="flex items-center justify-center"><x-ui.avatar name="Rina Pratama" size="xl" /></x-ui.card>
            </div>
        </section>

        <section id="panel-samping" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Panel samping & paginasi</h2>
            <x-ui.card class="flex flex-wrap items-center gap-3">
                <x-ui.button data-modal-open="demo-sheet" icon="users">Buka panel (slide-over / bottom sheet)</x-ui.button>
            </x-ui.card>
            <x-ui.sheet name="demo-sheet" title="Detail Akun" description="Informasi akun dan pengaturan role">
                <div class="space-y-4">
                    <x-ui.field label="Nama Lengkap" for="s1"><x-ui.input id="s1" value="Benjamin Wong" readonly /></x-ui.field>
                    <x-ui.field label="Role" for="s2"><x-ui.select id="s2"><option>Customer</option><option>Driver</option></x-ui.select></x-ui.field>
                </div>
                <x-slot:footer>
                    <div class="flex flex-col gap-2">
                        <x-ui.button block data-modal-close>Simpan</x-ui.button>
                        <x-ui.button variant="danger-soft" icon="trash-2" block>Hapus Akun</x-ui.button>
                    </div>
                </x-slot:footer>
            </x-ui.sheet>
            @php($demoPages = new \Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 47, 10, 3, ['path' => url('/_ui')]))
            <x-ui.card>{{ $demoPages->links() }}</x-ui.card>
        </section>

        <section id="navigasi" class="space-y-4" x-motion.rise.inview>
            <h2 class="font-display text-2xl font-bold tracking-tight">Navigasi</h2>
            <p class="text-sm text-ink-500">Bottom-nav tetap menempel di bawah layar (mobile). Bump oranye menandai tab aktif.</p>
            <x-ui.tooltip text="Tooltip hanya di perangkat dengan mouse"><x-ui.button variant="secondary" icon="info">Arahkan kursor</x-ui.button></x-ui.tooltip>
        </section>

        <section id="lain-lain" class="space-y-2 pb-24">
            <h2 class="font-display text-2xl font-bold tracking-tight">Lain-lain</h2>
            <p class="text-sm text-ink-500">Ikon: <x-ui.icon name="house" class="inline size-5" /> <x-ui.icon name="store" class="inline size-5" /> <x-ui.icon name="bike" class="inline size-5" /> <x-ui.icon name="qr-code" class="inline size-5" /> (Lucide inline, <span class="font-mono">x-ui.icon</span>).</p>
        </section>
    </main>
</div>

<x-ui.bottom-nav :items="$nav" class="md:hidden" />
<x-ui.palette :items="[
    ['label' => 'Warna', 'href' => '#warna', 'hint' => 'token'],
    ['label' => 'Tombol', 'href' => '#tombol', 'hint' => 'variants'],
    ['label' => 'Form', 'href' => '#form', 'hint' => 'input, select'],
    ['label' => 'Modal & toast', 'href' => '#modal-toast', 'hint' => 'dialog'],
    ['label' => 'Input lanjutan', 'href' => '#input-lanjutan', 'hint' => 'password, otp, gambar'],
    ['label' => 'Panel samping', 'href' => '#panel-samping', 'hint' => 'sheet, paginasi'],
]" />
@endsection
