<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\GoodsReceipt;

class GoodsReceiptController extends Controller
{
    /**
     * Show the form for creating a new goods receipt for a purchase order.
     *
     * @param \App\Models\PurchaseOrder $purchaseOrder
     * @return \Illuminate\View\View
     */
    public function create(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product', 'items.goodsReceiptItems']);

        // Calculate remaining quantities for each item
        foreach ($purchaseOrder->items as $item) {
            $totalReceived = $item->goodsReceiptItems->sum('quantity_received');
            $item->quantity_remaining = $item->quantity - $totalReceived;
        }

        // Filter out items that are fully received
        $itemsToReceive = $purchaseOrder->items->filter(function ($item) {
            return $item->quantity_remaining > 0;
        });

        if ($itemsToReceive->isEmpty()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                             ->with('info', 'All items for this purchase order have already been fully received.');
        }

        return view('goods-receipts.create', compact('purchaseOrder', 'itemsToReceive'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GoodsReceipt  $goodsReceipt
     * @return \Illuminate\View\View
     */
    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['purchaseOrder', 'items.product', 'receivedBy']);
        return view('goods-receipts.show', compact('goodsReceipt'));
    }

    /**
     * Store a newly created goods receipt in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PurchaseOrder $purchaseOrder
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => ['required', Rule::exists('purchase_order_items', 'purchase_order_item_id')->where('purchase_order_id', $purchaseOrder->purchase_order_id)],
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $goodsReceipt = $purchaseOrder->goodsReceipts()->create([
                'receipt_date' => $validated['receipt_date'],
                'notes' => $validated['notes'],
                'received_by_user_id' => Auth::id(),
            ]);

            foreach ($validated['items'] as $receivedItemData) {
                if ($receivedItemData['quantity_received'] <= 0) {
                    continue;
                }

                $poItem = $purchaseOrder->items()->find($receivedItemData['purchase_order_item_id']);
                if (!$poItem) continue;

                $goodsReceipt->items()->create([
                    'purchase_order_item_id' => $poItem->purchase_order_item_id,
                    'product_id' => $poItem->product_id,
                    'quantity_received' => $receivedItemData['quantity_received'],
                ]);

                $product = $poItem->product;
                if ($product) {
                    $costPerUnit = $poItem->landed_cost_per_unit ?? $poItem->unit_price;
                    $product->receiveStock($receivedItemData['quantity_received'], $costPerUnit);
                }
            }
        });

        // After the transaction, update the PO status based on all receipts
        $purchaseOrder->updateStatusAfterReceipt();

        return redirect()->route('purchase-orders.show', $purchaseOrder)
                         ->with('success', 'Goods receipt created and inventory updated successfully.');
    }
}
