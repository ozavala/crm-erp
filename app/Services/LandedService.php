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
            $itemValue = $item->item_total;
            
            // Calculate the proportion of the total value this item represents
            $valueProportion = $itemValue / $poSubtotal;
            
            // Apportion the landed costs to this item line
            $apportionedCost = $totalLandedCosts * $valueProportion;
            
            // Calculate the final total cost for all units of this item and then the cost per unit
            $finalItemCost = $itemValue + $apportionedCost;
            $landedCostPerUnit = ($item->quantity > 0) ? ($finalItemCost / $item->quantity) : 0;

            $item->update(['landed_cost_per_unit' => $landedCostPerUnit]);
        }
    }
}

