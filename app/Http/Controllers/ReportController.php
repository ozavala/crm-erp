<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesByPeriod(Request $request)
    {
        $period = $request->input('period', 'last_30_days');

        list($startDate, $endDate) = $this->getDateRange($period);

        $salesData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total_sales'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('reports.sales', compact('salesData', 'period'));
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'last_7_days':
                return [now()->subDays(6), now()];
            case 'last_30_days':
                return [now()->subDays(29), now()];
            case 'last_90_days':
                return [now()->subDays(89), now()];
            default:
                return [now()->subDays(29), now()];
        }
    }
}
