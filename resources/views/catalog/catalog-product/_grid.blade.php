{{-- ── Product Grid ── --}}
@if($products->count())
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($products as $product)
            @php
                $variantLabel = $product->variants
                    ->take(2)
                    ->pluck('name')
                    ->filter()
                    ->implode(' · ');
            @endphp

            <div class="group overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition-shadow hover:shadow-md">
                {{-- Product image --}}
                <div class="relative aspect-square overflow-hidden bg-brand-50">
                    @if($product->gallery && count($product->gallery) > 0)
                        <img src="{{ asset('storage/' . $product->gallery[0]) }}"
                             alt="{{ $product->name }}"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="h-16 w-16 text-brand-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                            </svg>
                        </div>
                    @endif

                    {{-- Wishlist button --}}
                    <button type="button"
                            aria-label="Tambahkan {{ $product->name }} ke wishlist"
                            class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-ink shadow-sm backdrop-blur transition-colors hover:bg-white hover:text-brand-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                </div>

                {{-- Product info --}}
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-ink line-clamp-2">{{ $product->name }}</h3>
                    <p class="mt-1 text-base font-bold text-brand-600">
                        Rp{{ number_format($product->base_price, 0, ',', '.') }}
                    </p>

                    @if($variantLabel !== '')
                        <p class="mt-1 text-xs text-muted">{{ $variantLabel }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @include('catalog.catalog-product._pagination')
@else
    {{-- Empty state --}}
    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-line bg-white py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mb-4 h-16 w-16 text-brand-200">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        <h3 class="text-lg font-semibold text-ink">Belum ada produk</h3>
        <p class="mt-1 text-sm text-muted">Produk yang dicari tidak ditemukan. Coba kata kunci lain.</p>
    </div>
@endif
