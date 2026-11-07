@php
    $currentPage = $products->currentPage();
    $lastPage = $products->lastPage();
    $startPage = max(1, $currentPage - 1);
    $endPage = min($lastPage, $currentPage + 1);
@endphp

@if ($products->hasPages())
    <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
        {{-- Previous --}}
        @if ($products->onFirstPage())
            <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-line/60 bg-white/60 px-3 py-2 text-sm font-medium text-muted/50">
                Previous
            </span>
        @else
            <a href="{{ $products->previousPageUrl() }}"
               class="inline-flex items-center rounded-lg border border-line bg-white px-3 py-2 text-sm font-medium text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                Previous
            </a>
        @endif

        {{-- Page numbers --}}
        @if ($startPage > 1)
            <a href="{{ $products->url(1) }}"
               class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-line bg-white px-3 text-sm font-medium text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                1
            </a>
            @if ($startPage > 2)
                <span class="px-1 text-sm text-muted">…</span>
            @endif
        @endif

        @for ($page = $startPage; $page <= $endPage; $page++)
            @if ($page === $currentPage)
                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-brand-600 bg-brand-600 px-3 text-sm font-semibold text-white shadow-sm">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $products->url($page) }}"
                   class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-line bg-white px-3 text-sm font-medium text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                    {{ $page }}
                </a>
            @endif
        @endfor

        @if ($endPage < $lastPage)
            @if ($endPage < $lastPage - 1)
                <span class="px-1 text-sm text-muted">…</span>
            @endif
            <a href="{{ $products->url($lastPage) }}"
               class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-line bg-white px-3 text-sm font-medium text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                {{ $lastPage }}
            </a>
        @endif

        {{-- Next --}}
        @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}"
               class="inline-flex items-center rounded-lg border border-line bg-white px-3 py-2 text-sm font-medium text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                Next
            </a>
        @else
            <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-line/60 bg-white/60 px-3 py-2 text-sm font-medium text-muted/50">
                Next
            </span>
        @endif
    </nav>
@endif
