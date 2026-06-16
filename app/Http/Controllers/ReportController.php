<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(ReportService $reportService)
    {
        $reportData = $reportService->generate();

        Log::info('Reportes consultados', [
            'user'              => auth()->user()->email,
            'total_licitaciones' => $reportData['totalLicitaciones'],
        ]);

        return view('reportes.index', $reportData);
    }
}
