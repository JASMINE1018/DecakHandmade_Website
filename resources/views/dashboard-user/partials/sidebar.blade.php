<aside class="lg:col-span-4 xl:col-span-3">
    <div class="bg-[#f5eee6]/80 border border-[#e8dfd5] rounded-2xl p-5 shadow-xs sticky top-28">
        <!-- User Profile Intro -->
        <div class="flex items-center gap-3.5 mb-6">
            <div class="w-12 h-12 rounded-full bg-white border border-[#e0ddd8] flex items-center justify-center font-bold text-sm text-[#5a483e] shadow-2xs">
                <span>AP</span>
            </div>
            <div>
                <h2 class="font-semibold text-base text-[#2c2420] leading-tight">Ayu Pramesti</h2>
                <p class="text-xs text-[#7a6f68] mt-0.5">Anggota sejak Oktober 2026</p>
            </div>
        </div>

        <div class="h-px bg-[#e5dfd8] my-4"></div>

        <!-- Navigation Menu Links -->
        <nav class="space-y-1 text-sm font-medium">
            <a href="{{ route('user.dashboard') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-[#ebe3d8] text-[#2c2420] font-semibold' : 'text-[#5e5449] hover:bg-[#eee7dc] hover:text-[#2c2420]' }}">
                <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Profil saya</span>
            </a>

            <a href="{{ route('user.addresses') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.addresses') ? 'bg-[#ebe3d8] text-[#2c2420] font-semibold' : 'text-[#5e5449] hover:bg-[#eee7dc] hover:text-[#2c2420]' }}">
                <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Alamat tersimpan</span>
            </a>

            <a href="{{ route('user.orders') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.orders') ? 'bg-[#ebe3d8] text-[#2c2420] font-semibold' : 'text-[#5e5449] hover:bg-[#eee7dc] hover:text-[#2c2420]' }}">
                <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Pesanan saya</span>
            </a>

            <a href="{{ route('user.wishlist') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.wishlist') ? 'bg-[#ebe3d8] text-[#2c2420] font-semibold' : 'text-[#5e5449] hover:bg-[#eee7dc] hover:text-[#2c2420]' }}">
                <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span>Wishlist (4)</span>
            </a>

            <a href="{{ route('user.password') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('user.password') ? 'bg-[#ebe3d8] text-[#2c2420] font-semibold' : 'text-[#5e5449] hover:bg-[#eee7dc] hover:text-[#2c2420]' }}">
                <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Ubah kata sandi</span>
            </a>
        </nav>

        <div class="h-px bg-[#e5dfd8] my-4"></div>

        <!-- Logout Link -->
        <a href="#" class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium text-[#c06547] hover:text-[#a8543b] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Keluar</span>
        </a>
    </div>
</aside>
