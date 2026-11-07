@extends('dashboard-user.layouts.app')

@section('title', 'Pesanan Saya - Decak Handmade')

@section('content')
<div x-data="{
    activeFilter: '{{ $status ?? 'all' }}',
    trackingModal: false,
    currentTracking: null,
    orders: @json($orders),
    formatRupiah(num) {
        return 'Rp' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
    openTracking(order) {
        this.currentTracking = order;
        this.trackingModal = true;
    },
    get filteredOrders() {
        if (this.activeFilter === 'all') return this.orders;
        if (this.activeFilter === 'shipping') {
            return this.orders.filter(o => o.status === 'shipped');
        }
        return this.orders.filter(o => o.status === this.activeFilter);
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Beranda</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Akun saya</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Pesanan saya</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                Pesanan saya
            </h1>
            <p class="text-sm text-[#7a6f68] mt-1">
                Pantau proses pembuatan, pengemasan, hingga riwayat pengiriman produk kerajinan Anda.
            </p>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Navigation -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9 space-y-6">
            
            <!-- Horizontal Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-[#e5dfd8]">
                <button type="button" @click="activeFilter = 'all'"
                    :class="activeFilter === 'all' ? 'border-[#c06547] text-[#c06547] font-semibold bg-[#faf7f2]' : 'border-transparent text-[#7a6f68] hover:text-[#2c2420]'"
                    class="px-4 py-2 border-b-2 text-sm whitespace-nowrap transition-colors">
                    Semua
                </button>
                <button type="button" @click="activeFilter = 'shipping'"
                    :class="activeFilter === 'shipping' ? 'border-[#c06547] text-[#c06547] font-semibold bg-[#faf7f2]' : 'border-transparent text-[#7a6f68] hover:text-[#2c2420]'"
                    class="px-4 py-2 border-b-2 text-sm whitespace-nowrap transition-colors flex items-center gap-1.5">
                    <span>Dalam pengiriman</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-[#f0c9b9] text-[#703628] text-xs font-bold"
                        x-text="orders.filter(o => o.status === 'shipped').length"></span>
                </button>
                <button type="button" @click="activeFilter = 'completed'"
                    :class="activeFilter === 'completed' ? 'border-[#c06547] text-[#c06547] font-semibold bg-[#faf7f2]' : 'border-transparent text-[#7a6f68] hover:text-[#2c2420]'"
                    class="px-4 py-2 border-b-2 text-sm whitespace-nowrap transition-colors">
                    Selesai
                </button>
                <button type="button" @click="activeFilter = 'cancelled'"
                    :class="activeFilter === 'cancelled' ? 'border-[#c06547] text-[#c06547] font-semibold bg-[#faf7f2]' : 'border-transparent text-[#7a6f68] hover:text-[#2c2420]'"
                    class="px-4 py-2 border-b-2 text-sm whitespace-nowrap transition-colors">
                    Dibatalkan
                </button>
            </div>

            <!-- Orders List -->
            <div class="space-y-5">
                <template x-for="order in filteredOrders" :key="order.id">
                    <div class="rounded-2xl border border-[#dec9ba] bg-[#faf7f2] p-5 sm:p-6 transition-all shadow-xs">
                        <!-- Top Order Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-[#ebdcd0]">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[#2c2420] text-base" x-text="order.id"></span>
                                    <span class="text-xs text-[#7a6f68]">·</span>
                                    <span class="text-xs text-[#7a6f68]" x-text="order.date"></span>
                                </div>
                                <div class="text-xs text-[#7a6f68] mt-0.5">
                                    Ekspedisi: <span x-text="order.courier"></span> (<span x-text="order.trackingNumber"></span>)
                                </div>
                            </div>
                            <div class="self-start sm:self-auto">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium"
                                    :class="order.statusClass" x-text="order.statusLabel">
                                </span>
                            </div>
                        </div>

                        <!-- Product Rows Inside Order -->
                        <div class="py-4 divide-y divide-[#eee5dc]">
                            <template x-for="(item, idx) in order.items" :key="idx">
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <img :src="item.image" :alt="item.title" class="w-14 h-14 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs">
                                        <div>
                                            <h4 class="text-sm font-bold text-[#2c2420]" x-text="item.title"></h4>
                                            <p class="text-xs text-[#7a6f68]" x-text="item.variant"></p>
                                            <p class="text-xs text-[#5e5449] font-medium mt-0.5">
                                                <span x-text="item.qty"></span> × <span x-text="formatRupiah(item.price)"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-[#2c2420]" x-text="formatRupiah(item.price * item.qty)"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Order Total & Action Buttons -->
                        <div class="pt-4 border-t border-[#ebdcd0] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="text-xs text-[#7a6f68]">Total belanja</div>
                                <div class="text-lg font-bold text-[#2c2420]" x-text="formatRupiah(order.total)"></div>
                            </div>

                            <div class="flex items-center gap-3">
                                <template x-if="order.status === 'shipped'">
                                    <button type="button" @click="openTracking(order)"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#2c2420] text-[#2c2420] text-sm font-semibold hover:bg-[#f0e8dc] transition-colors">
                                        Lacak pesanan
                                    </button>
                                </template>
                                <template x-if="order.status === 'completed'">
                                    <a href="{{ route('user.cart') }}"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-colors shadow-xs">
                                        Beli lagi
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filteredOrders.length === 0" class="rounded-2xl border border-dashed border-[#d3c9c1] p-12 text-center bg-white">
                    <p class="text-sm text-[#7a6f68]">Tidak ada pesanan di kategori ini.</p>
                </div>
            </div>

        </section>
    </div>

    <!-- Modal Lacak Pengiriman -->
    <div x-cloak x-show="trackingModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="trackingModal" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="trackingModal = false"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Dialog Container -->
        <div class="relative min-h-screen flex items-center justify-center p-4 text-center sm:p-0">
            <div x-show="trackingModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full p-6 sm:p-8 z-10">
                
                <div class="flex items-center justify-between pb-4 border-b border-[#e5dfd8]">
                    <div>
                        <h3 class="text-lg font-bold text-[#2c2420]">Lacak pengiriman</h3>
                        <p class="text-xs text-[#7a6f68]" x-text="currentTracking ? currentTracking.id : ''"></p>
                    </div>
                    <button type="button" @click="trackingModal = false" class="text-[#7a6f68] hover:text-[#2c2420] p-1 rounded-lg hover:bg-[#faf7f2] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 p-3.5 rounded-xl bg-[#faf7f2] border border-[#ebdcd0] text-xs text-[#5e5449] space-y-1">
                    <div>Kurir: <strong class="text-[#2c2420]" x-text="currentTracking?.courier"></strong></div>
                    <div>Nomor Resi: <strong class="text-[#2c2420]" x-text="currentTracking?.trackingNumber"></strong></div>
                    <div>Status Pesanan: <strong class="text-[#c06547]" x-text="currentTracking?.statusLabel"></strong></div>
                </div>

                <!-- Timeline -->
                <div class="mt-6 space-y-5">
                    <template x-for="(step, idx) in currentTracking?.history || []" :key="idx">
                        <div class="flex items-start gap-3 relative">
                            <!-- Bullet -->
                            <div class="w-3 h-3 rounded-full mt-1 shrink-0"
                                :class="idx === 0 ? 'bg-[#c06547] ring-4 ring-[#f0c9b9]' : 'bg-[#d3c9c1]'"></div>
                            <div>
                                <p class="text-xs font-bold text-[#2c2420]" x-text="step.desc"></p>
                                <p class="text-[11px] text-[#7a6f68] mt-0.5" x-text="step.time"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-8 pt-4 border-t border-[#e5dfd8]">
                    <button type="button" @click="trackingModal = false" class="w-full py-2.5 px-4 rounded-xl bg-[#2c2420] text-white text-xs font-semibold hover:bg-black transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
