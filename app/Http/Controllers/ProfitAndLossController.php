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

        // Ingresos: cuenta 'Ventas' (haber)
        $ingresos = JournalEntryLine::where('account_name', 'Ventas')
            ->whereBetween('created_at', [$from, $to])
            ->sum('credit_amount');

        // Costos: cuenta 'Inventario' (debe)
        $costos = JournalEntryLine::where('account_name', 'Inventario')
            ->whereBetween('created_at', [$from, $to])
            ->sum('debit_amount');

        // Gastos: si tienes cuentas de gastos, agrégalas aquí
        $gastos = JournalEntryLine::where('account_name', 'Gastos')
            ->whereBetween('created_at', [$from, $to])
            ->sum('debit_amount');

        $utilidadBruta = $ingresos - $costos;
        $utilidadNeta = $utilidadBruta - $gastos;

        return view('reports.profit_and_loss', compact(
            'from', 'to', 'ingresos', 'costos', 'gastos', 'utilidadBruta', 'utilidadNeta'
        ));
    }
} 