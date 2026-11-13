{{-- ── Result counter + sort ── --}}
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="text-xs text-[#7a6f68]">
        Menampilkan <span class="font-semibold text-[#2c2420]">{{ $products->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#2c2420]">{{ $products->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-[#2c2420]">{{ $products->total() }}</span> produk
    </p>

    @include('catalog.sort-bar.index')
</div>
