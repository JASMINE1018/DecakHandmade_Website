@extends('layouts.admin')

@section('title', 'Laporan Produk')
@section('subtitle', 'Peringkat produk berdasarkan unit terjual pada pesanan Selesai')

@section('content')
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-muted">Dari tanggal</label>
                <input type="date" name="start_date" value="{{ $start }}" class="rounded-lg border border-line bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-muted">Sampai tanggal</label>
                <input type="date" name="end_date" value="{{ $end }}" class="rounded-lg border border-line bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
            </div>
            <button class="admin-btn-secondary">Terapkan</button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export', ['format' => 'pdf', 'start_date' => $start, 'end_date' => $end]) }}"
               class="admin-btn-secondary">Export PDF</a>
            <a href="{{ route('admin.reports.export', ['format' => 'xlsx', 'start_date' => $start, 'end_date' => $end]) }}"
               class="admin-btn-secondary">Export XLSX</a>
        </div>
    </div>

    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Penjualan</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ rupiah($summary['revenue']) }}</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Unit terjual</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $summary['units'] }}</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Pesanan selesai</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $summary['orders'] }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-line bg-white">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Unit</th>
                        <th>Pesanan</th>
                        <th class="text-right">Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="font-medium text-ink">{{ $product->product_name }}</td>
                            <td class="text-slate-600">{{ $product->units }}</td>
                            <td class="text-slate-600">{{ $product->orders }}</td>
                            <td class="text-right font-medium text-ink">{{ rupiah($product->revenue) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-muted">Belum ada penjualan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
