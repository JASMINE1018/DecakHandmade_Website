<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Penjualan {{ $start }} - {{ $end }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2c2420; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 20px 0 8px; }
        .muted { color: #7a6f68; }
        .header { border-bottom: 2px solid #a8543b; padding-bottom: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e0ddd8; padding: 6px 8px; text-align: left; }
        th { background: #f5e3c4; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .summary td { border: none; padding: 4px 0; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .chart { margin-top: 8px; width: 100%; }
        .footer { margin-top: 24px; font-size: 9px; color: #a99e96; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Penjualan — {{ \App\Models\Setting::get('store_name', 'Decak Handmade') }}</h1>
        <p class="muted">Periode {{ \Carbon\Carbon::parse($start)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($end)->format('d M Y') }} (WIB)</p>
        <p class="muted">Penjualan diakui dari pesanan berstatus Selesai berdasarkan tanggal selesai. Nilai penjualan adalah subtotal produk tanpa ongkos kirim.</p>
    </div>

    <h2>Ringkasan</h2>
    <table class="summary">
        <tr><td>Total penjualan (subtotal)</td><td>{{ rupiah($summary['revenue']) }}</td></tr>
        <tr><td>Pesanan selesai</td><td>{{ $summary['orders'] }}</td></tr>
        <tr><td>Unit terjual</td><td>{{ $summary['units'] }}</td></tr>
        <tr><td>Rata-rata nilai pesanan</td><td>{{ rupiah($summary['average']) }}</td></tr>
        <tr><td>Pelanggan baru</td><td>{{ $summary['new_customers'] }}</td></tr>
    </table>

    <h2>Grafik Penjualan Harian</h2>
    <img class="chart" src="data:image/png;base64,{{ $chart }}" alt="Grafik penjualan harian">

    <h2>Rincian Harian</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-right">Pesanan</th>
                <th class="text-right">Unit</th>
                <th class="text-right">Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daily as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</td>
                    <td class="text-right">{{ $day['orders'] }}</td>
                    <td class="text-right">{{ $day['units'] }}</td>
                    <td class="text-right">{{ rupiah($day['revenue']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th class="text-right">{{ $summary['orders'] }}</th>
                <th class="text-right">{{ $summary['units'] }}</th>
                <th class="text-right">{{ rupiah($summary['revenue']) }}</th>
            </tr>
        </tfoot>
    </table>

    <h2>Produk Terlaris</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th class="text-right">Unit</th>
                <th class="text-right">Pesanan</th>
                <th class="text-right">Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td class="text-right">{{ $product->units }}</td>
                    <td class="text-right">{{ $product->orders }}</td>
                    <td class="text-right">{{ rupiah($product->revenue) }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada penjualan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak {{ now('Asia/Jakarta')->format('d M Y H:i') }} WIB · Decak Handmade Admin Panel</p>
</body>
</html>
