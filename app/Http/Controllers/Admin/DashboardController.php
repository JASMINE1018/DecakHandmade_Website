<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SalesReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(SalesReportService $reports): View
    {
        return view('admin.dashboard', [
            'data' => $reports->dashboard(),
        ]);
    }
}
