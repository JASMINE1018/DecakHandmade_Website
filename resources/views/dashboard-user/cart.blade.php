@extends('dashboard-user.layouts.app')

@section('title', 'Keranjang Belanja - Decak Handmade')

@section('content')
<div x-data="{
    items: [
        {
            id: 1,
            title: 'Tote Senja',
            material: 'Kanvas katun · Natural',
            price: 189000,
            qty: 1,
            image: '/images/products/tote-senja.png'
        },
        {
            id: 2,
            title: 'Pouch Rami',
            material: 'Linen katun · Oat',
            price: 79000,
            qty: 2,
            image: '/images/products/pouch-rami.png'
        }
    ],
    shipping: 18000,
    get totalItemsCount() {
        return this.items.reduce((sum, item) => sum + item.qty, 0);
    },
    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },
    get total() {
        return this.subtotal + (this.items.length > 0 ? this.shipping : 0);
    },
    formatRupiah(num) {
        return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
    increaseQty(id) {
        const item = this.items.find(i => i.id === id);
        if (item) item.qty++;
    },
    decreaseQty(id) {
        const item = this.items.find(i => i.id === id);
        if (item && item.qty > 1) {
            item.qty--;
        }
    },
    removeItem(id) {
        this.items = this.items.filter(i => i.id !== id);
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Beranda</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Keranjang belanja</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
            Keranjang belanja
        </h1>
        <p class="text-sm text-[#7a6f68]">
            <span x-text="items.length">2</span> produk · <span x-text="totalItemsCount">3</span> barang
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Items Column (Left) -->
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-[#7a6f68] pb-3 border-b border-[#e5dfd8]">
                <span>PRODUK</span>
                <span>JUMLAH & HARGA</span>
            </div>

            <!-- Items List -->
            <div class="divide-y divide-[#e5dfd8]/70">
                <template x-for="item in items" :key="item.id">
                    <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <!-- Product Info Left -->
                        <div class="flex items-center gap-4 sm:gap-6">
                            <img :src="item.image" :alt="item.title" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs">
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-[#2c2420]" x-text="item.title"></h3>
                                <p class="text-xs sm:text-sm text-[#7a6f68]" x-text="item.material"></p>
                                <p class="text-xs sm:text-sm text-[#7a6f68]">
                                    <span x-text="formatRupiah(item.price)"></span> / barang
                                </p>
                                <button type="button" @click="removeItem(item.id)" class="text-xs text-[#7a6f68] hover:text-[#c06547] flex items-center gap-1.5 pt-1 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>

                        <!-- Quantity and Price Right -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-3">
                            <!-- Qty Control -->
                            <div class="inline-flex items-center rounded-lg border border-[#d3c9c1] bg-white">
                                <button type="button" @click="decreaseQty(item.id)" class="w-9 h-9 flex items-center justify-center text-[#5e5449] hover:bg-[#f5eee6] rounded-l-lg transition-colors">
                                    –
                                </button>
                                <span class="w-10 text-center text-sm font-semibold text-[#2c2420]" x-text="item.qty"></span>
                                <button type="button" @click="increaseQty(item.id)" class="w-9 h-9 flex items-center justify-center text-[#5e5449] hover:bg-[#f5eee6] rounded-r-lg transition-colors">
                                    +
                                </button>
                            </div>

                            <!-- Line Total -->
                            <div class="text-base font-bold text-[#2c2420]" x-text="formatRupiah(item.price * item.qty)">
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="items.length === 0" class="py-12 text-center text-[#7a6f68]">
                    Keranjang belanja Anda kosong.
                </div>
            </div>

            <div class="pt-6">
                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2c2420] hover:text-[#c06547] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Lanjut belanja</span>
                </a>
            </div>

            <!-- Guarantee Banner -->
            <div class="mt-8 rounded-2xl bg-[#f5eee6]/90 border border-[#e5dfd8] p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#e0ddd8] flex items-center justify-center shrink-0 text-[#2c2420] shadow-2xs">
                    <svg class="w-5 h-5 text-[#5e5449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-[#2c2420]">Dikemas rapi, dikirim dengan hati.</h4>
                    <p class="text-xs text-[#7a6f68] mt-0.5">Pesanan disiapkan dalam 1–2 hari kerja dari atelier Bandung.</p>
                </div>
            </div>
        </div>

        <!-- Summary Column (Right) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="rounded-2xl bg-[#f5eee6]/80 border border-[#e8dfd5] p-6 shadow-xs space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-[#2c2420]">Ringkasan belanja</h3>
                    <p class="text-xs text-[#7a6f68] mt-1">
                        <span x-text="items.length">2</span> produk, <span x-text="totalItemsCount">3</span> barang siap dibawa pulang.
                    </p>
                </div>

                <!-- Breakdown White Box -->
                <div class="bg-white rounded-xl border border-[#e5dfd8] p-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#7a6f68]">Subtotal</span>
                        <span class="font-medium text-[#2c2420]" x-text="formatRupiah(subtotal)">Rp347.000</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#7a6f68]">Ongkir estimasi</span>
                        <span class="font-medium text-[#2c2420]" x-text="items.length > 0 ? formatRupiah(shipping) : 'Rp0'">Rp18.000</span>
                    </div>
                    <div class="h-px bg-[#e5dfd8]"></div>
                    <div class="flex items-center justify-between text-base font-bold text-[#2c2420] pt-1">
                        <span>Total estimasi</span>
                        <span x-text="formatRupiah(total)">Rp365.000</span>
                    </div>
                </div>

                <p class="text-xs text-[#7a6f68] leading-relaxed">
                    Estimasi JNE REG ke Kota Bandung. Biaya pengiriman akhir dikonfirmasi saat checkout.
                </p>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('user.checkout') }}" class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs">
                        Lanjut ke checkout
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl border border-[#c06547] text-[#c06547] font-semibold text-sm hover:bg-[#fbf5ef] transition-colors">
                        Lanjut belanja
                    </a>
                </div>

                <div class="h-px bg-[#e5dfd8]"></div>

                <!-- Simulation Security Note -->
                <div class="flex items-start gap-2.5 text-xs text-[#7a6f68]">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-[#5e5449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Checkout berikutnya hanya simulasi. Tidak ada biaya yang ditagihkan.</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
