<header class="w-full bg-[#faf7f2]/90 backdrop-blur border-b border-[#e5dfd8]/60 sticky top-0 z-30 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Logo Decak Handmade -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-[#c06547] flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="4" />
                </svg>
            </div>
            <span class="font-bold text-xl tracking-tight text-[#2c2420]">Decak Handmade</span>
        </a>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-[#4a4039]">
            <a href="{{ route('home') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('home') ? 'text-[#c06547] font-semibold' : '' }}">
                Home
            </a>
            <a href="{{ route('catalog.index') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('catalog.*') ? 'text-[#c06547] font-semibold' : '' }}">
                Catalog
            </a>
            <a href="{{ route('home') }}#our-story" class="transition-colors hover:text-[#c06547]">
                Our Story
            </a>
            <a href="{{ route('user.dashboard') }}" class="transition-colors hover:text-[#c06547] {{ request()->routeIs('user.*') ? 'text-[#c06547] font-semibold' : '' }}">
                My Account
            </a>
        </nav>

        <!-- Right Action Button -->
        <div class="flex items-center gap-3">
            <button type="button" @click="$dispatch('open-cart-drawer')" aria-label="Keranjang Belanja" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#d3c9c1] bg-white text-[#2c2420] hover:bg-[#f5eee6] hover:text-[#c06547] transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span data-cart-badge class="hidden absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#c06547] px-1 text-[10px] font-bold text-white">0</span>
            </button>
            @guest('customer')
            <a href="{{ route('user.login') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg border border-[#c8bcaf] text-[#4a3f35] text-sm font-semibold hover:bg-[#f0ebe3] transition-all">
                Sign In
            </a>
            @endguest
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-[#c06547] text-white text-sm font-semibold hover:bg-[#a8543b] transition-all shadow-sm hover:shadow">
                Shop Now
            </a>
        </div>
    </div>
</header>
