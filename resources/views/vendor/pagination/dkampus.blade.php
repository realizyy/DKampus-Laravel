{{-- Paginasi DKampus: ringkasan rentang (mono) + sebelumnya/berikutnya + nomor halaman --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginasi" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        <p class="font-mono text-xs text-ink-500">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </p>
        <ul class="flex items-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="grid size-9 place-items-center rounded-lg text-ink-300" aria-disabled="true"><x-ui.icon name="chevron-left" class="size-4" /></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya" class="ui-press grid size-9 place-items-center rounded-lg bg-surface text-ink shadow-hairline hover:bg-sunken"><x-ui.icon name="chevron-left" class="size-4" /></a>
                @endif
            </li>
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="grid size-9 place-items-center font-mono text-sm text-ink-400">…</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="tnum grid size-9 place-items-center rounded-lg bg-ink font-mono text-sm font-semibold text-canvas">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="ui-press tnum grid size-9 place-items-center rounded-lg font-mono text-sm text-ink-600 hover:bg-sunken hover:text-ink">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya" class="ui-press grid size-9 place-items-center rounded-lg bg-surface text-ink shadow-hairline hover:bg-sunken"><x-ui.icon name="chevron-right" class="size-4" /></a>
                @else
                    <span class="grid size-9 place-items-center rounded-lg text-ink-300" aria-disabled="true"><x-ui.icon name="chevron-right" class="size-4" /></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
