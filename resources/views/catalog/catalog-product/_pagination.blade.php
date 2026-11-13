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

<nav class="mt-10 border-t border-[#e5dfd8] pt-6" aria-label="Navigasi Halaman">
    <div x-data="{ currentPage: {{ $currentPage }} }" class="flex flex-wrap items-center justify-center gap-2">

        {{-- Previous --}}
        @if ($products->onFirstPage())
            <span aria-disabled="true"
                  class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-xs font-medium text-[#a99e96] cursor-not-allowed">
                ← Sebelumnya
            </span>
        @else
            <a href="{{ $products->previousPageUrl() }}"
               aria-label="Halaman sebelumnya"
               class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-xs font-medium text-[#5e5449] hover:bg-[#f5eee6] hover:text-[#c06547] transition-colors">
                ← Sebelumnya
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($paginationPages as $page)
            @if ($page === '...')
                <span class="inline-flex h-9 w-9 items-center justify-center text-xs text-[#7a6f68]">…</span>
            @elseif ($page === $currentPage)
                <span aria-current="page"
                      class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#c06547] text-xs font-bold text-white shadow-xs">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $products->url($page) }}"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e0ddd8] bg-white text-xs font-medium text-[#2c2420] transition-colors hover:bg-[#f5eee6] hover:border-[#c06547]">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}"
               aria-label="Halaman berikutnya"
               class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-xs font-medium text-[#5e5449] hover:bg-[#f5eee6] hover:text-[#c06547] transition-colors">
                Selanjutnya →
            </a>
        @else
            <span aria-disabled="true"
                  class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-xs font-medium text-[#a99e96] cursor-not-allowed">
                Selanjutnya →
            </span>
        @endif
    </div>
</nav>
