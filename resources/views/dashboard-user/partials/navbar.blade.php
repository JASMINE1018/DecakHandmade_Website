<header class="w-full bg-[#faf7f2]/90 backdrop-blur border-b border-[#e5dfd8]/60 sticky top-0 z-30 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Logo Decak Handmade -->
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-[#c06547] flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="4" />
                </svg>
            </div>
            <span class="font-bold text-xl tracking-tight text-[#2c2420]">Decak Handmade</span>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-[#4a4039]">
            <a href="{{ route('user.dashboard') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('user.beranda') ? 'text-[#c06547] font-semibold' : '' }}">
                Beranda
            </a>
            <a href="{{ route('user.dashboard') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('user.katalog') ? 'text-[#c06547] font-semibold' : '' }}">
                Katalog
            </a>
            <a href="{{ route('user.dashboard') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('user.cerita') ? 'text-[#c06547] font-semibold' : '' }}">
                Cerita Kami
            </a>
            <a href="{{ route('user.dashboard') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('user.dashboard') ? 'text-[#c06547] font-semibold' : '' }}">
                Akun saya
            </a>
        </nav>

        <!-- Right Action Button -->
        <div class="flex items-center gap-4">
            <a href="{{ route('user.cart') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-sm hover:shadow">
                Belanja Sekarang
            </a>
        </div>
    </div>
</header>
