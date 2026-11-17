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
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-ink">Grafik penjualan harian</h2>
                    <p class="text-xs text-muted">{{ $start }} s.d. {{ $end }}</p>
                </div>
                @if ($maxRevenue > 1)
                    <span class="text-xs font-medium text-muted">Puncak: <strong class="text-ink">{{ rupiah($maxRevenue) }}</strong></span>
                @endif
            </div>

            @php
                $count = $daily->count();
                $hasSales = $daily->sum('revenue') > 0;

                $svgW = 900;
                $svgH = 220;
                $padX = 45;
                $padTop = 25;
                $padBottom = 35;
                $chartW = $svgW - ($padX * 2);
                $chartH = $svgH - $padTop - $padBottom;
                $baseY = $svgH - $padBottom;
                $step = $count > 1 ? $chartW / ($count - 1) : 0;

                $coords = [];
                foreach ($daily->values() as $i => $day) {
                    $x = (int) round($padX + ($step * $i));
                    $ratio = $hasSales ? ($day['revenue'] / $maxRevenue) : 0;
                    $y = (int) round($baseY - ($ratio * $chartH));
                    $coords[] = [
                        'x' => $x,
                        'y' => $y,
                        'day' => $day,
                    ];
                }

                $linePoints = collect($coords)->map(fn ($c) => "{$c['x']},{$c['y']}")->implode(' ');
                $firstX = $coords[0]['x'] ?? $padX;
                $lastX = end($coords)['x'] ?? ($svgW - $padX);
                $areaPath = "M {$firstX},{$baseY} ".collect($coords)->map(fn ($c) => "L {$c['x']},{$c['y']}")->implode(' ')." L {$lastX},{$baseY} Z";
            @endphp

            <div class="mt-2 w-full overflow-hidden">
                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" class="h-auto w-full" preserveAspectRatio="xMidYMid meet">
                    <defs>
                        <linearGradient id="salesLineGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#c86d51" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#c86d51" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>

                    <!-- Horizontal Gridlines -->
                    @for ($g = 0; $g <= 3; $g++)
                        @php
                            $gy = (int) round($padTop + ($chartH * $g / 3));
                        @endphp
                        <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $svgW - $padX }}" y2="{{ $gy }}" stroke="#e0ddd8" stroke-dasharray="4 4" stroke-width="1"/>
                    @endfor
                    <!-- Baseline -->
                    <line x1="{{ $padX }}" y1="{{ $baseY }}" x2="{{ $svgW - $padX }}" y2="{{ $baseY }}" stroke="#d3c9c1" stroke-width="1.5"/>

                    @if ($hasSales)
                        <!-- Area Fill -->
                        <path d="{{ $areaPath }}" fill="url(#salesLineGrad)"/>
                        <!-- Line -->
                        <polyline points="{{ $linePoints }}" fill="none" stroke="#c86d51" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    @else
                        <!-- Flat zero line -->
                        <line x1="{{ $firstX }}" y1="{{ $baseY }}" x2="{{ $lastX }}" y2="{{ $baseY }}" stroke="#c86d51" stroke-width="2.5" stroke-linecap="round"/>
                    @endif

                    <!-- Points & Labels -->
                    @foreach ($coords as $pt)
                        @php
                            $showLabel = $count <= 14 || $loop->index % (int) ceil($count / 10) === 0;
                        @endphp
                        <g class="group">
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" stroke="#c86d51" stroke-width="2.5" class="cursor-pointer transition-all hover:r-7">
                                <title>{{ rupiah($pt['day']['revenue']) }} ({{ $pt['day']['orders'] }} pesanan) · {{ \Carbon\Carbon::parse($pt['day']['date'])->format('d M Y') }}</title>
                            </circle>
                            @if ($showLabel)
                                <text x="{{ $pt['x'] }}" y="{{ $svgH - 12 }}" text-anchor="middle" font-size="12" fill="#7a6f68" font-weight="500">
                                    {{ \Carbon\Carbon::parse($pt['day']['date'])->format('d/m') }}
                                </text>
                            @endif
                        </g>
                    @endforeach
                </svg>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-line/60 pt-3 text-xs text-muted">
                <span>Penjualan dihitung dari subtotal produk pada pesanan selesai.</span>
                @unless ($hasSales)
                    <span class="font-medium text-amber-600">Belum ada pesanan selesai pada rentang tanggal ini.</span>
                @endunless
            </div>
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
