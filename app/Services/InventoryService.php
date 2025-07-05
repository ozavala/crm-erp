<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Check if product has sufficient stock
     */
    public function hasSufficientStock(int $productId, int $quantity, int $warehouseId = null): bool
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return false;
        }

        if ($warehouseId) {
            $stock = DB::table('product_warehouse')
                ->where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->first();
            
            return $stock && $stock->quantity >= $quantity;
        }

        // Check total stock across all warehouses
        $totalStock = $product->warehouses()->sum('quantity');
        return $totalStock >= $quantity;
    }

    /**
     * Reserve stock for an order
     */
    public function reserveStock(int $productId, int $quantity, int $warehouseId): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $warehouseId) {
            $product = Product::find($productId);
            
            if (!$product) {
                return false;
            }

            $warehouseStock = DB::table('product_warehouse')
                ->where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->first();

            if (!$warehouseStock || $warehouseStock->quantity < $quantity) {
                return false;
            }

            // Update stock
            DB::table('product_warehouse')
                ->where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->update(['quantity' => $warehouseStock->quantity - $quantity]);

            return true;
        });
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts(int $threshold = 10): array
    {
        $alerts = [];

        $products = Product::with('warehouses')
            ->where('is_active', true)
            ->get();

        foreach ($products as $product) {
            $totalStock = $product->warehouses()->sum('quantity');
            
            if ($totalStock <= $threshold) {
                $alerts[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'current_stock' => $totalStock,
                    'threshold' => $threshold,
                ];
            }
        }

        return $alerts;
    }

    /**
     * Calculate reorder point
     */
    public function calculateReorderPoint(int $productId): int
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return 0;
        }

        // Simple reorder point calculation
        // In a real system, you'd use historical data for lead time and demand
        $averageDailyDemand = 5; // This would come from historical data
        $leadTimeDays = 7; // This would come from supplier data
        $safetyStock = 10; // Buffer stock

        return ($averageDailyDemand * $leadTimeDays) + $safetyStock;
    }

    /**
     * Get stock movement summary
     */
    public function getStockMovementSummary(int $productId, int $days = 30): array
    {
        // This would typically query a stock_movements table
        // For now, we'll return a mock structure
        return [
            'product_id' => $productId,
            'period_days' => $days,
            'total_in' => 0,
            'total_out' => 0,
            'net_movement' => 0,
            'average_daily_movement' => 0,
        ];
    }

    /**
     * Update product cost using weighted average
     */
    public function updateProductCost(int $productId, float $newCost, int $quantity): void
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return;
        }

        $oldQuantity = $product->quantity_on_hand;
        $oldCost = $product->cost;

        $totalQuantity = $oldQuantity + $quantity;
        $totalValue = ($oldQuantity * $oldCost) + ($quantity * $newCost);

        $newAverageCost = $totalQuantity > 0 ? $totalValue / $totalQuantity : $newCost;

        $product->update([
            'cost' => $newAverageCost,
            'quantity_on_hand' => $totalQuantity,
        ]);
    }
} 