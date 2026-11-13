@extends('dashboard-user.layouts.app')

@section('title', 'Shopping Cart - Decak Handmade')

@section('content')
<div x-data="{
    cart: @js($cart),
    loading: false,
    error: null,
    csrf: document.querySelector('meta[name=csrf-token]')?.content ?? '',
    init() {
        window.addEventListener('cart-updated', (e) => {
            if (e.detail) {
                this.cart = e.detail;
            }
        });
    },
    get items() {
        return this.cart.items || [];
    },
    get totalItemsCount() {
        return this.cart.total_quantity || 0;
    },
    get subtotal() {
        return this.cart.subtotal || 0;
    },
    formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    },
    async updateQty(key, nextQty) {
        this.error = null;
        this.loading = true;
        try {
            const res = await fetch('{{ url('/cart/items') }}/' + encodeURIComponent(key), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                },
                body: JSON.stringify({ quantity: nextQty }),
            });
            const data = await res.json();
            if (!res.ok) {
                this.error = data.message || 'Failed to update quantity.';
            } else {
                this.cart = data;
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            }
        } catch (_) {
            this.error = 'Network error occurred. Please try again.';
        } finally {
            this.loading = false;
        }
    },
    async removeItem(key) {
        this.error = null;
        this.loading = true;
        try {
            const res = await fetch('{{ url('/cart/items') }}/' + encodeURIComponent(key), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                },
            });
            if (res.ok) {
                const data = await res.json();
                this.cart = data;
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            }
        } finally {
            this.loading = false;
        }
    }
}">
    @if (session('error'))
        <div class="mb-6 rounded-xl border border-[#f0c9b9] bg-[#fdf3ee] px-4 py-3 text-sm text-[#b93826]">
            {{ session('error') }}
        </div>
    @endif

    <div x-show="error" x-cloak class="mb-6 rounded-xl border border-[#f0c9b9] bg-[#fdf3ee] px-4 py-3 text-sm text-[#b93826]" x-text="error"></div>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('home') }}" class="hover:text-[#2c2420]">Home</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Shopping cart</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
            Shopping cart
        </h1>
        <p class="text-sm text-[#7a6f68]">
            <span x-text="items.length"></span> products · <span x-text="totalItemsCount"></span> items
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Items Column (Left) -->
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-[#7a6f68] pb-3 border-b border-[#e5dfd8]">
                <span>PRODUCT</span>
                <span>QUANTITY & PRICE</span>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-[#e5dfd8]/70">
                <template x-for="item in items" :key="item.key">
                    <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <!-- Product Info Left -->
                        <div class="flex items-center gap-4 sm:gap-6">
                            <img :src="item.image" :alt="item.name" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs bg-white">
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-[#2c2420]" x-text="item.name"></h3>
                                <p x-show="item.variant_name" class="text-xs sm:text-sm text-[#7a6f68]" x-text="'Variant: ' + item.variant_name"></p>
                                <template x-for="c in item.customizations" :key="c.option_id">
                                    <p class="text-xs text-[#7a6f68]" x-text="c.option_name + (c.value ? ': ' + c.value : '')"></p>
                                </template>
                                <p class="text-xs sm:text-sm text-[#7a6f68]">
                                    <span x-text="formatRupiah(item.unit_price)"></span> / item
                                </p>
                                <button type="button" @click="removeItem(item.key)" :disabled="loading" class="text-xs text-[#7a6f68] hover:text-[#c06547] flex items-center gap-1.5 pt-1 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>

                        <!-- Quantity and Price Right -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-3">
                            <!-- Qty Control -->
                            <div class="inline-flex items-center rounded-lg border border-[#d3c9c1] bg-white">
                                <button type="button" @click="updateQty(item.key, item.quantity - 1)" :disabled="loading" class="w-9 h-9 flex items-center justify-center text-[#5e5449] hover:bg-[#f5eee6] rounded-l-lg transition-colors">
                                    –
                                </button>
                                <span class="w-10 text-center text-sm font-semibold text-[#2c2420]" x-text="item.quantity"></span>
                                <button type="button" @click="updateQty(item.key, item.quantity + 1)" :disabled="loading || item.quantity >= item.max_stock" class="w-9 h-9 flex items-center justify-center text-[#5e5449] hover:bg-[#f5eee6] rounded-r-lg transition-colors disabled:opacity-40">
                                    +
                                </button>
                            </div>

                            <!-- Line Total -->
                            <div class="text-base font-bold text-[#2c2420]" x-text="formatRupiah(item.line_total)"></div>
                        </div>
                    </div>
                </template>

                <div x-show="items.length === 0" class="py-12 text-center text-[#7a6f68]">
                    Your shopping cart is empty.
                    <div class="mt-4">
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center rounded-xl bg-[#c06547] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#a8543b] transition-colors">
                            Start Shopping
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2c2420] hover:text-[#c06547] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Continue shopping</span>
                </a>
            </div>
        </div>

        <!-- Summary Column (Right) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="rounded-2xl bg-[#f5eee6]/80 border border-[#e8dfd5] p-6 shadow-xs space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-[#2c2420]">Order summary</h3>
                    <p class="text-xs text-[#7a6f68] mt-1">
                        <span x-text="items.length"></span> products, <span x-text="totalItemsCount"></span> items in cart.
                    </p>
                </div>

                <!-- Breakdown White Box -->
                <div class="bg-white rounded-xl border border-[#e5dfd8] p-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#7a6f68]">Subtotal</span>
                        <span class="font-medium text-[#2c2420]" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="h-px bg-[#e5dfd8]"></div>
                    <div class="flex items-center justify-between text-base font-bold text-[#2c2420] pt-1">
                        <span>Total</span>
                        <span class="text-[#c06547]" x-text="formatRupiah(subtotal)"></span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a
                        href="{{ route('user.checkout') }}"
                        x-show="items.length > 0"
                        class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs"
                    >
                        Proceed to checkout
                    </a>
                    <a href="{{ route('catalog.index') }}" class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl border border-[#c06547] text-[#c06547] font-semibold text-sm hover:bg-[#fbf5ef] transition-colors">
                        Continue shopping
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
