{{--
    Kartu order aktif kurir (halaman Home). Satu kartu = satu order di Firebase onProgress/{custId}-{courId}.
    Tombol Selesaikan/Batalkan membuka modal dan mengisi custId + ringkasan dari kartu INI (template #order-summary-{custId}).
--}}
@props(['item', 'custId', 'phoneUmkm' => '-', 'phoneCust' => '-'])
@php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $safe = fn ($u) => preg_match('#^https?://#i', (string) $u) === 1 ? $u : null;
    $tel = fn ($n) => preg_replace('/[^0-9+]/', '', (string) $n);
    $subtotal = (int) ($item['total'] ?? 0);
    $ongkir = (int) ($item['ongkir'] ?? 0);
    $summaryId = 'order-summary-'.$custId;
    $lines = collect($item['orders'] ?? []);
@endphp
<article {{ $attributes->class('flex h-full flex-col overflow-hidden rounded-2xl bg-surface shadow-card') }} x-motion.rise>
    <header class="flex items-start gap-3 border-b border-line/70 p-4">
        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-800"><x-ui.icon name="store" class="size-5" /></span>
        <div class="min-w-0 flex-1">
            <h2 class="truncate font-display text-lg font-bold tracking-tight text-ink">{{ $item['nama_umkm'] }}</h2>
            <p class="truncate text-sm text-ink-500">Antar ke <span class="font-semibold text-ink-700">{{ $item['nama_penerima'] }}</span></p>
        </div>
        <x-ui.status value="On Delivery" />
    </header>

    <dl class="grid flex-1 content-start gap-x-6 gap-y-4 p-4 text-[15px] sm:grid-cols-2">
        <div class="space-y-1">
            <dt class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Restoran</dt>
            <dd>
                @if ($safe($item['umkm_link_address'] ?? null))
                    <a href="{{ $safe($item['umkm_link_address']) }}" target="_blank" rel="noopener" class="ui-press inline-flex items-start gap-1 font-semibold text-brand-700 underline-offset-2 hover:underline">{{ $item['umkm_address'] ?? '-' }}<x-ui.icon name="arrow-up-right" class="mt-0.5 size-4 shrink-0" /></a>
                @else
                    <span class="font-semibold text-ink-700">{{ $item['umkm_address'] ?? '-' }}</span>
                @endif
            </dd>
            <dd class="font-mono text-sm text-ink-600">
                @if ($tel($phoneUmkm) !== '')<a href="tel:{{ $tel($phoneUmkm) }}" class="hover:text-brand-700 hover:underline">{{ $phoneUmkm }}</a>@else - @endif
            </dd>
        </div>
        <div class="space-y-1">
            <dt class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Penerima</dt>
            <dd>
                <span class="font-semibold text-ink-700">{{ $item['nama_penerima'] }}</span>
            </dd>
            <dd>
                @if ($safe($item['cust_link_address'] ?? null))
                    <a href="{{ $safe($item['cust_link_address']) }}" target="_blank" rel="noopener" class="ui-press inline-flex items-start gap-1 text-brand-700 underline-offset-2 hover:underline">{{ $item['cust_address'] ?? '-' }}<x-ui.icon name="arrow-up-right" class="mt-0.5 size-4 shrink-0" /></a>
                @else
                    <span class="text-ink-600">{{ $item['cust_address'] ?? '-' }}</span>
                @endif
            </dd>
            <dd class="font-mono text-sm text-ink-600">
                @if ($tel($phoneCust) !== '')<a href="tel:{{ $tel($phoneCust) }}" class="hover:text-brand-700 hover:underline">{{ $phoneCust }}</a>@else - @endif
            </dd>
        </div>
        <div class="space-y-1.5 sm:col-span-2">
            <dt class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Pesanan</dt>
            <dd>
                <ul class="space-y-1.5">
                    @foreach ($lines as $line)
                        <li class="flex items-baseline gap-2">
                            <span class="tnum font-mono text-sm font-semibold text-ink-700">{{ $line['jumlah'] }}×</span>
                            <span class="text-ink">{{ $line['nama'] }}@if (($line['catatan'] ?? '-') !== '-')<span class="text-ink-500"> — {{ $line['catatan'] }}</span>@endif</span>
                        </li>
                    @endforeach
                </ul>
            </dd>
        </div>
    </dl>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line/70 bg-sunken/50 p-4">
        <div class="flex gap-6 font-mono text-sm text-ink-600">
            <span>Subtotal <b class="tnum font-semibold text-ink">{{ $rp($subtotal) }}</b></span>
            <span>Ongkir <b class="tnum font-semibold text-ink">{{ $rp($ongkir) }}</b></span>
        </div>
        <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
            <x-ui.button variant="danger-soft" icon="x" data-modal-open="cancel" data-cust-id="{{ $custId }}" data-summary="#{{ $summaryId }}">Batalkan</x-ui.button>
            <x-ui.button variant="success" icon="check" data-modal-open="complete" data-cust-id="{{ $custId }}" data-summary="#{{ $summaryId }}">Selesaikan</x-ui.button>
        </div>
    </div>

    {{-- Ringkasan untuk modal (disalin JS saat modal dibuka) --}}
    <template id="{{ $summaryId }}">
        <div class="rounded-xl bg-sunken p-3 text-sm">
            <p class="font-mono text-[11px] uppercase tracking-widest text-ink-500">Pesanan</p>
            <ul class="mt-1 space-y-0.5 text-ink">
                @foreach ($lines as $line)
                    <li><span class="tnum font-mono font-semibold">{{ $line['jumlah'] }}×</span> {{ $line['nama'] }}</li>
                @endforeach
            </ul>
            <dl class="mt-2 space-y-0.5 border-t border-line pt-2 font-mono text-ink-600">
                <div class="flex justify-between"><dt>Subtotal</dt><dd class="tnum">{{ $rp($subtotal) }}</dd></div>
                <div class="flex justify-between"><dt>Ongkir</dt><dd class="tnum">{{ $rp($ongkir) }}</dd></div>
                <div class="flex justify-between font-semibold text-ink"><dt>Total</dt><dd class="tnum">{{ $rp($subtotal + $ongkir) }}</dd></div>
            </dl>
        </div>
    </template>
</article>
