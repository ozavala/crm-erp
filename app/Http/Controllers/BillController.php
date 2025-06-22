<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Http\Requests\StoreBillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    // Basic CRUD methods will go here.
    // For now, let's focus on creating a bill from a PO.

    public function index()
    {
        // Logic to list all bills
    }

    public function create(Request $request)
    {
        $purchaseOrder = null;
        if ($request->has('purchase_order_id')) {
            $purchaseOrder = PurchaseOrder::with('items.product', 'supplier')->findOrFail($request->input('purchase_order_id'));
        }

        $suppliers = Supplier::orderBy('name')->get();
        $bill = new Bill(); // For form model binding
        $statuses = Bill::$statuses;

        return view('bills.create', compact('bill', 'purchaseOrder', 'suppliers', 'statuses'));
    }

    public function store(StoreBillRequest $request)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData) {
            $billData = collect($validatedData)->except(['items'])->all();
            $billData['created_by_user_id'] = Auth::id();

            // Calculate totals
            $subtotal = 0;
            foreach ($validatedData['items'] as $item) {
                $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }

            $taxAmount = $validatedData['tax_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount;

            $billData['subtotal'] = $subtotal;
            $billData['total_amount'] = $totalAmount;
            $billData['status'] = 'Awaiting Payment'; // Default status
            $billData['amount_paid'] = 0;

            $bill = Bill::create($billData);

            foreach ($validatedData['items'] as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                $bill->items()->create($itemData);
            }

            return redirect()->route('bills.show', $bill->bill_id)
                             ->with('success', 'Bill created successfully.');
        });
    }

    public function show(Bill $bill)
    {
        $bill->load(['supplier', 'items.product', 'purchaseOrder', 'payments']);
        return view('bills.show', compact('bill'));
    }

    // ... other resource methods (edit, update, destroy)
}