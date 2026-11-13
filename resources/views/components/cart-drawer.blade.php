<div
    x-data="{
        open: false,
        loading: false,
        error: null,
        cart: { items: [], subtotal: 0, total_quantity: 0, count: 0 },
        csrf: document.querySelector('meta[name=csrf-token]')?.content ?? '',
        init() {
            this.fetchSummary();
            window.addEventListener('open-cart-drawer', () => {
                this.open = true;
                this.fetchSummary();
            });
            window.addEventListener('cart-updated', (e) => {
                if (e.detail) {
                    this.cart = e.detail;
                    this.syncBadges();
                } else {
                    this.fetchSummary();
                }
            });
        },
        syncBadges() {
            const count = this.cart.total_quantity || 0;
            document.querySelectorAll('[data-cart-badge]').forEach((el) => {
                el.textContent = count;
                el.classList.toggle('hidden', count <= 0);
            });
        },
        async fetchSummary() {
            try {
                const res = await fetch('{{ route('cart.summary') }}', {
                    headers: { 'Accept': 'application/json' },
                });
                if (res.ok) {
                    this.cart = await res.json();
                    this.syncBadges();
                }
            } catch (_) {}
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
                    this.syncBadges();
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                }
            } catch (_) {
                this.error = 'Network error occurred.';
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
                    this.syncBadges();
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                }
            } finally {
                this.loading = false;
            }
        },
        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }
    }"
    @keydown.escape.window="open = false"
    class="relative z-[70]"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-[#2c2420]/45 backdrop-blur-[2px]"
    ></div>

    <!-- Drawer Panel -->
    <aside
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 flex w-full max-w-md flex-col bg-[#faf7f2] shadow-2xl border-l border-[#e5dfd8]"
        aria-label="Shopping Cart"
    >
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-[#e5dfd8] px-6 py-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[#c06547]">Shopping Cart</p>
                <h2 class="mt-0.5 text-2xl font-bold text-[#2c2420]" x-text="cart.items.length > 0 ? 'Products' : 'Shopping Cart'"></h2>
            </div>
            <button
                type="button"
                @click="open = false"
                aria-label="Close cart"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#5e5449] hover:bg-[#efe8df] hover:text-[#2c2420] transition-colors cursor-pointer"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Error Banner -->
        <div x-show="error" x-cloak class="mx-6 mt-4 rounded-xl bg-[#fdf3ee] border border-[#f0c9b9] px-4 py-2.5 text-xs text-[#b93826]" x-text="error"></div>

        <!-- Empty State -->
        <div x-show="cart.items.length === 0" class="flex flex-1 flex-col items-center justify-center px-6 text-center">
            <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-white border border-[#e5dfd8] shadow-xs text-[#c06547]">
                <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#2c2420]">Your Cart is Empty</h3>
            <p class="mt-1.5 text-sm text-[#7a6f68]">Looks like you haven't added any handcrafted pieces yet.</p>
            <a
                href="{{ route('catalog.index') }}"
                @click="open = false"
                class="mt-6 inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-[#c06547] px-6 py-3 text-sm font-semibold text-white hover:bg-[#a8543b] transition-colors shadow-xs"
            >
                Start Shopping
            </a>
        </div>

        <!-- Filled State -->
        <div x-show="cart.items.length > 0" class="flex flex-1 flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto px-6 py-4 divide-y divide-[#e5dfd8]">
                <template x-for="item in cart.items" :key="item.key">
                    <div class="py-4 flex items-start gap-4">
                        <img
                            :src="item.image"
                            :alt="item.name"
                            class="h-18 w-18 shrink-0 rounded-xl border border-[#e0ddd8] object-cover bg-white"
                        >
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="text-sm font-bold text-[#2c2420] truncate" x-text="item.name"></h4>
                                <button
                                    type="button"
                                    @click="removeItem(item.key)"
                                    :disabled="loading"
                                    aria-label="Remove item"
                                    class="text-[#8b7f76] hover:text-[#b93826] transition-colors shrink-0 cursor-pointer"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <p x-show="item.variant_name" class="mt-0.5 text-xs text-[#7a6f68]" x-text="'Variant: ' + item.variant_name"></p>
                            <template x-for="c in item.customizations" :key="c.option_id">
                                <p class="mt-0.5 text-[11px] text-[#7a6f68]" x-text="c.option_name + (c.value ? ': ' + c.value : '')"></p>
                            </template>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="text-sm font-bold text-[#c06547]" x-text="formatRupiah(item.unit_price)"></span>
                                <div class="inline-flex items-center rounded-lg border border-[#d3c9c1] bg-white">
                                    <button
                                        type="button"
                                        @click="updateQty(item.key, item.quantity - 1)"
                                        :disabled="loading"
                                        class="h-7 w-7 flex items-center justify-center text-sm text-[#5e5449] hover:bg-[#f5eee6] rounded-l-lg cursor-pointer"
                                    >
                                        –
                                    </button>
                                    <span class="w-8 text-center text-xs font-semibold text-[#2c2420]" x-text="item.quantity"></span>
                                    <button
                                        type="button"
                                        @click="updateQty(item.key, item.quantity + 1)"
                                        :disabled="loading || item.quantity >= item.max_stock"
                                        class="h-7 w-7 flex items-center justify-center text-sm text-[#5e5449] hover:bg-[#f5eee6] rounded-r-lg disabled:opacity-40 cursor-pointer"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Summary & Actions -->
            <div class="border-t border-[#e5dfd8] bg-white px-6 py-5 space-y-4">
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between text-[#5e5449]">
                        <span>Subtotal</span>
                        <span class="font-semibold text-[#2c2420]" x-text="formatRupiah(cart.subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between border-t border-[#e5dfd8] pt-2.5 text-base font-bold">
                        <span class="text-[#2c2420]">Total</span>
                        <span class="text-[#c06547]" x-text="formatRupiah(cart.subtotal)"></span>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <button
                        type="button"
                        @click="open = false"
                        class="w-full rounded-xl border border-[#d3c9c1] bg-white px-5 py-2.5 text-sm font-semibold text-[#2c2420] hover:bg-[#f5eee6] transition-colors cursor-pointer"
                    >
                        Continue Shopping
                    </button>
                    <a
                        href="{{ route('user.checkout') }}"
                        class="flex w-full items-center justify-center rounded-xl bg-[#c06547] px-5 py-3 text-sm font-semibold text-white hover:bg-[#a8543b] transition-colors shadow-xs"
                    >
                        Checkout
                    </a>
                </div>
            </div>
        </div>
    </aside>
</div>
