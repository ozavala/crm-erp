<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    public function getSalesReport(Carbon $startDate, Carbon $endDate): array
    {
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'items.product'])
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalOrders = $sales->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Top productos vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Top clientes
        $topCustomers = $sales->groupBy('customer_id')
            ->map(function ($orders, $customerId) {
                $customer = $orders->first()->customer;
                return [
                    'customer' => $customer,
                    'total_orders' => $orders->count(),
                    'total_revenue' => $orders->sum('total_amount'),
                    'average_order_value' => $orders->avg('total_amount')
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'summary' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order_value' => $averageOrderValue
            ],
            'top_products' => $topProducts,
            'top_customers' => $topCustomers->values()
        ];
    }

    public function getInventoryReport(): array
    {
        $products = Product::with(['warehouses', 'category'])
            ->get();

        $lowStockProducts = $products->filter(function ($product) {
            return $product->warehouses->some(function ($warehouse) use ($product) {
                return $warehouse->pivot->quantity <= ($product->reorder_point ?? 10);
            });
        });

        $outOfStockProducts = $products->filter(function ($product) {
            return $product->warehouses->sum('pivot.quantity') == 0;
        });

        $totalInventoryValue = $products->sum(function ($product) {
            return $product->warehouses->sum(function ($warehouse) use ($product) {
                return $warehouse->pivot->quantity * $product->cost_price;
            });
        });

        return [
            'total_products' => $products->count(),
            'low_stock_products' => $lowStockProducts->count(),
            'out_of_stock_products' => $outOfStockProducts->count(),
            'total_inventory_value' => $totalInventoryValue,
            'low_stock_list' => $lowStockProducts->take(20),
            'out_of_stock_list' => $outOfStockProducts->take(20)
        ];
    }

    public function getCashFlowReport(Carbon $startDate, Carbon $endDate): array
    {
        // Ingresos (pagos recibidos)
        $incomingPayments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->whereHasMorph('payable', [Invoice::class])
            ->sum('amount');

        // Gastos (pagos a proveedores)
        $outgoingPayments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->whereHasMorph('payable', [PurchaseOrder::class])
            ->sum('amount');

        // Facturas pendientes
        $pendingInvoices = Invoice::where('status', '!=', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Órdenes de compra pendientes
        $pendingPurchaseOrders = PurchaseOrder::where('status', '!=', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'cash_flow' => [
                'incoming' => $incomingPayments,
                'outgoing' => $outgoingPayments,
                'net_cash_flow' => $incomingPayments - $outgoingPayments
            ],
            'pending' => [
                'invoices' => $pendingInvoices,
                'purchase_orders' => $pendingPurchaseOrders
            ]
        ];
    }

    public function getProfitabilityReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product'])
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalCost = 0;
        $totalProfit = 0;

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $cost = $item->quantity * $item->product->cost_price;
                $revenue = $item->quantity * $item->unit_price;
                $totalCost += $cost;
                $totalProfit += ($revenue - $cost);
            }
        }

        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin_percentage' => $profitMargin
            ]
        ];
    }

    public function getSupplierPerformanceReport(Carbon $startDate, Carbon $endDate): array
    {
        $purchaseOrders = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])
            ->with(['supplier', 'items'])
            ->get();

        $supplierStats = $purchaseOrders->groupBy('supplier_id')
            ->map(function ($orders, $supplierId) {
                $supplier = $orders->first()->supplier;
                $totalOrders = $orders->count();
                $totalSpent = $orders->sum('total_amount');
                $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

                // Calcular tiempo promedio de entrega
                $deliveryTimes = $orders->filter(function ($order) {
                    return $order->dispatched_at && $order->created_at;
                })->map(function ($order) {
                    return $order->dispatched_at->diffInDays($order->created_at);
                });

                $averageDeliveryTime = $deliveryTimes->count() > 0 ? $deliveryTimes->avg() : null;

                return [
                    'supplier' => $supplier,
                    'total_orders' => $totalOrders,
                    'total_spent' => $totalSpent,
                    'average_order_value' => $averageOrderValue,
                    'average_delivery_time' => $averageDeliveryTime
                ];
            })
            ->sortByDesc('total_spent');

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'suppliers' => $supplierStats->values()
        ];
    }
} 