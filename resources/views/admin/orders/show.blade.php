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

            @if ($order->isFinal())
                <div class="admin-card">
                    <h2 class="text-sm font-semibold text-ink">Status pesanan</h2>
                    <div class="mt-3 flex items-center gap-2">
                        <x-status-badge :status="$order->status" />
                        <span class="text-xs text-muted">Status sudah final dan tidak dapat diubah lagi.</span>
                    </div>
                </div>
            @else
                <div class="admin-card" x-data="{
                    targetStatus: '{{ $order->status === \App\Models\Order::STATUS_PROCESSING ? \App\Models\Order::STATUS_SHIPPED : \App\Models\Order::STATUS_COMPLETED }}'
                }">
                    <div class="flex items-center justify-between pb-3 border-b border-line">
                        <h2 class="text-sm font-semibold text-ink">Ubah status</h2>
                        <span class="text-xs text-muted">Saat ini: <span class="font-medium text-ink">{{ $order->statusLabel() }}</span></span>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-700">Pilih status baru:</label>

                            <div class="space-y-2">
                                {{-- Option: In Delivery --}}
                                @if ($order->status !== \App\Models\Order::STATUS_SHIPPED)
                                    <label class="flex items-center justify-between rounded-xl border p-3 cursor-pointer transition-all"
                                           :class="targetStatus === 'shipped' ? 'border-brand-500 bg-brand-50/70 ring-1 ring-brand-400' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="status" value="shipped" x-model="targetStatus"
                                                   class="h-4 w-4 text-brand-600 focus:ring-brand-200">
                                            <div>
                                                <p class="text-sm font-medium text-ink">In Delivery</p>
                                                <p class="text-xs text-muted">Pesanan dalam pengiriman</p>
                                            </div>
                                        </div>
                                        <span class="admin-badge bg-brand-100 text-brand-800">In Delivery</span>
                                    </label>
                                @endif

                                {{-- Option: Selesai --}}
                                @if ($order->status !== \App\Models\Order::STATUS_COMPLETED)
                                    <label class="flex items-center justify-between rounded-xl border p-3 cursor-pointer transition-all"
                                           :class="targetStatus === 'completed' ? 'border-emerald-500 bg-emerald-50/70 ring-1 ring-emerald-400' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="status" value="completed" x-model="targetStatus"
                                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-200">
                                            <div>
                                                <p class="text-sm font-medium text-ink">Selesai</p>
                                                <p class="text-xs text-muted">Pesanan telah selesai diterima</p>
                                            </div>
                                        </div>
                                        <span class="admin-badge bg-success-soft text-success-ink">Selesai</span>
                                    </label>
                                @endif

                                {{-- Option: Dibatalkan --}}
                                @if ($order->status !== \App\Models\Order::STATUS_CANCELLED)
                                    <label class="flex items-center justify-between rounded-xl border p-3 cursor-pointer transition-all"
                                           :class="targetStatus === 'cancelled' ? 'border-rose-500 bg-rose-50/70 ring-1 ring-rose-400' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="status" value="cancelled" x-model="targetStatus"
                                                   class="h-4 w-4 text-rose-600 focus:ring-rose-200">
                                            <div>
                                                <p class="text-sm font-medium text-ink">Dibatalkan</p>
                                                <p class="text-xs text-muted">Batalkan pesanan & kembalikan stok</p>
                                            </div>
                                        </div>
                                        <span class="admin-badge bg-danger/10 text-danger">Dibatalkan</span>
                                    </label>
                                @endif

                                {{-- Option: Diproses (Koreksi dari status In Delivery jika diperlukan) --}}
                                @if ($order->status === \App\Models\Order::STATUS_SHIPPED)
                                    <label class="flex items-center justify-between rounded-xl border p-3 cursor-pointer transition-all"
                                           :class="targetStatus === 'processing' ? 'border-amber-500 bg-amber-50/70 ring-1 ring-amber-400' : 'border-line hover:border-slate-300 bg-white'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="status" value="processing" x-model="targetStatus"
                                                   class="h-4 w-4 text-amber-600 focus:ring-amber-200">
                                            <div>
                                                <p class="text-sm font-medium text-ink">Diproses</p>
                                                <p class="text-xs text-amber-600">Koreksi kembali ke diproses</p>
                                            </div>
                                        </div>
                                        <span class="admin-badge bg-brand-500 text-white">Diproses</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="status-note" class="block text-xs font-medium text-slate-700 mb-1">Catatan (opsional)</label>
                            <input id="status-note" type="text" name="note" maxlength="500" placeholder="Catatan perubahan status..." class="admin-input">
                        </div>

                        @if ($order->status === \App\Models\Order::STATUS_SHIPPED)
                            <div x-show="targetStatus === 'cancelled'" x-cloak class="rounded-xl border border-amber-200 bg-amber-50/80 p-3">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="returned" value="1" class="mt-0.5 rounded border-amber-300 text-brand-600 focus:ring-brand-200">
                                    <span class="text-xs text-amber-900 leading-relaxed">
                                        Konfirmasi barang sudah kembali / gagal kirim (wajib dicentang untuk pembatalan pesanan In Delivery agar stok dikembalikan).
                                    </span>
                                </label>
                            </div>
                        @endif

                        <button type="submit"
                                :class="targetStatus === 'cancelled' ? 'admin-btn-danger w-full' : 'admin-btn-primary w-full'">
                            <span x-text="targetStatus === 'cancelled' ? 'Batalkan Pesanan' : (targetStatus === 'completed' ? 'Tandai Selesai' : (targetStatus === 'shipped' ? 'Tandai In Delivery' : 'Perbarui Status'))"></span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
