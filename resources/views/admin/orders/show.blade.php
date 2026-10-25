@extends('layouts.admin')

@section('title', 'Pesanan '.$order->order_number)
@section('subtitle', 'Dibuat '.wib($order->created_at).' WIB oleh '.$order->creator?->name)

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <x-status-badge :status="$order->status" />
            <span class="text-sm text-muted">{{ $order->items->sum('quantity') }} unit · {{ $order->items->count() }} item</span>
        </div>

        <div class="flex gap-2">
            @unless ($order->isFinal())
                <a href="{{ route('admin.orders.edit', $order) }}" class="admin-btn-secondary">Edit catatan</a>
            @endunless
            <a href="{{ route('admin.orders.index') }}" class="admin-btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-line bg-white">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="text-sm font-semibold text-ink">Item pesanan</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga satuan</th>
                                <th>Qty</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <p class="font-medium text-ink">{{ $item->product_name }}</p>
                                        <p class="text-xs text-muted">{{ $item->sku }}</p>

                                        @foreach ($item->variants as $variant)
                                            <p class="mt-1 text-xs text-muted">Variasi: {{ $variant->variant_name }} ({{ rupiah($variant->price_adjustment) }})</p>
                                        @endforeach

                                        @foreach ($item->customizations as $customization)
                                            <p class="mt-1 text-xs text-muted">{{ $customization->option_name }}: {{ $customization->value }} ({{ rupiah($customization->price) }})</p>
                                        @endforeach

                                        @if ($item->note)
                                            <p class="mt-1 text-xs text-slate-400">Catatan: {{ $item->note }}</p>
                                        @endif
                                    </td>
                                    <td class="text-slate-600">{{ rupiah($item->unit_price) }}</td>
                                    <td class="text-slate-600">{{ $item->quantity }}</td>
                                    <td class="text-right font-medium text-ink">{{ rupiah($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-2 border-t border-line px-5 py-4 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal produk</span>
                        <span>{{ rupiah($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos kirim</span>
                        <span>{{ rupiah($order->shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-line pt-2 text-base font-semibold text-ink">
                        <span>Total pembayaran</span>
                        <span>{{ rupiah($order->grand_total) }}</span>
                    </div>
                    <p class="text-xs text-muted">Penjualan yang diakui pada laporan adalah subtotal produk, tanpa ongkir.</p>
                </div>
            </div>

            <div class="admin-card">
                <h2 class="mb-4 text-sm font-semibold text-ink">Riwayat status</h2>
                <ol class="space-y-4">
                    @foreach ($order->statusHistories as $history)
                        <li class="flex gap-3">
                            <span class="mt-1 h-2 w-2 flex-none rounded-full bg-brand-500"></span>
                            <div>
                                <p class="text-sm text-ink">
                                    {{ $history->from_status ? (\App\Models\Order::STATUSES[$history->from_status] ?? $history->from_status).' → ' : '' }}
                                    <span class="font-medium">{{ \App\Models\Order::STATUSES[$history->to_status] ?? $history->to_status }}</span>
                                </p>
                                @if ($history->note)
                                    <p class="text-xs text-muted">{{ $history->note }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ wib($history->created_at) }} WIB · {{ $history->actor?->name }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="space-y-6">
            <div class="admin-card">
                <h2 class="text-sm font-semibold text-ink">Pelanggan</h2>
                <p class="mt-3 text-sm font-medium text-ink">{{ $order->customer?->name }}</p>
                <p class="text-sm text-slate-600">{{ $order->customer?->phone }}</p>
                @if ($order->customer?->email)
                    <p class="text-sm text-slate-600">{{ $order->customer->email }}</p>
                @endif
                @if ($order->customer?->address)
                    <p class="mt-2 text-sm text-slate-600">{{ $order->customer->address }}</p>
                @endif
            </div>

            @if ($order->customer_note || $order->admin_note)
                <div class="admin-card">
                    <h2 class="text-sm font-semibold text-ink">Catatan</h2>
                    @if ($order->customer_note)
                        <p class="mt-2 text-sm text-slate-600"><span class="font-medium text-ink">Pelanggan:</span> {{ $order->customer_note }}</p>
                    @endif
                    @if ($order->admin_note)
                        <p class="mt-2 text-sm text-slate-600"><span class="font-medium text-ink">Internal:</span> {{ $order->admin_note }}</p>
                    @endif
                </div>
            @endif

            @unless ($order->isFinal())
                <div class="admin-card">
                    <h2 class="text-sm font-semibold text-ink">Ubah status</h2>

                    @foreach ($transitions as $to)
                        @continue($to === 'cancelled')
                        <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="mt-3 space-y-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $to }}">
                            <input name="note" maxlength="500" placeholder="Catatan (opsional)" class="admin-input">
                            @if ($order->status === 'shipped' && $to === 'processing')
                                <p class="text-xs text-amber-600">Koreksi ke Diproses akan dicatat pada riwayat.</p>
                            @endif
                            <button class="admin-btn-primary w-full">
                                Tandai {{ \App\Models\Order::STATUSES[$to] ?? $to }}
                            </button>
                        </form>
                    @endforeach

                    @if (in_array('cancelled', $transitions, true))
                        <div class="mt-5 border-t border-line pt-4">
                            <h3 class="text-sm font-semibold text-danger">Batalkan pesanan</h3>
                            <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="mt-2 space-y-2">
                                @csrf
                                <select name="reason" class="admin-input">
                                    @foreach ($cancelReasons as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <textarea name="note" required minlength="10" maxlength="500" rows="2" placeholder="Alasan pembatalan (wajib, min. 10 karakter)" class="admin-input"></textarea>
                                @if ($order->status === 'shipped')
                                    <label class="flex items-start gap-2 text-xs text-muted">
                                        <input type="checkbox" name="returned" value="1" class="mt-0.5 rounded border-line text-brand-600 focus:ring-brand-200">
                                        Barang sudah kembali / gagal kirim (wajib untuk pesanan Dikirim)
                                    </label>
                                @endif
                                <button class="admin-btn-danger w-full">Batalkan pesanan</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endunless
        </div>
    </div>
@endsection
