<?php

namespace App\Services;

use App\Models\PurchaseOrder;

class LandedCostService
{
    /**
     * Apportions total landed costs across all items in a purchase order
     * and updates the landed_cost_per_unit for each item.
     *
     * @param \App\Models\PurchaseOrder $purchaseOrder
     * @return void
     */
    public function apportionCosts(PurchaseOrder $purchaseOrder): void
    {
        $totalLandedCosts = $purchaseOrder->landedCosts()->sum('amount');
        $poSubtotal = $purchaseOrder->items()->sum('item_total');

        if ($poSubtotal <= 0) {
            // Avoid division by zero if the PO has no value or items.
            return;
        }

        foreach ($purchaseOrder->items as $item) {
            // Using bcmath for high-precision calculations to avoid floating-point inaccuracies
            $itemValue = (string) $item->item_total;
            $poSubtotalStr = (string) $poSubtotal;
            $totalLandedCostsStr = (string) $totalLandedCosts;
            $itemQuantityStr = (string) $item->quantity;

            // Calculate the proportion of the total value this item represents
            $valueProportion = bcdiv($itemValue, $poSubtotalStr, 10); // 10 decimal places for precision

            // Apportion the landed costs to this item line
            $apportionedCost = bcmul($totalLandedCostsStr, $valueProportion, 10);

            // Calculate the landed cost per unit for this item line
            $landedCostPerUnit = ($item->quantity > 0) ? bcdiv($apportionedCost, $itemQuantityStr, 4) : '0.0000'; // 4 decimal places for unit cost

            $item->update(['landed_cost_per_unit' => $landedCostPerUnit]);
        }
    }
}
