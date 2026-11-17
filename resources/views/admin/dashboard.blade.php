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

            @php
                $count = $daily->count();
                $maxRevenue = max(1, $daily->max('revenue'));
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
                        <linearGradient id="dashLineGrad" x1="0" y1="0" x2="0" y2="1">
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
                        <path d="{{ $areaPath }}" fill="url(#dashLineGrad)"/>
                        <!-- Line -->
                        <polyline points="{{ $linePoints }}" fill="none" stroke="#c86d51" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    @else
                        <!-- Flat zero line -->
                        <line x1="{{ $firstX }}" y1="{{ $baseY }}" x2="{{ $lastX }}" y2="{{ $baseY }}" stroke="#c86d51" stroke-width="2.5" stroke-linecap="round"/>
                    @endif

                    <!-- Points & Labels -->
                    @foreach ($coords as $pt)
                        <g class="group">
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" stroke="#c86d51" stroke-width="2.5" class="cursor-pointer transition-all hover:r-7">
                                <title>{{ rupiah($pt['day']['revenue']) }} ({{ $pt['day']['orders'] }} pesanan) · {{ \Carbon\Carbon::parse($pt['day']['date'])->format('d M Y') }}</title>
                            </circle>
                            <text x="{{ $pt['x'] }}" y="{{ $svgH - 12 }}" text-anchor="middle" font-size="12" fill="#7a6f68" font-weight="500">
                                {{ \Carbon\Carbon::parse($pt['day']['date'])->format('d/m') }}
                            </text>
                        </g>
                    @endforeach
                </svg>
            </div>

            @unless ($hasSales)
                <p class="mt-2 text-center text-xs text-muted">Belum ada transaksi berstatus Selesai pada periode 7 hari terakhir.</p>
            @endunless
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
        <div class="mt-3 grid gap-2 sm:grid-cols-3">
            <a href="{{ route('admin.products.create') }}" class="admin-btn-primary w-full">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah produk baru
            </a>
            <a href="{{ route('admin.orders.create') }}" class="admin-btn-primary w-full">
                <x-icon name="orders" class="h-4 w-4" />
                Catat pesanan
            </a>
            <a href="{{ route('admin.reports.sales.index') }}" class="admin-btn-primary w-full">
                <x-icon name="reports" class="h-4 w-4" />
                Cetak laporan
            </a>
        </div>
    </div>
@endsection
