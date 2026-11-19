@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('subtitle', $product->exists ? $product->name : 'Lengkapi data produk baru')

@section('content')
    @php
        $isEdit = $product->exists;
        $images = app(\App\Services\ImageStorageService::class);
    @endphp

    <div class="grid gap-6 xl:grid-cols-3" x-data="{
        variantModalOpen: false,
        variantAction: '',
        variant: {
            name: '',
            type: 'color',
            color_hex: '#c86d51',
            sku: '',
            price_adjustment: 0,
            is_active: true
        },
        openVariantEdit(data, url) {
            this.variant = {
                name: data.name,
                type: data.type || 'color',
                color_hex: data.color_hex || '#c86d51',
                sku: data.sku || '',
                price_adjustment: data.price_adjustment || 0,
                is_active: Boolean(data.is_active)
            };
            this.variantAction = url;
            this.variantModalOpen = true;
        },

        optionModalOpen: false,
        optionAction: '',
        option: {
            name: '',
            input_type: 'text',
            price: 0,
            max_length: '',
            is_required: false,
            is_active: true
        },
        openOptionEdit(data, url) {
            this.option = {
                name: data.name,
                input_type: data.input_type || 'text',
                price: data.price || 0,
                max_length: data.max_length || '',
                is_required: Boolean(data.is_required),
                is_active: Boolean(data.is_active)
            };
            this.optionAction = url;
            this.optionModalOpen = true;
        }
    }">
        <div class="space-y-6 xl:col-span-2">
            <form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
                  enctype="multipart/form-data" class="admin-card">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                    <input type="hidden" name="updated_at_token" value="{{ $product->updated_at?->toIso8601String() }}">
                @endif

                <h2 class="mb-4 text-sm font-semibold text-ink">Informasi produk</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-ink">Nama produk</label>
                        <input name="name" value="{{ old('name', $product->name) }}" required maxlength="150"
                               class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <label class="block text-sm font-medium text-ink">Kategori</label>
                            <a href="{{ route('admin.products.index', ['tab' => 'categories']) }}" target="_blank" rel="noopener"
                               class="text-xs font-medium text-brand-700 hover:underline">Kelola kategori</a>
                        </div>
                        <select name="category_id" required class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">SKU</label>
                        <input name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="50"
                               class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Harga dasar</label>
                        <input name="base_price" type="number" min="0" max="2000000000" value="{{ old('base_price', $product->base_price) }}" required
                               class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                    </div>

                    @if (! $isEdit)
                        <div>
                            <label class="mb-1 block text-sm font-medium text-ink">Stok awal</label>
                            <input name="stock" type="number" min="0" max="10000000" value="{{ old('stock', 0) }}" required
                                   class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                        </div>
                    @endif

                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Stok minimum</label>
                        <input name="min_stock" type="number" min="0" max="10000000" value="{{ old('min_stock', $product->min_stock ?? 0) }}"
                               class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-ink">Deskripsi</label>
                        <textarea name="description" rows="4" class="w-full rounded-lg border border-line px-3 py-2 text-sm">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-line text-brand-600">
                        Produk aktif
                    </label>
                </div>

                <div class="mt-5 border-t border-line pt-4">
                    <label class="mb-1 block text-sm font-medium text-ink">Tambah foto (JPG/PNG, maks 2 MB, maks 5)</label>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png"
                           class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">Kembali</a>
                    <button class="admin-btn-primary">Simpan</button>
                </div>
            </form>

            @if ($isEdit)
                <div class="admin-card">
                    <h2 class="mb-3 text-sm font-semibold text-ink">Galeri</h2>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @forelse (($product->gallery ?? []) as $index => $base)
                            <div class="group relative overflow-hidden rounded-lg border border-line">
                                <img src="{{ $images->productImageUrl($base, 'thumb') }}" class="h-24 w-full object-cover">
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $index]) }}" class="absolute right-1 top-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded bg-danger px-1.5 py-0.5 text-xs text-white">×</button>
                                </form>
                            </div>
                        @empty
                            <p class="col-span-full text-sm text-muted">Belum ada foto.</p>
                        @endforelse
                    </div>
                </div>

                <div class="admin-card">
                    <h2 class="mb-3 text-sm font-semibold text-ink">Variasi</h2>

                    <div class="overflow-x-auto">
                        <table class="admin-table admin-table-compact">
                            <thead>
                                <tr>
                                    <th class="py-2">Nama</th>
                                    <th class="py-2">Tipe</th>
                                    <th class="py-2">Tambahan harga</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($product->variants as $variant)
                                    <tr>
                                        <td class="py-2">{{ $variant->name }}</td>
                                        <td class="py-2 capitalize">{{ $variant->type }}</td>
                                        <td class="py-2">{{ rupiah($variant->price_adjustment) }}</td>
                                        <td class="py-2">{{ $variant->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                        <td class="py-2 text-right">
                                            <button type="button"
                                                    @click="openVariantEdit({{ Js::from($variant) }}, '{{ route('admin.products.variants.update', [$product, $variant]) }}')"
                                                    class="text-xs font-medium text-brand-700 hover:underline">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" class="ml-3 inline" onsubmit="return confirm('Hapus variasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-danger hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-center text-muted">Belum ada variasi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <details class="mt-4 rounded-lg border border-line p-3">
                        <summary class="cursor-pointer text-sm font-medium text-brand-700">Tambah variasi</summary>
                        <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" enctype="multipart/form-data" class="mt-3 grid gap-3 sm:grid-cols-2">
                            @csrf
                            <input name="name" placeholder="Nama variasi" required class="rounded-lg border border-line px-3 py-2 text-sm">
                            <select name="type" class="rounded-lg border border-line px-3 py-2 text-sm">
                                <option value="color">Warna</option>
                                <option value="motif">Motif</option>
                            </select>
                            <input name="color_hex" type="color" value="#c86d51" class="h-10 w-full rounded-lg border border-line">
                            <input type="file" name="image" accept="image/jpeg,image/png" class="rounded-lg border border-line px-3 py-2 text-sm">
                            <input name="sku" placeholder="SKU variasi (opsional)" class="rounded-lg border border-line px-3 py-2 text-sm">
                            <input name="price_adjustment" type="number" min="0" value="0" placeholder="Tambahan harga" class="rounded-lg border border-line px-3 py-2 text-sm">
                            <button class="admin-btn-primary sm:col-span-2">Simpan variasi</button>
                        </form>
                    </details>
                </div>

                <div class="admin-card">
                    <h2 class="mb-3 text-sm font-semibold text-ink">Opsi personalisasi</h2>

                    <div class="overflow-x-auto">
                        <table class="admin-table admin-table-compact">
                            <thead>
                                <tr>
                                    <th class="py-2">Nama</th>
                                    <th class="py-2">Tipe</th>
                                    <th class="py-2">Biaya</th>
                                    <th class="py-2">Wajib</th>
                                    <th class="py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($product->customizationOptions as $option)
                                    <tr>
                                        <td class="py-2">{{ $option->name }}</td>
                                        <td class="py-2">{{ $option->input_type === 'text' ? 'Teks' : 'Pilihan' }}</td>
                                        <td class="py-2">{{ rupiah($option->price) }}</td>
                                        <td class="py-2">{{ $option->is_required ? 'Ya' : 'Tidak' }}</td>
                                        <td class="py-2 text-right">
                                            <button type="button"
                                                    @click="openOptionEdit({{ Js::from($option) }}, '{{ route('admin.products.customization-options.update', [$product, $option]) }}')"
                                                    class="text-xs font-medium text-brand-700 hover:underline">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.products.customization-options.destroy', [$product, $option]) }}" class="ml-3 inline" onsubmit="return confirm('Hapus opsi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-danger hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-center text-muted">Belum ada opsi personalisasi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <details class="mt-4 rounded-lg border border-line p-3">
                        <summary class="cursor-pointer text-sm font-medium text-brand-700">Tambah opsi personalisasi</summary>
                        <form method="POST" action="{{ route('admin.products.customization-options.store', $product) }}" class="mt-3 grid gap-3 sm:grid-cols-2">
                            @csrf
                            <input name="name" placeholder="Nama opsi" required class="rounded-lg border border-line px-3 py-2 text-sm">
                            <select name="input_type" class="rounded-lg border border-line px-3 py-2 text-sm">
                                <option value="text">Teks</option>
                                <option value="select">Pilihan</option>
                            </select>
                            <input name="price" type="number" min="0" value="0" placeholder="Biaya tambahan" class="rounded-lg border border-line px-3 py-2 text-sm">
                            <input name="max_length" type="number" min="1" max="200" placeholder="Maks karakter (untuk teks)" class="rounded-lg border border-line px-3 py-2 text-sm">
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_required" value="1" class="rounded border-line text-brand-600"> Wajib diisi</label>
                            <button class="admin-btn-primary sm:col-span-2">Simpan opsi</button>
                        </form>
                    </details>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($isEdit)
                <div class="admin-card">
                    <h2 class="text-sm font-semibold text-ink">Penyesuaian stok</h2>
                    <p class="mt-1 text-xs text-muted">Stok saat ini: <span class="font-semibold text-ink">{{ $product->stock }}</span></p>

                    <form method="POST" action="{{ route('admin.products.stock-adjustments.store', $product) }}" class="mt-3 space-y-3">
                        @csrf
                        <select name="mode" class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                            <option value="in">Tambah stok</option>
                            <option value="out">Kurangi stok</option>
                            <option value="set">Set stok absolut</option>
                        </select>
                        <input name="quantity" type="number" min="0" required placeholder="Jumlah" class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                        <select name="reason" class="w-full rounded-lg border border-line px-3 py-2 text-sm">
                            <option value="purchase">Pembelian</option>
                            <option value="return">Retur</option>
                            <option value="damage">Rusak</option>
                            <option value="correction">Koreksi</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <textarea name="note" required minlength="5" maxlength="500" rows="2" placeholder="Catatan (wajib)" class="w-full rounded-lg border border-line px-3 py-2 text-sm"></textarea>
                        <button class="admin-btn-primary w-full">Simpan penyesuaian</button>
                    </form>
                </div>

                <div class="admin-card">
                    <h2 class="text-sm font-semibold text-ink">Riwayat stok terakhir</h2>
                    <ul class="mt-3 space-y-2 text-xs text-slate-600">
                        @forelse ($product->inventoryMovements()->latest('id')->limit(8)->get() as $movement)
                            <li class="flex items-center justify-between border-b border-line pb-2">
                                <span>{{ $movement->type }} · {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</span>
                                <span class="text-slate-400">{{ wib($movement->created_at, 'd/m H:i') }}</span>
                            </li>
                        @empty
                            <li>Belum ada pergerakan stok.</li>
                        @endforelse
                    </ul>
                </div>
            @else
                <div class="admin-card text-sm text-slate-600">
                    <p class="font-medium text-ink">Setelah produk disimpan</p>
                    <p class="mt-2">Kamu dapat menambahkan galeri, variasi, opsi personalisasi, dan penyesuaian stok.</p>
                </div>
            @endif
        </div>

        @if ($isEdit)
            <!-- Modal Edit Variasi -->
            <div x-cloak x-show="variantModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div x-show="variantModalOpen"
                     x-transition.opacity.duration.200ms
                     @click="variantModalOpen = false"
                     class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

                <div class="relative flex min-h-screen items-center justify-center p-4">
                    <div x-show="variantModalOpen"
                         x-transition.duration.200ms
                         class="relative z-10 w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-2xl">
                        <div class="flex items-center justify-between pb-3 border-b border-line">
                            <h3 class="text-base font-semibold text-ink">Edit Variasi</h3>
                            <button type="button" @click="variantModalOpen = false" class="text-muted hover:text-ink">
                                <x-icon name="x" class="h-5 w-5" />
                            </button>
                        </div>

                        <form method="POST" :action="variantAction" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama variasi</label>
                                <input name="name" x-model="variant.name" required maxlength="100" class="admin-input">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe</label>
                                    <select name="type" x-model="variant.type" class="admin-input">
                                        <option value="color">Warna</option>
                                        <option value="motif">Motif</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Warna Hex</label>
                                    <input name="color_hex" type="color" x-model="variant.color_hex" class="h-10 w-full rounded-lg border border-line">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">SKU variasi (opsional)</label>
                                <input name="sku" x-model="variant.sku" class="admin-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tambahan harga</label>
                                <input name="price_adjustment" type="number" min="0" x-model="variant.price_adjustment" class="admin-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Foto variasi baru (opsional)</label>
                                <input type="file" name="image" accept="image/jpeg,image/png" class="admin-input">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-muted pt-1">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" x-model="variant.is_active" class="rounded border-line text-brand-600 focus:ring-brand-200">
                                Aktif
                            </label>
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-line">
                                <button type="button" @click="variantModalOpen = false" class="admin-btn-secondary">Batal</button>
                                <button type="submit" class="admin-btn-primary">Simpan variasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Opsi Personalisasi -->
            <div x-cloak x-show="optionModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div x-show="optionModalOpen"
                     x-transition.opacity.duration.200ms
                     @click="optionModalOpen = false"
                     class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

                <div class="relative flex min-h-screen items-center justify-center p-4">
                    <div x-show="optionModalOpen"
                         x-transition.duration.200ms
                         class="relative z-10 w-full max-w-md rounded-2xl border border-line bg-white p-6 shadow-2xl">
                        <div class="flex items-center justify-between pb-3 border-b border-line">
                            <h3 class="text-base font-semibold text-ink">Edit Opsi Personalisasi</h3>
                            <button type="button" @click="optionModalOpen = false" class="text-muted hover:text-ink">
                                <x-icon name="x" class="h-5 w-5" />
                            </button>
                        </div>

                        <form method="POST" :action="optionAction" class="mt-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama opsi</label>
                                <input name="name" x-model="option.name" required maxlength="100" class="admin-input">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe input</label>
                                    <select name="input_type" x-model="option.input_type" class="admin-input">
                                        <option value="text">Teks</option>
                                        <option value="select">Pilihan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Biaya tambahan</label>
                                    <input name="price" type="number" min="0" x-model="option.price" class="admin-input">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Maksimal karakter (untuk teks)</label>
                                <input name="max_length" type="number" min="1" max="200" x-model="option.max_length" class="admin-input">
                            </div>
                            <div class="flex items-center gap-4 pt-1">
                                <label class="flex items-center gap-2 text-sm text-muted">
                                    <input type="hidden" name="is_required" value="0">
                                    <input type="checkbox" name="is_required" value="1" x-model="option.is_required" class="rounded border-line text-brand-600 focus:ring-brand-200">
                                    Wajib diisi
                                </label>
                                <label class="flex items-center gap-2 text-sm text-muted">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" x-model="option.is_active" class="rounded border-line text-brand-600 focus:ring-brand-200">
                                    Aktif
                                </label>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-line">
                                <button type="button" @click="optionModalOpen = false" class="admin-btn-secondary">Batal</button>
                                <button type="submit" class="admin-btn-primary">Simpan opsi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
