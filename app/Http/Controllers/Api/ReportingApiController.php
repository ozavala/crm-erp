<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingApiController extends Controller
{
    /**
     * Sales report
     */
    public function salesReport(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());
        $groupBy = $request->get('group_by', 'day'); // day, week, month, year

        $query = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                     ->where('status', '!=', 'cancelled');

        $salesData = match($groupBy) {
            'day' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
                           ->groupBy('date')
                           ->orderBy('date')
                           ->get(),
            'week' => $query->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as orders, SUM(total_amount) as revenue')
                            ->groupBy('week')
                            ->orderBy('week')
                            ->get(),
            'month' => $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as orders, SUM(total_amount) as revenue')
                             ->groupBy('month')
                             ->orderBy('month')
                             ->get(),
            'year' => $query->selectRaw('YEAR(created_at) as year, COUNT(*) as orders, SUM(total_amount) as revenue')
                            ->groupBy('year')
                            ->orderBy('year')
                            ->get(),
        };

        $totalRevenue = $query->sum('total_amount');
        $totalOrders = $query->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'average_order_value' => $averageOrderValue,
                ],
                'sales_data' => $salesData,
            ]
        ]);
    }

    /**
     * Inventory report
     */
    public function inventoryReport(Request $request): JsonResponse
    {
        $lowStock = $request->get('low_stock', false);
        $categoryId = $request->get('category_id');

        $query = Product::with(['category', 'warehouses']);

        if ($lowStock) {
            $query->whereHas('warehouses', function ($q) {
                $q->whereRaw('quantity <= reorder_point');
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        $inventoryData = $products->map(function ($product) {
            $totalStock = $product->warehouses->sum('quantity');
            $totalValue = $totalStock * $product->price;
            
            return [
                'product' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category?->name,
                'total_stock' => $totalStock,
                'total_value' => $totalValue,
                'reorder_point' => $product->reorder_point,
                'low_stock' => $totalStock <= $product->reorder_point,
                'warehouses' => $product->warehouses->map(function ($warehouse) {
                    return [
                        'warehouse' => $warehouse->warehouse->name,
                        'quantity' => $warehouse->quantity,
                    ];
                }),
            ];
        });

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->warehouses->sum('quantity') * $product->price;
        });
        $lowStockCount = $products->where('low_stock', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_products' => $totalProducts,
                    'total_stock_value' => $totalStockValue,
                    'low_stock_count' => $lowStockCount,
                ],
                'inventory_data' => $inventoryData,
            ]
        ]);
    }

    /**
     * Cash flow report
     */
    public function cashFlowReport(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        // Ingresos (facturas pagadas)
        $income = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])
                        ->where('status', 'paid')
                        ->sum('total_amount');

        // Gastos (órdenes de compra pagadas)
        $expenses = PurchaseOrder::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->where('status', 'received')
                                ->sum('total_amount');

        // Facturas pendientes
        $pendingInvoices = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])
                                 ->whereIn('status', ['sent', 'overdue'])
                                 ->sum('total_amount');

        // Órdenes de compra pendientes
        $pendingExpenses = PurchaseOrder::whereBetween('created_at', [$dateFrom, $dateTo])
                                       ->whereIn('status', ['ordered', 'confirmed'])
                                       ->sum('total_amount');

        $netCashFlow = $income - $expenses;

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'cash_flow' => [
                    'income' => $income,
                    'expenses' => $expenses,
                    'net_cash_flow' => $netCashFlow,
                    'pending_income' => $pendingInvoices,
                    'pending_expenses' => $pendingExpenses,
                ],
            ]
        ]);
    }

    /**
     * Profitability report
     */
    public function profitabilityReport(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        // Ventas
        $sales = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                     ->where('status', '!=', 'cancelled')
                     ->sum('total_amount');

        // Costos (estimado basado en cost_price de productos)
        $costs = DB::table('order_items')
                   ->join('orders', 'order_items.order_id', '=', 'orders.id')
                   ->join('products', 'order_items.product_id', '=', 'products.id')
                   ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                   ->where('orders.status', '!=', 'cancelled')
                   ->sum(DB::raw('order_items.quantity * COALESCE(products.cost_price, 0)'));

        $grossProfit = $sales - $costs;
        $profitMargin = $sales > 0 ? ($grossProfit / $sales) * 100 : 0;

        // Productos más rentables
        $topProducts = DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->join('products', 'order_items.product_id', '=', 'products.id')
                        ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                        ->where('orders.status', '!=', 'cancelled')
                        ->selectRaw('products.name, SUM(order_items.quantity) as units_sold, SUM(order_items.total) as revenue, SUM(order_items.quantity * COALESCE(products.cost_price, 0)) as cost')
                        ->groupBy('products.id', 'products.name')
                        ->orderByDesc('revenue')
                        ->limit(10)
                        ->get()
                        ->map(function ($product) {
                            $profit = $product->revenue - $product->cost;
                            $margin = $product->revenue > 0 ? ($profit / $product->revenue) * 100 : 0;
                            
                            return [
                                'name' => $product->name,
                                'units_sold' => $product->units_sold,
                                'revenue' => $product->revenue,
                                'cost' => $product->cost,
                                'profit' => $profit,
                                'margin' => $margin,
                            ];
                        });

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'profitability' => [
                    'sales' => $sales,
                    'costs' => $costs,
                    'gross_profit' => $grossProfit,
                    'profit_margin' => $profitMargin,
                ],
                'top_products' => $topProducts,
            ]
        ]);
    }

    /**
     * Supplier performance report
     */
    public function supplierPerformanceReport(Request $request): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());

        $suppliers = Supplier::with(['purchaseOrders' => function ($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }])->get();

        $supplierData = $suppliers->map(function ($supplier) {
            $totalOrders = $supplier->purchaseOrders->count();
            $totalSpent = $supplier->purchaseOrders->sum('total_amount');
            $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
            
            // Calcular tiempo promedio de entrega
            $deliveryTimes = $supplier->purchaseOrders
                ->where('status', 'received')
                ->map(function ($po) {
                    return Carbon::parse($po->order_date)->diffInDays($po->expected_delivery_date);
                });

            $avgDeliveryTime = $deliveryTimes->count() > 0 ? $deliveryTimes->avg() : 0;

            return [
                'supplier' => $supplier->name,
                'total_orders' => $totalOrders,
                'total_spent' => $totalSpent,
                'average_order_value' => $averageOrderValue,
                'average_delivery_time' => $avgDeliveryTime,
                'on_time_deliveries' => $supplier->purchaseOrders->where('status', 'received')->count(),
            ];
        })->sortByDesc('total_spent');

        $totalSuppliers = $suppliers->count();
        $totalSpent = $suppliers->sum(function ($supplier) {
            return $supplier->purchaseOrders->sum('total_amount');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'summary' => [
                    'total_suppliers' => $totalSuppliers,
                    'total_spent' => $totalSpent,
                ],
                'supplier_performance' => $supplierData,
            ]
        ]);
    }
} 