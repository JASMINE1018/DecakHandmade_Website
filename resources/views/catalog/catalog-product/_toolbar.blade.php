{{-- ── Result counter + sort ── --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-muted">
        Showing
        <span class="font-semibold text-ink">{{ $products->firstItem() ?? 0 }}</span>–<span class="font-semibold text-ink">{{ $products->lastItem() ?? 0 }}</span>
        of <span class="font-semibold text-ink">{{ $products->total() }}</span> products
    </p>

    @include('catalog.sort-bar.index')
</div>
