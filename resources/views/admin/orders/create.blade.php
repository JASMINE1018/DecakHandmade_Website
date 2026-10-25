@extends('layouts.admin')

@section('title', 'Catat Pesanan')
@section('subtitle', 'Harga dihitung ulang oleh server saat disimpan')

@section('content')
    @php
        $productsJson = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'base_price' => $product->base_price,
            'stock' => $product->stock,
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'price_adjustment' => $variant->price_adjustment,
            ])->values(),
            'options' => $product->customizationOptions->map(fn ($option) => [
                'id' => $option->id,
                'name' => $option->name,
                'input_type' => $option->input_type,
                'price' => $option->price,
                'max_length' => $option->max_length,
                'is_required' => (bool) $option->is_required,
            ])->values(),
        ])->values();
    @endphp

    <script>window.__PRODUCTS = @json($productsJson); window.__OLD = @json(old());</script>

    <form method="POST" action="{{ route('admin.orders.store') }}" x-data="orderForm()" class="grid gap-6 xl:grid-cols-3">
        @csrf

        <div class="space-y-6 xl:col-span-2">
            <div class="admin-card">
                <h2 class="mb-4 text-sm font-semibold text-ink">Pelanggan</h2>

                <div class="mb-4 flex gap-4 text-sm text-ink">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="customer_mode" value="existing" x-model="customerMode" checked class="text-brand-600">
                        Pelanggan lama
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="customer_mode" value="new" x-model="customerMode" class="text-brand-600">
                        Pelanggan baru
                    </label>
                </div>

                <div x-show="customerMode === 'existing'" x-cloak>
                    <label class="mb-1 block text-sm font-medium text-ink">Pilih pelanggan</label>
                    <select name="customer_id" class="admin-input">
                        <option value="">Pilih pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }} — {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="customerMode === 'new'" x-cloak class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Nama</label>
                        <input name="customer_name" value="{{ old('customer_name') }}" maxlength="120" class="admin-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Nomor telepon</label>
                        <input name="customer_phone" value="{{ old('customer_phone') }}" maxlength="20" placeholder="08xxxxxxxxxx" class="admin-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-ink">Email (opsional)</label>
                        <input name="customer_email" type="email" value="{{ old('customer_email') }}" class="admin-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-ink">Alamat</label>
                        <textarea name="customer_address" rows="2" class="admin-input">{{ old('customer_address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-ink">Item pesanan</h2>
                    <button type="button" @click="addItem()" class="admin-btn-secondary">
                        <x-icon name="plus" class="h-4 w-4" />
                        Tambah item
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, ii) in items" :key="ii">
                        <div class="rounded-xl border border-line bg-brand-50/40 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted" x-text="'Item ' + (ii + 1)"></p>
                                <button type="button" @click="removeItem(ii)" x-show="items.length > 1" class="text-xs font-medium text-danger hover:underline">Hapus</button>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-muted">Produk</label>
                                    <select :name="'items[' + ii + '][product_id]'" x-model="item.product_id" @change="onProductChange(item)" class="admin-input">
                                        <option value="">Pilih produk</option>
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id" x-text="product.name + ' — ' + formatRp(product.base_price) + ' (stok ' + product.stock + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-medium text-muted">Variasi</label>
                                    <select :name="'items[' + ii + '][variant_id]'" x-model="item.variant_id" class="admin-input">
                                        <option value="">Tanpa variasi</option>
                                        <template x-for="variant in variantsFor(item)" :key="variant.id">
                                            <option :value="variant.id" x-text="variant.name + (variant.price_adjustment ? ' (+' + formatRp(variant.price_adjustment) + ')' : '')"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-medium text-muted">Jumlah</label>
                                    <input :name="'items[' + ii + '][quantity]'" type="number" min="1" max="999" x-model="item.quantity" class="admin-input">
                                </div>

                                <div class="sm:col-span-2" x-show="optionsFor(item).length > 0" x-cloak>
                                    <p class="mb-2 text-xs font-medium text-muted">Personalisasi</p>
                                    <div class="space-y-2">
                                        <template x-for="(option, oi) in optionsFor(item)" :key="option.id">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <input type="hidden" :name="'items[' + ii + '][customizations][' + oi + '][option_id]'" :value="option.id">
                                                <label class="w-40 text-xs text-muted">
                                                    <span x-text="option.name"></span>
                                                    <span x-show="option.price > 0" class="text-slate-400" x-text="' (+' + formatRp(option.price) + ')'"></span>
                                                    <span x-show="option.is_required" class="text-danger">*</span>
                                                </label>
                                                <input :name="'items[' + ii + '][customizations][' + oi + '][value]'" x-model="item.values[option.id]"
                                                       :maxlength="option.max_length || 200" placeholder="Isi teks personalisasi"
                                                       class="admin-input min-w-48 flex-1">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-muted">Catatan item (opsional)</label>
                                    <input :name="'items[' + ii + '][note]'" x-model="item.note" maxlength="300" class="admin-input">
                                </div>
                            </div>

                            <p class="mt-3 text-right text-sm text-muted">
                                Subtotal item: <span class="font-semibold text-ink" x-text="formatRp(lineTotal(item))"></span>
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="admin-card">
                <h2 class="text-sm font-semibold text-ink">Ringkasan</h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-muted">Ongkos kirim</label>
                        <input name="shipping_cost" type="number" min="0" value="{{ old('shipping_cost', 0) }}" class="admin-input">
                    </div>

                    <div class="flex items-center justify-between border-t border-line pt-3">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold text-ink" x-text="formatRp(subtotal)"></span>
                    </div>
                    <p class="text-xs text-muted">Total akhir dihitung server dan ditampilkan setelah pesanan dibuat.</p>
                </div>
            </div>

            <div class="admin-card">
                <h2 class="text-sm font-semibold text-ink">Catatan</h2>
                <div class="mt-3 space-y-3">
                    <textarea name="customer_note" rows="2" maxlength="500" placeholder="Catatan dari pelanggan" class="admin-input">{{ old('customer_note') }}</textarea>
                    <textarea name="admin_note" rows="2" maxlength="500" placeholder="Catatan internal admin" class="admin-input">{{ old('admin_note') }}</textarea>
                </div>

                <button class="admin-btn-primary mt-4 w-full">Simpan pesanan</button>
                <a href="{{ route('admin.orders.index') }}" class="mt-2 block text-center text-xs text-muted hover:underline">Batal</a>
            </div>
        </div>
    </form>
@endsection
