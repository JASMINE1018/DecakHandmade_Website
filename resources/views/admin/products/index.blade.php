@extends('layouts.admin')

@section('title', $tab === 'categories' ? 'Kategori Produk' : 'Kelola Produk')
@section('subtitle', $tab === 'categories' ? 'Kelompokkan produk agar mudah dicari' : 'Kelola informasi, stok, dan variasi handmade')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <nav class="inline-flex rounded-full border border-line bg-white p-1 text-sm">
            <a href="{{ route('admin.products.index') }}"
               class="rounded-full px-4 py-1.5 font-medium transition-colors {{ $tab === 'products' ? 'bg-brand-600 text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                Produk
            </a>
            <a href="{{ route('admin.products.index', ['tab' => 'categories']) }}"
               class="rounded-full px-4 py-1.5 font-medium transition-colors {{ $tab === 'categories' ? 'bg-brand-600 text-white shadow-sm' : 'text-muted hover:text-ink' }}">
                Kategori
            </a>
        </nav>
    </div>

    @if ($tab === 'categories')
        @include('admin.categories._manager', ['categories' => $categoryManager])
    @else
        <div class="mb-5">
            <h2 class="text-2xl font-bold text-ink">Total produk: <span class="text-brand-600">{{ $products->total() }} produk</span></h2>
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-brand-500">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                       class="w-56 rounded-full border border-brand-300 bg-white py-2 pl-9 pr-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
            </div>
            <select name="category" class="rounded-lg border border-brand-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-brand-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
            <button class="admin-btn-secondary">Terapkan</button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Tambah produk
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-line bg-white">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $thumb = app(\App\Services\ImageStorageService::class)->productImageUrl($product->gallery[0] ?? null, 'thumb');
                        @endphp
                        <tr>
                            <td>
                                @if ($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $product->name }}" class="h-11 w-11 rounded-lg border border-line object-cover">
                                @else
                                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-50 text-xs text-brand-400">—</span>
                                @endif
                            </td>
                            <td>
                                <p class="font-medium text-ink">{{ $product->name }}</p>
                                <p class="text-xs text-muted">{{ $product->sku }}</p>
                            </td>
                            <td class="text-slate-600">{{ $product->category?->name }}</td>
                            <td class="text-slate-600">{{ rupiah($product->base_price) }}</td>
                            <td>
                                <span class="admin-badge {{ $product->isLowStock() ? 'bg-danger/10 text-danger' : 'bg-brand-50 text-ink' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge {{ $product->is_active ? 'admin-badge-success' : 'admin-badge-muted' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="admin-icon-btn admin-icon-btn-edit"
                                       title="Edit {{ $product->name }}" aria-label="Edit {{ $product->name }}">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Arsipkan produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="admin-icon-btn admin-icon-btn-danger"
                                                title="Arsipkan {{ $product->name }}" aria-label="Arsipkan {{ $product->name }}">
                                            <x-icon name="archive" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-muted">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-line px-5 py-3">
            {{ $products->links() }}
        </div>
        </div>
    @endif
@endsection
