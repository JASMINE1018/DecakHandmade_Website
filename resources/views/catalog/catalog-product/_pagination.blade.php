@php
    $currentPage = $products->currentPage();
    $lastPage = $products->lastPage();

    // Susun nomor halaman: 1 … 4 5 6 … 20
    $paginationPages = [];

    if ($lastPage <= 7) {
        $paginationPages = range(1, $lastPage);
    } else {
        $paginationPages[] = 1;

        if ($currentPage > 3) {
            $paginationPages[] = '...';
        }

        $startPage = max(2, $currentPage - 1);
        $endPage = min($lastPage - 1, $currentPage + 1);

        for ($page = $startPage; $page <= $endPage; $page++) {
            $paginationPages[] = $page;
        }

        if ($currentPage < $lastPage - 2) {
            $paginationPages[] = '...';
        }

        $paginationPages[] = $lastPage;
    }
@endphp

<nav class="mt-10 border-t border-line pt-6" aria-label="Pagination">
        <div x-data="{ currentPage: {{ $currentPage }} }" class="flex flex-row items-center justify-center gap-1.5">

            {{-- Previous --}}
            @if ($products->onFirstPage())
                <span aria-disabled="true"
                      class="inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-none bg-slate-200 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}"
                   aria-label="Halaman sebelumnya"
                   class="inline-flex h-10 w-10 items-center justify-center rounded-none bg-slate-400 text-white transition-colors duration-200 hover:bg-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($paginationPages as $page)
                @if ($page === '...')
                    <span class="inline-flex h-10 w-10 items-center justify-center text-sm text-muted">…</span>
                @elseif ($page === $currentPage)
                    <span aria-current="page"
                          class="inline-flex h-10 w-10 items-center justify-center rounded-none border border-brand-600 bg-brand-600 text-sm font-semibold text-white">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $products->url($page) }}"
                       @click="currentPage = {{ $page }}"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-none border border-line bg-white text-sm font-medium text-ink transition-colors duration-200 hover:bg-brand-50">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}"
                   aria-label="Halaman berikutnya"
                   class="inline-flex h-10 w-10 items-center justify-center rounded-none border border-line bg-transparent text-muted transition-colors duration-200 hover:bg-brand-50 hover:text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true"
                      class="inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-none border border-line/60 bg-transparent text-muted/40">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
