@extends('layouts.admin')

@section('title', 'Rekap Penjualan')
@section('subtitle', 'Penjualan diakui dari pesanan Selesai berdasarkan tanggal selesai (WIB)')

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

    @php
        $maxRevenue = max(1, $daily->max('revenue'));
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Total penjualan</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ rupiah($summary['revenue']) }}</p>
            <p class="mt-1 text-xs text-ink/60">Subtotal produk, tanpa ongkir</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Pesanan selesai</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $summary['orders'] }}</p>
            <p class="mt-1 text-xs text-ink/60">{{ $summary['units'] }} unit terjual</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Rata-rata pesanan</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ rupiah($summary['average']) }}</p>
            <p class="mt-1 text-xs text-ink/60">Per pesanan selesai</p>
        </div>
        <div class="admin-card-peach">
            <p class="text-xs font-medium uppercase tracking-wide text-ink/70">Pelanggan baru</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ $summary['new_customers'] }}</p>
            <p class="mt-1 text-xs text-ink/60">Pesanan pertama pada periode</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="admin-card xl:col-span-2">
            <h2 class="text-sm font-semibold text-ink">Grafik penjualan harian</h2>
            <p class="text-xs text-muted">{{ $start }} s.d. {{ $end }}</p>

            <div class="mt-4 flex h-56 items-end gap-1">
                @foreach ($daily as $day)
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="flex w-full flex-1 items-end">
                            <div class="w-full rounded-t bg-brand-500 transition-colors hover:bg-brand-600" title="{{ rupiah($day['revenue']) }}"
                                 style="height: {{ max(2, (int) round(($day['revenue'] / $maxRevenue) * 100)) }}%"></div>
                        </div>
                        @if ($daily->count() <= 14 || $loop->index % (int) ceil($daily->count() / 10) === 0)
                            <span class="text-[10px] text-muted">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-xs text-muted">Penjualan dihitung dari subtotal produk pada pesanan selesai.</p>
        </div>

        <div class="admin-card-peach">
            <h2 class="text-sm font-semibold text-ink">Produk terlaris</h2>
            <ol class="mt-3 space-y-3 text-sm">
                @forelse ($topProducts as $product)
                    <li class="flex items-start justify-between gap-2 border-b border-brand-200 pb-2 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-medium text-ink">{{ $product->product_name }}</p>
                            <p class="text-xs text-ink/60">{{ $product->units }} unit · {{ $product->orders }} pesanan</p>
                        </div>
                        <span class="text-xs font-medium text-ink/70">{{ rupiah($product->revenue) }}</span>
                    </li>
                @empty
                    <li class="text-ink/60">Belum ada penjualan pada periode ini.</li>
                @endforelse
            </ol>
            <a href="{{ route('admin.reports.products.index', ['start_date' => $start, 'end_date' => $end]) }}" class="mt-3 inline-block text-xs font-semibold text-brand-700 hover:underline">Lihat laporan produk</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-line bg-white">
        <div class="border-b border-line px-5 py-4">
            <h2 class="text-sm font-semibold text-ink">Rincian harian</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pesanan</th>
                        <th>Unit</th>
                        <th class="text-right">Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daily as $day)
                        <tr>
                            <td class="text-slate-700">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</td>
                            <td class="text-slate-600">{{ $day['orders'] }}</td>
                            <td class="text-slate-600">{{ $day['units'] }}</td>
                            <td class="text-right font-medium text-ink">{{ rupiah($day['revenue']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>{{ $summary['orders'] }}</td>
                        <td>{{ $summary['units'] }}</td>
                        <td class="text-right">{{ rupiah($summary['revenue']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
