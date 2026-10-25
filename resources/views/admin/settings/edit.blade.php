@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('subtitle', 'Informasi toko yang dipakai pada struk dan laporan')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <div class="admin-card">
            <h2 class="mb-4 text-sm font-semibold text-ink">Identitas toko</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-ink">Nama toko</label>
                    <input name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'Decak Handmade') }}" required maxlength="120" class="admin-input">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Telepon toko</label>
                    <input name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" maxlength="30" class="admin-input">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Prefix nomor pesanan</label>
                    <input name="order_prefix" value="{{ old('order_prefix', $settings['order_prefix'] ?? 'ORD') }}" maxlength="6" class="admin-input uppercase">
                    <p class="mt-1 text-xs text-muted">2–6 karakter alfanumerik.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-ink">Alamat toko</label>
                    <textarea name="store_address" rows="3" maxlength="500" class="admin-input">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-ink">Catatan tambahan</label>
                    <textarea name="store_note" rows="2" maxlength="500" class="admin-input">{{ old('store_note', $settings['store_note'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-ink">Ambang stok menipis</label>
                    <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? 3) }}" class="admin-input">
                    <p class="mt-1 text-xs text-muted">Dipakai sebagai nilai default peringatan stok.</p>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button class="admin-btn-primary">Simpan pengaturan</button>
            </div>
        </div>
    </form>
@endsection
