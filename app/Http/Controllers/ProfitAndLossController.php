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

        $user = auth()->user();
        $companyFilter = function ($query) use ($user) {
            if (!$user->is_super_admin) {
                $query->where('owner_company_id', $user->owner_company_id);
            }
        };

        // Ingresos: todas las cuentas tipo 'Ingreso'
        $ingresosDetalle = JournalEntryLine::whereHas('account', function($q) use ($companyFilter) {
                $q->where('type', 'income');
                $companyFilter($q);
            })
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('account_code, SUM(credit_amount) as total')
            ->groupBy('account_code')
            ->get();
        $ingresos = $ingresosDetalle->sum('total');

        // Costos: todas las cuentas tipo 'Gasto' cuyo nombre sea "Costo de ventas" o "Compras"
        $costosDetalle = JournalEntryLine::whereHas('account', function($q) use ($companyFilter) {
                $q->where('type', 'expense')->whereIn('name', ['Costo de ventas', 'Compras']);
                $companyFilter($q);
            })
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('account_code, SUM(debit_amount) as total')
            ->groupBy('account_code')
            ->get();
        $costos = $costosDetalle->sum('total');

        // Gastos: todas las cuentas tipo 'Gasto' excepto "Costo de ventas" y "Compras"
        $gastosDetalle = JournalEntryLine::whereHas('account', function($q) use ($companyFilter) {
                $q->where('type', 'expense')->whereNotIn('name', ['Costo de ventas', 'Compras']);
                $companyFilter($q);
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