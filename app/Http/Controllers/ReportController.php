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

    public function salesByProduct(Request $request)
    {
        $period = $request->input('period', 'last_30_days');

        list($startDate, $endDate) = $this->getDateRange($period);

        $productSalesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('products.name as product_name', DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_sales'))
            ->groupBy('products.name')
            ->orderByDesc('total_sales')
            ->get();

        return view('reports.sales-by-product', compact('productSalesData', 'period'));
    }

    public function salesByCustomer(Request $request)
    {
        $period = $request->input('period', 'last_30_days');

        list($startDate, $endDate) = $this->getDateRange($period);

        $customerSalesData = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('customers.company_name as customer_name', DB::raw('SUM(orders.total) as total_sales'))
            ->groupBy('customers.company_name')
            ->orderByDesc('total_sales')
            ->get();

        return view('reports.sales-by-customer', compact('customerSalesData', 'period'));
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
