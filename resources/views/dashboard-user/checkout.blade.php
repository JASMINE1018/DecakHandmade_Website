@extends('dashboard-user.layouts.app')

@section('title', 'Checkout Simulasi - Decak Handmade')

@section('content')
<div x-data="{
    paymentMethod: 'bank',
    isPrimaryAddress: true,
    isSubmitting: false,
    orderSuccess: false,
    shippingFee: 18000,
    items: [
        {
            title: 'Tote Senja',
            desc: 'Kanvas katun · Natural · 1 × Rp189.000',
            price: 189000,
            image: '/images/products/tote-senja.png'
        },
        {
            title: 'Pouch Rami',
            desc: 'Linen katun · Oat · 2 × Rp79.000',
            price: 158000,
            image: '/images/products/pouch-rami.png'
        }
    ],
    get subtotal() {
        return this.items.reduce((sum, item) => sum + item.price, 0);
    },
    get total() {
        return this.subtotal + this.shippingFee;
    },
    formatRupiah(num) {
        return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
    submitOrder() {
        this.isSubmitting = true;
        setTimeout(() => {
            this.isSubmitting = false;
            this.orderSuccess = true;
        }, 600);
    }
}">
    <!-- Top Step Bar & Title -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
            Checkout
        </h1>

        <!-- Step Indicator -->
        <div class="flex items-center gap-3 text-xs font-semibold">
            <div class="flex items-center gap-2 text-[#7a6f68]">
                <span class="w-6 h-6 rounded-full bg-[#c06547] text-white flex items-center justify-center text-xs">✓</span>
                <span class="text-[#c06547]">Keranjang</span>
            </div>
            <span class="w-8 h-px bg-[#c06547]"></span>
            
            <div class="flex items-center gap-2 text-[#2c2420]">
                <span class="w-6 h-6 rounded-full bg-[#c06547] text-white flex items-center justify-center text-xs">2</span>
                <span class="text-[#c06547] font-bold">Checkout</span>
            </div>
            <span class="w-8 h-px bg-[#d3c9c1]"></span>

            <div class="flex items-center gap-2 text-[#a99e96]">
                <span class="w-6 h-6 rounded-full bg-[#e0ddd8] text-[#7a6f68] flex items-center justify-center text-xs">3</span>
                <span>Selesai</span>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Form Column -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">
            
            <!-- Alamat Penerima Section -->
            <div>
                <h2 class="text-xl font-bold text-[#2c2420] mb-4">Alamat penerima</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nama penerima</label>
                            <input type="text" value="Ayu Pramesti" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nomor telepon</label>
                            <input type="text" value="0812 3456 7890" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Alamat lengkap</label>
                        <input type="text" value="Jl. Melati No. 18, RT 02/RW 04, Sukajadi" 
                            class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Provinsi</label>
                            <input type="text" value="Jawa Barat" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Kota / kabupaten</label>
                            <input type="text" value="Kota Bandung" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Kode pos</label>
                            <input type="text" value="40162" 
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <input type="checkbox" id="primaryAddress" x-model="isPrimaryAddress" class="rounded border-[#dec9ba] text-[#c06547] focus:ring-[#c06547]/30">
                        <label for="primaryAddress" class="text-xs text-[#5e5449] select-none cursor-pointer">
                            Simpan sebagai alamat utama
                        </label>
                    </div>
                </form>
            </div>

            <div class="h-px bg-[#e5dfd8]"></div>

            <!-- Pembayaran Simulasi Section -->
            <div>
                <h2 class="text-xl font-bold text-[#2c2420] mb-4">Pembayaran simulasi</h2>
                
                <div class="space-y-3">
                    <!-- Bank Option -->
                    <label @click="paymentMethod = 'bank'" class="flex items-center gap-3 p-4 rounded-xl border border-[#e0ddd8] bg-white cursor-pointer hover:border-[#c06547]/50 transition-colors">
                        <input type="radio" name="payment" value="bank" x-model="paymentMethod" class="text-[#c06547] focus:ring-[#c06547]/30">
                        <span class="text-sm font-semibold text-[#2c2420]">Transfer bank</span>
                    </label>

                    <!-- E-Wallet Option -->
                    <label @click="paymentMethod = 'ewallet'" class="flex items-center gap-3 p-4 rounded-xl border border-[#e0ddd8] bg-white cursor-pointer hover:border-[#c06547]/50 transition-colors">
                        <input type="radio" name="payment" value="ewallet" x-model="paymentMethod" class="text-[#c06547] focus:ring-[#c06547]/30">
                        <span class="text-sm font-semibold text-[#2c2420]">Dompet digital</span>
                    </label>
                </div>

                <p class="text-xs text-[#7a6f68] mt-4">
                    Dengan membuat pesanan simulasi, Anda menyetujui Syarat & Kebijakan Privasi decak.
                </p>
            </div>

        </div>

        <!-- Right Summary Column -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="rounded-2xl bg-[#f5eee6]/80 border border-[#e8dfd5] p-6 shadow-xs space-y-6">
                <h3 class="text-xl font-bold text-[#2c2420]">Ringkasan pesanan</h3>

                <!-- Product List in Checkout -->
                <div class="space-y-4">
                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#e5dfd8]/60 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <img :src="item.image" :alt="item.title" class="w-14 h-14 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs">
                                <div>
                                    <h4 class="text-sm font-bold text-[#2c2420]" x-text="item.title"></h4>
                                    <p class="text-xs text-[#7a6f68]" x-text="item.desc"></p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-[#2c2420] shrink-0" x-text="formatRupiah(item.price)"></span>
                        </div>
                    </template>
                </div>

                <div class="h-px bg-[#e5dfd8]"></div>

                <!-- Price Calculations -->
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[#7a6f68]">Subtotal (3 barang)</span>
                        <span class="font-medium text-[#2c2420]" x-text="formatRupiah(subtotal)">Rp347.000</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#7a6f68]">Pengiriman JNE REG</span>
                        <span class="font-medium text-[#2c2420]" x-text="formatRupiah(shippingFee)">Rp18.000</span>
                    </div>
                    <div class="h-px bg-[#e5dfd8] my-2"></div>
                    <div class="flex items-center justify-between text-base font-bold text-[#2c2420]">
                        <span>Total</span>
                        <span x-text="formatRupiah(total)">Rp365.000</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="button" @click="submitOrder" :disabled="isSubmitting"
                        class="w-full inline-flex items-center justify-center py-3.5 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs disabled:opacity-50">
                        <span x-show="!isSubmitting">Buat pesanan</span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Simulation Success Modal -->
    <div x-cloak x-show="orderSuccess" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="orderSuccess"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Dialog Container -->
        <div class="relative min-h-screen flex items-center justify-center p-4 text-center">
            <div x-show="orderSuccess"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl space-y-4 z-10">
                <div class="w-16 h-16 mx-auto rounded-full bg-[#fdf3ee] text-[#c06547] flex items-center justify-center text-2xl font-bold">
                    ✓
                </div>
                <h3 class="text-2xl font-bold text-[#2c2420]">Pesanan Berhasil Disimulasikan!</h3>
                <p class="text-sm text-[#7a6f68] leading-relaxed">
                    Nomor Pesanan: <strong class="text-[#2c2420]">DCK-20261020-018</strong>. Karena ini adalah antarmuka prototipe (frontend-only), pesanan ini telah dicatat dalam simulasi.
                </p>
                <div class="pt-2 flex flex-col gap-2">
                    <a href="{{ route('user.dashboard') }}" class="w-full py-2.5 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs">
                        Kembali ke Akun Saya
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
