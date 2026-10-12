{{-- Tab "Pesanan | Proses" (Figma): keranjang dan status pesanan --}}
@props(['active' => 'pesanan'])
<div {{ $attributes->class('inline-flex rounded-xl bg-sunken p-1 shadow-inset') }} role="tablist" aria-label="Pesanan">
    @foreach (['pesanan' => ['Pesanan', '/pesanan'], 'proses' => ['Proses', '/pesanan/status']] as $key => [$label, $href])
        <a href="{{ url($href) }}" role="tab" aria-selected="{{ $active === $key ? 'true' : 'false' }}"
           class="ui-press min-w-28 rounded-lg px-5 py-2 text-center text-[15px] font-semibold {{ $active === $key ? 'bg-surface text-ink shadow-card' : 'text-ink-500 hover:text-ink' }}">{{ $label }}</a>
    @endforeach
</div>
