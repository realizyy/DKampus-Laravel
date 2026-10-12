{{-- Tombol hati: toggle favorit lewat POST /favoritStore/{id}. Tamu diarahkan ke halaman masuk. Dikelola resources/js/lib/favorites.js --}}
@props(['menu'])
@php($on = \App\Support\Favorites::has((int) $menu->id))
<button type="button" data-fav="{{ $menu->id }}" aria-pressed="{{ $on ? 'true' : 'false' }}" aria-label="Favorit" @guest data-login="{{ route('login') }}" @endguest
        {{ $attributes->class('ui-press group/fav grid size-9 place-items-center rounded-full bg-surface/95 text-ink-600 shadow-hairline hover:text-brick-600 aria-pressed:text-brick-600') }}>
    <x-ui.icon name="heart" class="size-[18px] group-aria-pressed/fav:fill-brick-600" />
</button>
