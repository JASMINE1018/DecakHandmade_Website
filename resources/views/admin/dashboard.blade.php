@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('subtitle', 'Ringkasan operasional hari ini (WIB)')

@section('content')
    @php
        $today = $data['today'];
        $daily = $data['daily'];
        $maxRevenue = max(1, $daily->max('revenue'));
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Omzet hari ini</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ rupiah($today['revenue']) }}</p>
            <p class="mt-1 text-xs text-ink/60">Subtotal pesanan selesai, tanpa ongkir</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Pesanan baru</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $data['new_orders'] }} transaksi</p>
            <p class="mt-1 text-xs text-ink/60">Dibuat hari ini, tanpa yang dibatalkan</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Perlu dikirim</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $data['processing'] }} pesanan</p>
            <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="mt-1 inline-block text-xs font-semibold text-brand-700 hover:underline">Lihat pesanan Diproses</a>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Stok menipis</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $data['low_stock'] }} produk</p>
            <a href="{{ route('admin.products.index') }}" class="mt-1 inline-block text-xs font-semibold text-brand-700 hover:underline">Buka daftar produk</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="admin-card xl:col-span-2">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-ink">Penjualan 7 hari terakhir</h2>
                    <p class="text-xs text-muted">{{ $data['week_start'] }} s.d. {{ $data['today_date'] }}</p>
                </div>
                <a href="{{ route('admin.reports.sales.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">Rekap lengkap</a>
            </div>

            <div class="flex h-52 items-end gap-2">
                @foreach ($daily as $day)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex w-full flex-1 items-end">
                            <div class="w-full rounded-t-md bg-brand-500 transition-colors hover:bg-brand-600"
                                 style="height: {{ max(4, (int) round(($day['revenue'] / $maxRevenue) * 100)) }}%"
                                 title="{{ rupiah($day['revenue']) }}"></div>
                        </div>
                        <span class="text-[10px] text-muted">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="admin-card-peach">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-ink">Pesanan terbaru</h2>
                    <p class="text-xs text-ink/60">Pantau transaksi yang baru masuk</p>
                </div>
            </div>

            <div class="mt-3 overflow-hidden rounded-xl border border-brand-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data['latest_orders'] as $order)
                                <tr>
                                    <td class="font-medium text-ink">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-brand-700">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="text-slate-600">{{ $order->customer?->name }}</td>
                                    <td class="text-slate-600">{{ rupiah($order->grand_total) }}</td>
                                    <td><x-status-badge :status="$order->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-muted">Belum ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('admin.orders.index') }}" class="admin-btn-primary mt-3 w-full">Lihat semua pesanan</a>
        </div>
    </div>

    <div class="admin-card-peach mt-6">
        <h2 class="text-sm font-semibold text-ink">Aksi cepat</h2>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.products.create') }}" class="admin-btn-primary w-full">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah produk baru
            </a>
            <a href="{{ route('admin.banners.create') }}" class="admin-btn-primary w-full">
                <x-icon name="plus" class="h-4 w-4" />
                Buat banner promo
            </a>
            <a href="{{ route('admin.reports.sales.index') }}" class="admin-btn-primary w-full">
                <x-icon name="reports" class="h-4 w-4" />
                Cetak laporan
            </a>
            <a href="{{ route('admin.orders.create') }}" class="admin-btn-primary w-full">
                <x-icon name="orders" class="h-4 w-4" />
                Catat pesanan
            </a>
        </div>
    </div>
@endsection
