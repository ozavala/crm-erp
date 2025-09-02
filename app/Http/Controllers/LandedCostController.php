<?php

namespace App\Http\Controllers;

use App\Models\LandedCost;
use App\Models\PurchaseOrder;
use App\Services\LandedCostService;
use Illuminate\Http\Request;

class LandedCostController extends Controller
{
    /**
     * Store a newly created landed cost for a purchase order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $purchaseOrder->landedCosts()->create($request->all());

        return redirect()->route('purchase-orders.show', $purchaseOrder)
                         ->with('success', 'Landed cost added successfully.');
    }

    /**
     * Remove the specified landed cost from storage.
     *
     * @param  \App\Models\LandedCost  $landedCost
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LandedCost $landedCost)
    {
        $purchaseOrderId = $landedCost->costable_id;
        $landedCost->delete();

        return redirect()->route('purchase-orders.show', $purchaseOrderId)
                         ->with('success', 'Landed cost removed successfully.');
    }

    /**
     * Apportion the landed costs across the purchase order items.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @param  \App\Services\LandedCostService  $landedCostService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apportion(PurchaseOrder $purchaseOrder, LandedCostService $landedCostService)
    {
        $landedCostService->apportionCosts($purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
                         ->with('success', 'Landed costs apportioned successfully across all items.');
    }
}

