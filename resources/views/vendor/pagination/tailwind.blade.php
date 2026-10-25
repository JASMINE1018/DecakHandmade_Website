@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-muted">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-semibold text-ink">{{ $paginator->count() }}</span>
            @endif
            dari <span class="font-semibold text-ink">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex flex-wrap items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-line bg-slate-50 text-slate-400" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-200 bg-white text-brand-700 transition-colors hover:bg-brand-50" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 min-w-9 cursor-default items-center justify-center rounded-lg border border-line bg-white px-3 text-sm text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-brand-200 bg-white px-3 text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-200 bg-white text-brand-700 transition-colors hover:bg-brand-50" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg border border-line bg-slate-50 text-slate-400" aria-disabled="true" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
