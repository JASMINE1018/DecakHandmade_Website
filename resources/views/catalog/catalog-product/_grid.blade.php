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

                $productImage = null;
                if ($product->gallery && count($product->gallery) > 0) {
                    $first = $product->gallery[0];
                    $productImage = str_starts_with($first, 'http') || str_starts_with($first, '/')
                        ? $first
                        : asset('storage/' . ltrim($first, '/'));
                } else {
                    $productImage = asset('images/products/tote-senja.png');
                }

                $isWishlisted = in_array($product->id, $wishlistedProductIds ?? [], true);
            @endphp

            <div x-data="{
                    wishlisted: {{ $isWishlisted ? 'true' : 'false' }},
                    loading: false,
                    async toggleWishlist() {
                        if (this.loading) return;
                        this.loading = true;
                        try {
                            const res = await fetch('{{ route('user.wishlist.toggle', $product) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                                    'Accept': 'application/json'
                                }
                            });
                            if (res.status === 401) {
                                window.location.href = '{{ route('user.login') }}';
                                return;
                            }
                            if (res.ok) {
                                const data = await res.json();
                                this.wishlisted = data.status === 'added';
                            }
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                        }
                    }
                }"
                class="group overflow-hidden rounded-2xl border border-[#e5dfd8] bg-white shadow-2xs transition-all hover:shadow-md hover:border-[#c06547]/40 flex flex-col justify-between">
                <div>
                    {{-- Product image & Wishlist --}}
                    <div class="relative aspect-square overflow-hidden bg-[#faf7f2]">
                        <a href="{{ route('catalog.product.show', $product->slug) }}" class="block h-full w-full">
                            <img src="{{ $productImage }}"
                                 alt="{{ $product->name }}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </a>

                        {{-- Wishlist button --}}
                        <button type="button"
                                @click="toggleWishlist()"
                                :disabled="loading"
                                aria-label="Toggle wishlist for {{ $product->name }}"
                                :class="wishlisted ? 'bg-white text-[#c06547]' : 'bg-white/90 text-[#7a6f68] hover:text-[#c06547]'"
                                class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full shadow-xs backdrop-blur transition-all hover:bg-white hover:scale-110 focus:outline-none cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="wishlisted ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Product info --}}
                    <div class="p-4">
                        <a href="{{ route('catalog.product.show', $product->slug) }}" class="block">
                            <h3 class="text-sm font-semibold text-[#2c2420] hover:text-[#c06547] transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                        </a>
                        <p class="mt-1 text-sm font-bold text-[#2c2420]">
                            Rp{{ number_format($product->base_price, 0, ',', '.') }}
                        </p>

                        <p class="mt-1 text-xs text-[#7a6f68] truncate">
                            {{ $product->category?->name ?? 'Handmade' }}{{ $variantLabel !== '' ? ' · ' . $variantLabel : '' }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @include('catalog.catalog-product._pagination')
@else
    {{-- Empty state --}}
    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-[#e5dfd8] bg-white py-20 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mb-4 h-16 w-16 text-[#d3c9c1]">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        <h3 class="text-base font-semibold text-[#2c2420]">Tidak ada produk ditemukan</h3>
        <p class="mt-1 text-xs text-[#7a6f68]">Coba gunakan kata kunci atau filter kategori lainnya.</p>
    </div>
@endif
