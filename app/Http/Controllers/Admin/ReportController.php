<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ChartRenderer;
use App\Services\SalesReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function sales(Request $request, SalesReportService $reports): View
    {
        [$start, $end] = $this->period($request);

        return view('admin.reports.sales', [
            'start' => $start,
            'end' => $end,
            'summary' => $reports->summary($start, $end),
            'daily' => $reports->daily($start, $end),
            'topProducts' => $reports->topProducts($start, $end),
        ]);
    }

    public function products(Request $request, SalesReportService $reports): View
    {
        [$start, $end] = $this->period($request);

        return view('admin.reports.products', [
            'start' => $start,
            'end' => $end,
            'summary' => $reports->summary($start, $end),
            'topProducts' => $reports->topProducts($start, $end, 50),
        ]);
    }

    public function export(Request $request, string $format, SalesReportService $reports, ChartRenderer $charts)
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);

        [$start, $end] = $this->period($request);

        $summary = $reports->summary($start, $end);
        $daily = $reports->daily($start, $end);
        $topProducts = $reports->topProducts($start, $end);
        $filename = "rekap-penjualan_{$start}_{$end}";

        app(\App\Services\AuditLogService::class)->record('report.exported', null, null, [
            'format' => $format,
            'start' => $start,
            'end' => $end,
        ], $request->user('admin'));

        if ($format === 'pdf') {
            $chart = base64_encode($charts->lineChart($daily->toArray()));

            return Pdf::loadView('admin.reports.pdf', compact('start', 'end', 'summary', 'daily', 'topProducts', 'chart'))
                ->setPaper('a4')
                ->download($filename.'.pdf');
        }

        return $this->xlsx($filename, $start, $end, $summary, $daily, $topProducts);
    }

    private function period(Request $request): array
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $data['start_date'] ?? now('Asia/Jakarta')->subDays(6)->toDateString();
        $end = $data['end_date'] ?? now('Asia/Jakarta')->toDateString();

        return [$start, $end];
    }

    private function xlsx(string $filename, string $start, string $end, array $summary, $daily, $topProducts)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');
        $sheet->fromArray([
            ['Rekap Penjualan Decak Handmade'],
            ['Periode', $start.' s.d. '.$end],
            ['Dibuat', now('Asia/Jakarta')->format('Y-m-d H:i').' WIB'],
            [],
            ['Total penjualan (subtotal, tanpa ongkir)', $summary['revenue']],
            ['Pesanan selesai', $summary['orders']],
            ['Unit terjual', $summary['units']],
            ['Rata-rata nilai pesanan', $summary['average']],
            ['Pelanggan baru', $summary['new_customers']],
        ]);
        $sheet->getStyle('B5:B8')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getColumnDimension('A')->setWidth(42);
        $sheet->getColumnDimension('B')->setWidth(22);

        $dailySheet = $spreadsheet->createSheet();
        $dailySheet->setTitle('Penjualan Harian');
        $dailySheet->fromArray([['Tanggal', 'Penjualan', 'Jumlah Pesanan', 'Unit']]);

        $row = 2;
        foreach ($daily as $day) {
            $dailySheet->fromArray([[$day['date'], $day['revenue'], $day['orders'], $day['units']]], null, 'A'.$row);
            $row++;
        }
        $dailySheet->getStyle('B2:B'.$row)->getNumberFormat()->setFormatCode('#,##0');

        $productSheet = $spreadsheet->createSheet();
        $productSheet->setTitle('Produk Terlaris');
        $productSheet->fromArray([['Produk', 'Unit Terjual', 'Penjualan', 'Jumlah Pesanan']]);

        $row = 2;
        foreach ($topProducts as $product) {
            $productSheet->fromArray([[$product->product_name, $product->units, $product->revenue, $product->orders]], null, 'A'.$row);
            $row++;
        }
        $productSheet->getStyle('C2:C'.$row)->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->setActiveSheetIndex(0);

        ob_start();
        (new Xlsx($spreadsheet))->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.xlsx"',
        ]);
    }
}
