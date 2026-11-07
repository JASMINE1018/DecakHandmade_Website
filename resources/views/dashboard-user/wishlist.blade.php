@extends('dashboard-user.layouts.app')

@section('title', 'Wishlist Saya - Decak Handmade')

@section('content')
<div x-data="wishlistPage()">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-[#7a6f68] mb-3">
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Beranda</a>
        <span>/</span>
        <a href="{{ route('user.dashboard') }}" class="hover:text-[#2c2420]">Akun saya</a>
        <span>/</span>
        <span class="text-[#2c2420] font-medium">Wishlist</span>
    </nav>

    <!-- Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-[#2c2420]">
                Wishlist saya
            </h1>
            <p class="text-sm text-[#7a6f68] mt-1">
                Koleksi kerajinan tangan yang Anda simpan untuk momen istimewa.
            </p>
        </div>
        <div class="text-xs font-semibold text-[#5e5449] bg-[#f5eee6] px-3 py-1.5 rounded-lg border border-[#e5dfd8]">
            <span x-text="wishlist.length">0</span> kerajinan tersimpan
        </div>
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
        <!-- Sidebar Navigation -->
        @include('dashboard-user.partials.sidebar')

        <!-- Right Content Area -->
        <section class="lg:col-span-8 xl:col-span-9">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <template x-for="item in wishlist" :key="item.id">
                    <div class="rounded-2xl border border-[#e0ddd8] bg-white overflow-hidden shadow-2xs hover:shadow-sm transition-all flex flex-col justify-between group">
                        
                        <!-- Top Image Area with Delete button -->
                        <div class="relative aspect-square overflow-hidden bg-[#faf7f2] border-b border-[#e5dfd8]">
                            <img :src="item.image" :alt="item.title" class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300">
                            
                            <!-- Remove Heart Button -->
                            <button type="button" @click="removeFromWishlist(item.id)" 
                                title="Hapus dari wishlist"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs border border-[#e0ddd8] flex items-center justify-center text-[#c06547] hover:bg-[#c06547] hover:text-white transition-colors shadow-2xs">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>

                            <!-- Stock Status Badge -->
                            <div class="absolute bottom-3 left-3">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold backdrop-blur-xs border border-black/5"
                                    :class="item.stockBadge" x-text="item.stockStatus">
                                </span>
                            </div>
                        </div>

                        <!-- Card Content Bottom -->
                        <div class="p-5 flex-1 flex flex-col justify-between gap-4">
                            <div class="space-y-1">
                                <h3 class="font-bold text-base text-[#2c2420] group-hover:text-[#c06547] transition-colors" x-text="item.title"></h3>
                                <p class="text-xs text-[#7a6f68] line-clamp-2" x-text="item.material"></p>
                                <div class="text-base font-bold text-[#2c2420] pt-1" x-text="formatRupiah(item.price)"></div>
                            </div>

                            <div class="pt-2">
                                <a href="{{ route('user.cart') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-3.5 rounded-xl bg-[#c06547] text-white font-semibold text-xs hover:bg-[#a8543b] transition-colors shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    <span>Masukkan keranjang</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="wishlist.length === 0" class="rounded-2xl border border-dashed border-[#d3c9c1] p-12 text-center bg-white">
                <svg class="w-12 h-12 mx-auto text-[#a99e96] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="text-base font-bold text-[#2c2420]">Wishlist Anda masih kosong</h3>
                <p class="text-xs text-[#7a6f68] mt-1 mb-4">Temukan karya kerajinan tangan lokal favorit Anda di katalog kami.</p>
                <a href="{{ route('user.dashboard') }}" class="inline-flex px-4 py-2 rounded-lg bg-[#c06547] text-white text-xs font-semibold hover:bg-[#a8543b]">
                    Eksplor katalog
                </a>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function wishlistPage() {
    return {
        wishlist: @json($wishlistItems),
        formatRupiah(num) {
            return 'Rp' + Number(num || 0).toLocaleString('id-ID');
        },
        async removeFromWishlist(id) {
            try {
                const res = await fetch(`/user/wishlist/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    this.wishlist = this.wishlist.filter(item => item.id !== id);
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endpush
