@extends('dashboard-user.layouts.app')

@section('title', 'Akun Saya - Decak Handmade')

@section('content')
<div x-data="{
    isEditOpen: false,
    activeTab: 'profile',
    user: {
        name: '{{ addslashes($customer->name ?? 'Pelanggan') }}',
        email: '{{ addslashes($customer->email ?? '-') }}',
        phone: '{{ addslashes($customer->phone ?? '-') }}',
        joined: '{{ $customer->created_at ? $customer->created_at->translatedFormat('F Y') : '-' }}',
        address: '{{ addslashes($primaryAddress->full_address ?? ($customer->address ?? 'Belum ada alamat tersimpan')) }}',
        city: '{{ addslashes($primaryAddress->city ?? 'Bandung') }}',
        province: '{{ addslashes($primaryAddress->province ?? 'Jawa Barat') }}',
        postalCode: '{{ addslashes($primaryAddress->postal_code ?? '') }}'
    },
    editForm: {
        name: '{{ addslashes($customer->name ?? '') }}',
        email: '{{ addslashes($customer->email ?? '') }}',
        phone: '{{ addslashes($customer->phone ?? '') }}',
        agreeTerms: true
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Beranda</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Akun saya</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
            Halo, <span>{{ explode(' ', $customer->name ?? 'Pelanggan')[0] }}</span>.
        </h1>
        <p class="text-sm text-[#7a6f68]">
            Ruang kecil untuk semua cerita belanja Anda.
        </p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-[#eaf0e7] border border-[#cfdfca] flex items-center gap-2 text-xs text-[#5a7355]">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Sidebar / Navigation Card -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9 space-y-10">
            
            <!-- Section: Informasi Pribadi -->
            <div>
                <h2 class="text-xl font-bold text-[#2c2420] mb-5">Informasi pribadi</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nama lengkap</label>
                        <div class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] font-medium shadow-2xs">
                            {{ $customer->name ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Email</label>
                        <div class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] font-medium shadow-2xs truncate">
                            {{ $customer->email ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nomor telepon</label>
                        <div class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] font-medium shadow-2xs">
                            {{ $customer->phone ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" @click="isEditOpen = true" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-xs">
                        Edit informasi
                    </button>
                </div>
            </div>

            <!-- Section: Alamat Utama -->
            <div class="pt-2 border-t border-[#e8dfd5]/60">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xl font-bold text-[#2c2420]">Alamat utama</h2>
                    <a href="{{ route('user.addresses') }}" class="text-xs sm:text-sm font-medium text-[#c06547] hover:underline">
                        Kelola semua alamat →
                    </a>
                </div>
                
                @if($primaryAddress)
                <div class="text-sm text-[#4a4039] space-y-1">
                    <div class="font-semibold text-[#2c2420]">
                        <span>{{ $primaryAddress->recipient_name }}</span> · <span>{{ $primaryAddress->phone }}</span>
                    </div>
                    <div class="text-[#5e5449]">{{ $primaryAddress->full_address }}</div>
                    <div class="text-[#5e5449]">
                        {{ $primaryAddress->city ? $primaryAddress->city . ', ' : '' }}
                        {{ $primaryAddress->province ?? '' }} 
                        {{ $primaryAddress->postal_code ?? '' }}
                    </div>
                </div>
                @else
                <p class="text-sm text-[#7a6f68]">Belum ada alamat utama yang ditentukan.</p>
                @endif
            </div>

            <!-- Section: Pesanan Terbaru -->
            <div class="pt-2 border-t border-[#e8dfd5]/60">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-[#2c2420]">Pesanan terbaru</h2>
                    <a href="{{ route('user.orders') }}" class="text-xs sm:text-sm font-medium text-[#5e5449] hover:text-[#c06547] transition-colors flex items-center gap-1 group">
                        Lihat semua pesanan
                        <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                    </a>
                </div>

                @if($latestOrder)
                <!-- Order Card Box -->
                <div class="rounded-2xl border border-[#dec9ba] bg-[#faf7f2] p-5 sm:p-6 transition-all shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-[#ebdcd0]">
                        <div>
                            <div class="font-bold text-[#2c2420] text-base">{{ $latestOrder->order_number }}</div>
                            <div class="text-xs text-[#7a6f68] mt-0.5">
                                {{ $latestOrder->created_at->translatedFormat('d F Y') }} · {{ $latestOrder->items->count() }} produk ({{ $latestOrder->items->sum('quantity') }} barang)
                            </div>
                        </div>
                        <div class="self-start sm:self-auto">
                            @php
                                $badgeClass = match($latestOrder->status) {
                                    'processing' => 'bg-[#faf0eb] text-[#c06547]',
                                    'shipped' => 'bg-[#ede6dc] text-[#5e5449]',
                                    'completed' => 'bg-[#eaf0e7] text-[#5a7355]',
                                    'cancelled' => 'bg-[#fdf3ee] text-[#b93826]',
                                    default => 'bg-[#ede6dc] text-[#5e5449]',
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium {{ $badgeClass }}">
                                {{ $latestOrder->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <!-- Product Thumbnails -->
                            <div class="flex items-center gap-2">
                                @forelse($latestOrder->items->take(2) as $item)
                                    @php
                                        $imgUrl = ($item->product && is_array($item->product->gallery) && count($item->product->gallery) > 0)
                                            ? '/storage/' . $item->product->gallery[0]
                                            : '/images/products/tote-senja.png';
                                    @endphp
                                    <img src="{{ $imgUrl }}" alt="{{ $item->product_name }}" class="w-14 h-14 object-cover rounded-xl border border-[#e0ddd8] shadow-2xs">
                                @empty
                                    <div class="w-14 h-14 rounded-xl bg-[#ede6dc] flex items-center justify-center text-[#7a6f68] text-xs">Produk</div>
                                @endforelse
                            </div>
                            <div>
                                <div class="text-base font-bold text-[#2c2420]">Rp{{ number_format($latestOrder->grand_total, 0, ',', '.') }}</div>
                                <div class="text-xs text-[#7a6f68]">
                                    @if($latestOrder->courier)
                                        {{ $latestOrder->courier }} (Resi: {{ $latestOrder->tracking_number ?? 'Menunggu kurir' }})
                                    @else
                                        Pesanan Anda sedang disiapkan
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('user.orders') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-[#2c2420] text-[#2c2420] text-sm font-semibold hover:bg-[#f0e8dc] transition-colors">
                                Lacak pesanan
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="rounded-2xl border border-[#dec9ba] bg-[#faf7f2] p-8 text-center text-sm text-[#7a6f68]">
                    Belum ada riwayat pesanan. Mulai belanja untuk melengkapi koleksi kerajinan Anda!
                </div>
                @endif
            </div>

        </section>
    </div>

    <!-- Edit Profile Slide-Over Drawer Modal -->
    <div x-cloak x-show="isEditOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="isEditOpen" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="isEditOpen = false" 
            class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-10">
            <div x-show="isEditOpen" 
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between p-6 sm:p-8">
                
                <div class="space-y-6">
                    <!-- Back Button -->
                    <button type="button" @click="isEditOpen = false" class="inline-flex items-center gap-2 text-sm font-medium text-[#5e5449] hover:text-[#2c2420] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke profil</span>
                    </button>

                    <!-- Title & Subtitle -->
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-[#7a6f68] mb-1">JADI BAGIAN DARI CERITA KAMI</p>
                        <h2 class="text-2xl sm:text-3xl font-bold text-[#2c2420]">Edit informasi</h2>
                        <p class="text-sm text-[#7a6f68] mt-1">Ganti informasi untuk kebenaran data akun Anda</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nama lengkap</label>
                            <input type="text" name="name" x-model="editForm.name" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Email</label>
                            <input type="email" name="email" x-model="editForm.email" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#5e5449] mb-1.5">Nomor telepon</label>
                            <div class="relative">
                                <input type="text" name="phone" x-model="editForm.phone" required
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-[#e0ddd8] bg-white text-sm text-[#2c2420] focus:border-[#c06547] focus:ring-2 focus:ring-[#f0c9b9] outline-none">
                            </div>
                        </div>

                        <div class="pt-2 flex items-center gap-2">
                            <input type="checkbox" id="agreeTerms" required class="rounded border-[#dec9ba] text-[#c06547] focus:ring-[#c06547]/30" checked>
                            <label for="agreeTerms" class="text-xs text-[#5e5449] select-none cursor-pointer">
                                Saya menyetujui Syarat & Kebijakan Privasi.
                            </label>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#c06547] text-white font-semibold text-sm hover:bg-[#a8543b] transition-colors shadow-xs">
                                Simpan perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-xs text-[#7a6f68] mt-8 text-center sm:text-left">
                    © 2026 decak handmade · Kebijakan privasi
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
