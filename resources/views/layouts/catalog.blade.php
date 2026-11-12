<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catalog') — {{ config('app.name', 'Decak Handmade') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  NAVBAR                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 border-b border-line/60 bg-cream/90 backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            {{-- ── Left: Logo ── --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">DH</span>
                <span class="text-lg font-bold tracking-tight text-ink">Decak <span class="text-brand-600">Handmade</span></span>
            </a>

            {{-- ── Centre: Nav links (desktop) ── --}}
            <ul class="hidden items-center gap-8 text-sm font-medium md:flex">
                <li>
                    <a href="{{ url('/') }}" class="transition-colors hover:text-brand-600 {{ request()->is('/') ? 'text-brand-600' : 'text-ink' }}">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('catalog.index') }}" class="transition-colors hover:text-brand-600 {{ request()->routeIs('catalog.*') ? 'text-brand-600' : 'text-ink' }}">
                        Catalog
                    </a>
                </li>
                <li>
                    <a href="#our-story" class="transition-colors hover:text-brand-600 text-ink">
                        Our Story
                    </a>
                </li>
                <li>
                    <a href="#my-account" class="transition-colors hover:text-brand-600 text-ink">
                        My Account
                    </a>
                </li>
            </ul>

            {{-- ── Right: Action icons + Shop Now ── --}}
            <div class="flex items-center gap-3">
                {{-- Cart icon button --}}
                <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-line bg-white text-ink transition-colors hover:bg-brand-50 hover:text-brand-600">
                    {{-- Shopping bag icon (Heroicons outline) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    {{-- Cart badge (optional, shows count) --}}
                    {{-- <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">3</span> --}}
                </button>

                {{-- Shop Now button --}}
                <a href="{{ route('catalog.index') }}" class="hidden items-center gap-2 rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-700 sm:inline-flex">
                    Shop Now
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>

                {{-- Mobile menu toggle --}}
                <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-white text-ink md:hidden">
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
             class="border-t border-line bg-cream px-4 pb-4 md:hidden">
            <ul class="space-y-1 pt-3 text-sm font-medium">
                <li><a href="{{ url('/') }}" class="block rounded-lg px-3 py-2.5 transition-colors hover:bg-brand-50 {{ request()->is('/') ? 'text-brand-600 bg-brand-50' : 'text-ink' }}">Home</a></li>
                <li><a href="{{ route('catalog.index') }}" class="block rounded-lg px-3 py-2.5 transition-colors hover:bg-brand-50 {{ request()->routeIs('catalog.*') ? 'text-brand-600 bg-brand-50' : 'text-ink' }}">Catalog</a></li>
                <li><a href="#our-story" class="block rounded-lg px-3 py-2.5 text-ink transition-colors hover:bg-brand-50">Our Story</a></li>
                <li><a href="#my-account" class="block rounded-lg px-3 py-2.5 text-ink transition-colors hover:bg-brand-50">My Account</a></li>
            </ul>
            <a href="{{ route('catalog.index') }}" class="mt-3 flex w-full items-center justify-center gap-2 rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-700">
                Shop Now
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  MAIN CONTENT                                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <main>
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{--  FOOTER                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <footer class="border-t border-line bg-brand-900 text-white/80">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">DH</span>
                    <span class="text-sm font-semibold text-white">Decak Handmade</span>
                </div>
                <p class="text-xs text-white/50">&copy; {{ date('Y') }} Decak Handmade. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>

