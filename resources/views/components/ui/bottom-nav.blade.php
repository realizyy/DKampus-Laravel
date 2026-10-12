{{--
    Bottom-nav mobile (Figma: 4 tab + "bump" oranye di bawah tab aktif).
    $items: [['label' => 'Home', 'href' => '/', 'icon' => 'house', 'active' => true], ...]
    Bump bergeser dari tab sebelumnya ke tab aktif dengan pegas (posisi lama disimpan di sessionStorage).
--}}
@props(['items' => []])
@php
    $count = max(count($items), 1);
    $active = collect($items)->search(fn ($i) => ! empty($i['active']));
    $active = $active === false ? 0 : $active;
@endphp
<nav x-data="bottomNav({{ $count }}, {{ $active }})" aria-label="Navigasi utama"
     {{ $attributes->class('fixed inset-x-0 bottom-0 z-40 bg-surface pb-[env(safe-area-inset-bottom)] shadow-nav') }}>
    <ul class="relative grid" style="grid-template-columns: repeat({{ $count }}, minmax(0, 1fr))">
        <span x-ref="bump" class="pointer-events-none absolute bottom-0 h-1.5 w-11 -translate-x-1/2 rounded-t-full bg-brand-500" style="left: {{ (($active + 0.5) / $count) * 100 }}%" aria-hidden="true"></span>
        @foreach ($items as $item)
            @php($on = ! empty($item['active']))
            <li>
                <a href="{{ $item['href'] }}" @if ($on) aria-current="page" @endif
                   class="ui-press flex flex-col items-center gap-1 px-1 pb-3 pt-2.5 text-[11px] font-semibold tracking-wide {{ $on ? 'text-brand-700' : 'text-ink-500 hover:text-ink' }}">
                    <x-ui.icon :name="$item['icon']" class="size-6 {{ $on ? 'text-brand-600' : '' }}" :stroke="$on ? 2.25 : 2" />
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
