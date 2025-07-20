<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For aggregate queries

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Lead statistics
        $leadStatusCounts = Lead::select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status');

        $totalLeads = Lead::count();
        $activeLeads = Lead::whereNotIn('status', ['Won', 'Lost'])->count(); // Leads that are not yet closed

        // KPIs adicionales
        $salesThisMonth = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount');

        $pendingInvoices = Invoice::whereIn('status', ['Sent', 'Partially Paid', 'Overdue'])
            ->count();

        $openQuotations = Quotation::whereIn('status', ['Draft', 'Sent', 'Accepted'])
            ->count();

        // Gráfico de ventas por mes (últimos 6 meses)
        $salesData = Invoice::select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('invoice_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $salesMonths = $salesData->pluck('month')->toArray();
        $salesByMonth = $salesData->pluck('total')->toArray();

        // Recent leads (e.g., last 5 created)
        $recentLeads = Lead::with('assignedTo')
                           ->orderBy('created_at', 'desc')
                           ->take(5)
                           ->get();

        return view('dashboard', compact(
            'leadStatusCounts', 'totalLeads', 'activeLeads', 'recentLeads',
            'salesThisMonth', 'pendingInvoices', 'openQuotations', 'salesMonths', 'salesByMonth'
        ));
    }
}