<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catalog') — {{ config('app.name', 'Decak Handmade') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Pacifico&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  NAVBAR                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 border-b border-[#E5E0D8] bg-[#F9F6F1]/95 backdrop-blur-md">
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
                        <label for="catalog-nav-search" class="sr-only">Search products</label>
                        <div class="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-3.5 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                            <input
                                id="catalog-nav-search"
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

                {{-- User account icon --}}
                <a href="{{ route('user.dashboard') }}" aria-label="My account" class="inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B] p-1">
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
                <button type="button" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Toggle menu" class="inline-flex text-[#2B2520] transition-colors hover:text-[#B86B4B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#B86B4B] md:hidden p-1">
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
                    <label for="catalog-nav-search-mobile" class="sr-only">Search products</label>
                    <div class="flex items-center rounded-full border border-[#D6CFC5] bg-transparent px-4 py-1.5 text-xs transition-colors focus-within:border-[#B86B4B]">
                        <input
                            id="catalog-nav-search-mobile"
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

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  MAIN CONTENT                                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <main>
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  FOOTER                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
        $footerWhatsapp = $whatsappUrl ?? null;

        if (! $footerWhatsapp) {
            $footerPhone = preg_replace('/\D+/', '', (string) \App\Models\Setting::get('store_phone', '0812-3456-7890')) ?? '';

            if (str_starts_with($footerPhone, '0')) {
                $footerPhone = '62'.substr($footerPhone, 1);
            } elseif (str_starts_with($footerPhone, '8')) {
                $footerPhone = '62'.$footerPhone;
            }

            $footerWhatsapp = 'https://wa.me/'.($footerPhone !== '' ? $footerPhone : '6281234567890');
        }
    @endphp

    <footer class="bg-[#241F1E] text-gray-400">
        <div class="mx-auto max-w-7xl px-8 py-16 md:px-16">
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.1fr] lg:gap-10">

                {{-- Column 1: Brand --}}
                <div>
                    <div class="flex flex-col items-start">
                        {{-- Vintage sewing machine illustration --}}
                        <svg viewBox="0 0 72 56" fill="none" stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                             class="h-12 w-16 text-gray-300">
                            <path d="M6 49h60" />
                            <path d="M10 49v-4h52v4" />
                            <path d="M54 45V17h8v28" />
                            <path d="M62 17H30c-5 0-9 4-9 9v7" />
                            <path d="M21 33h6v9" />
                            <path d="M24 42v5" />
                            <path d="M24 47v2" />
                            <circle cx="58" cy="33" r="6" />
                            <path d="M58 27v12M52 33h12" />
                            <path d="M37 17v-6h8v6" />
                            <path d="M35 11h12" />
                        </svg>

                        <span class="mt-3 text-3xl leading-none text-gray-200" style="font-family: 'Pacifico', cursive;">Decak</span>
                    </div>

                    <p class="mt-5 max-w-xs text-sm leading-relaxed text-gray-400">
                        Handmade creations with a personal touch for every special story.
                    </p>
                </div>

                {{-- Column 2: Navigation --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-300">Navigation</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" class="transition-colors hover:text-white">Home</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="transition-colors hover:text-white">Catalog</a></li>
                        <li><span class="cursor-default text-gray-400">Our story</span></li>
                        <li><a href="#my-account" class="transition-colors hover:text-white">My Account</a></li>
                    </ul>
                </div>

                {{-- Column 3: Help --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-300">Help</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="#faq" class="transition-colors hover:text-white">FAQ</a></li>
                        <li><a href="#how-to-order" class="transition-colors hover:text-white">How to Order</a></li>
                        <li><a href="#privacy-policy" class="transition-colors hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact us --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-300">Contact us</h3>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li>
                            <a href="https://instagram.com/decak.handmade" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2.5 transition-colors hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true" class="h-4 w-4 shrink-0">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                                </svg>
                                @decak.handmade
                            </a>
                        </li>
                        <li>
                            <a href="mailto:Decakhandmade@gmail.com" class="inline-flex items-center gap-2.5 transition-colors hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true" class="h-4 w-4 shrink-0">
                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>
                                Decakhandmade@gmail.com
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Decak Handmade. All rights reserved.</p>

                <div class="flex items-center gap-6">
                    <a href="https://instagram.com/decak.handmade" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm transition-colors hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true" class="h-4 w-4">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                        </svg>
                        Instagram
                    </a>

                    <a href="{{ $footerWhatsapp }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm transition-colors hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                             aria-hidden="true" class="h-4 w-4">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <x-cart-drawer />

    @stack('floating')
    @stack('scripts')

</body>
</html>

