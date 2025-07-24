<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaxRecoveryService;
use Illuminate\Support\Facades\Validator;
use App\Exports\IvaMensualExport;
use App\Exports\IvaAnualExport;
use Maatwebsite\Excel\Facades\Excel;

class TaxReportController extends Controller
{
    protected TaxRecoveryService $taxRecoveryService;

    public function __construct(TaxRecoveryService $taxRecoveryService)
    {
        $this->taxRecoveryService = $taxRecoveryService;
    }

    /**
     * Reporte mensual de IVA
     */
    public function monthly(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ])->sometimes('year', 'nullable', fn() => !$request->has('year'))
          ->sometimes('month', 'nullable', fn() => !$request->has('month'))
          ->validate();

        $report = null;
        if ($request->has(['year', 'month'])) {
            $report = $this->taxRecoveryService->generateMonthlyReport($request->input('year'), $request->input('month'));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($report);
        }
        return view('reports.iva.mensual', compact('report'));
    }

    /**
     * Reporte anual de IVA
     */
    public function annual(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
        ])->sometimes('year', 'nullable', fn() => !$request->has('year'))
          ->validate();

        $summary = null;
        if ($request->has('year')) {
            $year = $request->input('year');
            $summary = [];
            for ($month = 1; $month <= 12; $month++) {
                $summary[$month-1] = $this->taxRecoveryService->generateMonthlyReport($year, $month);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($summary);
        }
        return view('reports.iva.anual', compact('summary'));
    }

    /**
     * Reporte personalizado de IVA por rango de fechas
     */
    public function custom(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ])->validate();

        $report = $this->taxRecoveryService->getTaxSummary($validated['start_date'], $validated['end_date']);
        return response()->json($report);
    }

    /**
     * Estadísticas rápidas para dashboard
     */
    public function dashboard()
    {
        $stats = $this->taxRecoveryService->getTaxStatistics();
        return response()->json($stats);
    }
    
    /**
     * Export monthly report to Excel
     */
    public function exportMonthlyExcel(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ])->validate();
        
        $year = $request->input('year');
        $month = $request->input('month');
        $report = $this->taxRecoveryService->generateMonthlyReport($year, $month);
        
        return Excel::download(
            new IvaMensualExport($report, $year, $month),
            'iva_mensual_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }
    
    /**
     * Export annual report to Excel
     */
    public function exportAnnualExcel(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
        ])->validate();
        
        $year = $request->input('year');
        $summary = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $summary[$month-1] = $this->taxRecoveryService->generateMonthlyReport($year, $month);
        }
        
        return Excel::download(
            new IvaAnualExport($summary, $year),
            'iva_anual_' . $year . '.xlsx'
        );
    }
}
