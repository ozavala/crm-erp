<?php

namespace App\Http\Controllers;

use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class ProfitAndLossController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->endOfMonth()->toDateString());

        // Ingresos: todas las cuentas tipo 'Ingreso'
        $ingresosDetalle = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'Ingreso');
        })
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('account_code, SUM(credit_amount) as total')
        ->groupBy('account_code')
        ->get();
        $ingresos = $ingresosDetalle->sum('total');

        // Costos: todas las cuentas tipo 'Gasto' cuyo nombre sea "Costo de ventas" o "Compras"
        $costosDetalle = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'Gasto')->whereIn('name', ['Costo de ventas', 'Compras']);
        })
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('account_code, SUM(debit_amount) as total')
        ->groupBy('account_code')
        ->get();
        $costos = $costosDetalle->sum('total');

        // Gastos: todas las cuentas tipo 'Gasto' excepto "Costo de ventas" y "Compras"
        $gastosDetalle = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'Gasto')->whereNotIn('name', ['Costo de ventas', 'Compras']);
        })
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('account_code, SUM(debit_amount) as total')
        ->groupBy('account_code')
        ->get();
        $gastos = $gastosDetalle->sum('total');

        $utilidadBruta = $ingresos - $costos;
        $utilidadNeta = $utilidadBruta - $gastos;

        return view('reports.profit_and_loss', compact(
            'from', 'to',
            'ingresos', 'costos', 'gastos',
            'utilidadBruta', 'utilidadNeta',
            'ingresosDetalle', 'costosDetalle', 'gastosDetalle'
        ));
    }
} 