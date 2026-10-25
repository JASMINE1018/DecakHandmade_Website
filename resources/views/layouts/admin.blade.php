<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
<div x-data="{ open: false }" class="min-h-screen lg:flex">
    <aside class="fixed inset-y-0 left-0 z-40 flex w-60 transform flex-col bg-brand-500 text-cream transition-transform lg:sticky lg:top-0 lg:bottom-auto lg:h-screen lg:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-20 items-center gap-3 border-b border-white/15 px-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-sm font-bold text-brand-600">DH</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">Decak Handmade</p>
                <p class="text-xs text-white/70">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
                    ['route' => 'admin.products.index', 'label' => 'Produk', 'active' => 'admin.products.*', 'icon' => 'products'],
                    ['route' => 'admin.orders.index', 'label' => 'Pesanan Masuk', 'active' => 'admin.orders.*', 'icon' => 'orders'],
                    ['route' => 'admin.reports.sales.index', 'label' => 'Rekap Penjualan', 'active' => 'admin.reports.*', 'icon' => 'reports'],
                    ['route' => 'admin.banners.index', 'label' => 'Promosi & Banner', 'active' => 'admin.banners.*', 'icon' => 'banners'],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-colors {{ request()->routeIs($link['active']) ? 'bg-brand-700 text-white shadow-sm' : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <x-icon :name="$link['icon']" class="h-5 w-5" />
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach

            <div class="mt-6 border-t border-white/15 pt-4">
                <a href="{{ route('admin.settings.edit') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-brand-700 text-white shadow-sm' : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <x-icon name="settings" class="h-5 w-5" />
                    <span>Pengaturan</span>
                </a>
                <a href="{{ route('admin.admins.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition-colors {{ request()->routeIs('admin.admins.*') ? 'bg-brand-700 text-white shadow-sm' : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <x-icon name="admins" class="h-5 w-5" />
                    <span>Admin</span>
                </a>
            </div>
        </nav>

        <div class="border-t border-white/15 p-4">
            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 rounded-lg p-2 transition-colors hover:bg-brand-600">
                <span class="flex h-9 w-9 flex-none items-center justify-center rounded-full bg-peach text-sm font-semibold text-brand-700">
                    {{ strtoupper(substr(auth('admin')->user()?->name ?? 'A', 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth('admin')->user()?->name }}</p>
                    <p class="truncate text-xs text-white/70">{{ auth('admin')->user()?->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-white/40 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-600">
                    <x-icon name="logout" class="h-4 w-4" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div x-show="open" x-cloak @click="open = false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between gap-4 border-b border-line/70 bg-cream/90 px-4 py-4 backdrop-blur lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" @click="open = !open" class="rounded-lg border border-line bg-white p-2 text-ink lg:hidden">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>
                <div>
                    <h1 class="text-xl font-bold text-ink lg:text-2xl">@yield('title', 'Dashboard')</h1>
                    <p class="text-xs text-muted">@yield('subtitle', 'Panel pengelolaan Decak Handmade')</p>
                </div>
            </div>

            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 rounded-full border border-line bg-white/70 py-1.5 pl-1.5 pr-3 transition-colors hover:bg-white">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-peach text-xs font-semibold text-brand-700">
                    {{ strtoupper(substr(auth('admin')->user()?->name ?? 'A', 0, 1)) }}
                </span>
                <span class="hidden text-sm font-medium text-ink sm:block">{{ auth('admin')->user()?->name }}</span>
            </a>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            @if (session('success'))
                <div class="mb-4 flex items-start gap-3 rounded-xl border border-success-ink/20 bg-success-soft px-4 py-3 text-sm text-success-ink">
                    <x-icon name="check" class="mt-0.5 h-4 w-4 flex-none" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-danger/20 bg-danger/10 px-4 py-3 text-sm text-danger">
                    <p class="font-semibold">Periksa kembali input berikut:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
