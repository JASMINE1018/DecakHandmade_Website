<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-[#E5E0D8] bg-[#F9F6F1]/95 backdrop-blur-md">
    <div class="mx-auto grid h-20 max-w-7xl grid-cols-[1fr_auto] items-center gap-4 px-4 sm:px-6 md:grid-cols-[1fr_auto_1fr] md:px-10 lg:px-16">
        {{-- ── Left: Logo ── --}}
        <a href="{{ route('home') }}" aria-label="Decak Handmade — Home" class="flex w-fit flex-col items-start leading-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B]">
            <span class="text-[1.75rem] leading-[0.85] tracking-tight text-[#B86B4B]" style="font-family: 'Playfair Display', Georgia, serif;">
                decak
            </span>
            <span class="mt-1.5 text-[9px] font-medium uppercase tracking-[0.25em] text-[#B86B4B]">
                HANDMADE
            </span>
        </a>

        {{-- ── Centre: Nav links (desktop) ── --}}
        <nav aria-label="Main navigation" class="hidden md:block md:justify-self-center">
            <ul class="flex items-center gap-7 lg:gap-9">
                <li>
                    <a href="{{ route('home') }}"
                       class="inline-block border-b pb-1 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] {{ request()->routeIs('home') ? 'border-[#B86B4B] text-[#B86B4B]' : 'border-transparent text-[#2B2520] hover:text-[#B86B4B]' }}">
                        HOME
                    </a>
                </li>
                <li>
                    <a href="{{ route('catalog.index') }}"
                       class="inline-block border-b pb-1 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] {{ request()->routeIs('catalog.*') ? 'border-[#B86B4B] text-[#B86B4B]' : 'border-transparent text-[#2B2520] hover:text-[#B86B4B]' }}">
                        CATALOG
                    </a>
                </li>
                <li>
                    <a href="{{ route('story.index') }}"
                       class="inline-block border-b pb-1 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] {{ request()->routeIs('story.*') ? 'border-[#B86B4B] text-[#B86B4B]' : 'border-transparent text-[#2B2520] hover:text-[#B86B4B]' }}">
                        OUR STORY
                    </a>
                </li>
            </ul>
        </nav>

        {{-- ── Right: Action icons ── --}}
        <div class="flex items-center justify-self-end gap-3 sm:gap-4 md:gap-5">
            {{-- Search pill form --}}
            <div class="hidden w-28 md:block lg:w-36 xl:w-44">
                <form action="{{ route('catalog.index') }}" method="GET" role="search" class="w-full">
                    <label for="user-nav-search" class="sr-only">Search products</label>
                    <div class="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-3.5 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                        <input
                            id="user-nav-search"
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="min-w-0 flex-1 appearance-none border-0 bg-transparent p-0 font-[inherit] text-xs text-[#5C554F] shadow-none placeholder:text-[#8C837A] focus:border-0 focus:outline-none focus:ring-0"
                        />
                        <button type="submit" aria-label="Search" class="ml-2 inline-flex text-[#8C837A] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#B86B4B]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- User account link --}}
            <a href="{{ route('user.dashboard') }}" aria-label="My account" class="inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] p-1 {{ request()->routeIs('user.*') ? 'text-[#B86B4B]' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-[18px] w-[18px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </a>

            {{-- Shopping cart drawer trigger --}}
            <button type="button" @click="$dispatch('open-cart-drawer')" aria-label="Shopping cart" class="relative inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] p-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-[18px] w-[18px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <span data-cart-badge class="hidden absolute -right-1.5 -top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#B86B4B] px-1 text-[9px] font-bold text-white">0</span>
            </button>

            {{-- Mobile menu button --}}
            <button type="button" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Toggle menu" class="inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] md:hidden p-1">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Mobile menu panel ── --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="border-t border-[#E5E0D8] bg-[#F9F6F1] px-6 py-6 md:hidden">
        <nav aria-label="Mobile navigation">
            <ul class="flex flex-col">
                <li>
                    <a href="{{ route('home') }}" @click="mobileOpen = false"
                       class="flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('home') ? 'text-[#B86B4B]' : 'text-[#2B2520]' }}">
                        <span>HOME</span>
                        @if(request()->routeIs('home'))
                            <span aria-hidden="true" class="h-px w-6 bg-[#B86B4B]"></span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('catalog.index') }}" @click="mobileOpen = false"
                       class="flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('catalog.*') ? 'text-[#B86B4B]' : 'text-[#2B2520]' }}">
                        <span>CATALOG</span>
                        @if(request()->routeIs('catalog.*'))
                            <span aria-hidden="true" class="h-px w-6 bg-[#B86B4B]"></span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('story.index') }}" @click="mobileOpen = false"
                       class="flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('story.*') ? 'text-[#B86B4B]' : 'text-[#2B2520]' }}">
                        <span>OUR STORY</span>
                        @if(request()->routeIs('story.*'))
                            <span aria-hidden="true" class="h-px w-6 bg-[#B86B4B]"></span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.dashboard') }}" @click="mobileOpen = false"
                       class="flex items-center justify-between border-b border-[#E5E0D8] py-3.5 text-[11px] font-medium uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('user.*') ? 'text-[#B86B4B]' : 'text-[#2B2520]' }}">
                        <span>MY ACCOUNT</span>
                        @if(request()->routeIs('user.*'))
                            <span aria-hidden="true" class="h-px w-6 bg-[#B86B4B]"></span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-5">
            <form action="{{ route('catalog.index') }}" method="GET" role="search" class="w-full">
                <label for="user-nav-search-mobile" class="sr-only">Search products</label>
                <div class="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-4 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                    <input
                        id="user-nav-search-mobile"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="min-w-0 flex-1 appearance-none border-0 bg-transparent p-0 font-[inherit] text-xs text-[#5C554F] shadow-none placeholder:text-[#8C837A] focus:border-0 focus:outline-none focus:ring-0"
                    />
                    <button type="submit" aria-label="Search" class="ml-2 inline-flex text-[#8C837A] transition-colors hover:text-[#B86B4B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</header>
