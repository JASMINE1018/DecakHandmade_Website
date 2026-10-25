@extends('layouts.admin')

@section('title', 'Edit Pesanan '.$order->order_number)
@section('subtitle', 'Hanya catatan dan ongkir yang dapat diubah. Perubahan item dilakukan dengan membatalkan lalu membuat pesanan baru.')

@section('content')
    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="admin-card max-w-2xl">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-ink">Ongkos kirim</label>
                <input name="shipping_cost" type="number" min="0" value="{{ old('shipping_cost', $order->shipping_cost) }}" class="admin-input">
                <p class="mt-1 text-xs text-muted">Total pembayaran dihitung ulang dari subtotal + ongkir.</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-ink">Catatan pelanggan</label>
                <textarea name="customer_note" rows="3" maxlength="500" class="admin-input">{{ old('customer_note', $order->customer_note) }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-ink">Catatan internal</label>
                <textarea name="admin_note" rows="3" maxlength="500" class="admin-input">{{ old('admin_note', $order->admin_note) }}</textarea>
            </div>
        </div>

        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.orders.show', $order) }}" class="admin-btn-secondary">Batal</a>
            <button class="admin-btn-primary">Simpan</button>
        </div>
    </form>
@endsection
