<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product; // Added for product selection in edit view
use App\Http\Requests\UpdateBillRequest;
use App\Http\Requests\StoreBillRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BillController extends Controller
{
    // Basic CRUD methods will go here.
    // For now, let's focus on creating a bill from a PO.

    public function index()
    {
        $empresaActivaId = Session::get('owner_company_id');
        $bills = Bill::with(['supplier', 'purchaseOrder'])
            ->where('owner_company_id', $empresaActivaId)
            ->orderBy('bill_date', 'desc')
            ->paginate(15);

        return view('bills.index', compact('bills'));
    }

    public function create(Request $request)
    {
        $empresaActivaId = Session::get('owner_company_id');
        $purchaseOrder = null;
        if ($request->has('purchase_order_id')) {
            $purchaseOrder = PurchaseOrder::where('owner_company_id', $empresaActivaId)
                ->with('items.product', 'supplier')->findOrFail($request->input('purchase_order_id'));
        }

        $suppliers = Supplier::where('owner_company_id', $empresaActivaId)->orderBy('name')->get();
        $bill = new Bill(); // For form model binding
        $statuses = Bill::$statuses;

        return view('bills.create', compact('bill', 'purchaseOrder', 'suppliers', 'statuses'));
    }

    public function store(StoreBillRequest $request)
    {
        $empresaActivaId = Session::get('owner_company_id');
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData, $empresaActivaId) {
            $billData = collect($validatedData)->except(['items'])->all();
            $billData['created_by_user_id'] = Auth::id();
            $billData['owner_company_id'] = $empresaActivaId;

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

    public function edit(Bill $bill)
    {
        $bill->load(['items.product', 'supplier', 'purchaseOrder']);
        $suppliers = Supplier::orderBy('name')->get();
        $statuses = Bill::$statuses;
        $products = Product::orderBy('name')->get(); // For adding new items

        return view('bills.edit', compact('bill', 'suppliers', 'statuses', 'products'));
    }

    public function update(UpdateBillRequest $request, Bill $bill)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData, $bill) {
            $billData = collect($validatedData)->except(['items'])->all();

            // Recalculate totals based on current items
            $subtotal = 0;
            foreach ($validatedData['items'] as $item) {
                $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }

            $taxAmount = $validatedData['tax_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount;

            $billData['subtotal'] = $subtotal;
            $billData['total_amount'] = $totalAmount;
            // Status and amount_paid are updated by PaymentController, but can be manually adjusted here if needed

            $bill->update($billData);

            $existingItemIds = $bill->items()->withTrashed()->pluck('bill_item_id')->toArray();
            $updatedItemIds = [];

            foreach ($validatedData['items'] as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);

                if (isset($itemData['bill_item_id']) && $itemData['bill_item_id']) {
                    // Update existing item
                    $billItem = $bill->items()->find($itemData['bill_item_id']);
                    if ($billItem) {
                        $billItem->update($itemData);
                        $updatedItemIds[] = $billItem->bill_item_id;
                    }
                } else {
                    // Create new item
                    $newItem = $bill->items()->create($itemData);
                    $updatedItemIds[] = $newItem->bill_item_id;
                }
            }

            // Delete items that were removed from the form
           // This line correctly soft-deletes any items associated with the bill
            // that are NOT in the list of updated/new items.
            $bill->items()->whereIn('bill_item_id', array_diff($existingItemIds, $updatedItemIds))->delete();

            return redirect()->route('bills.show', $bill->bill_id)
                             ->with('success', 'Bill updated successfully.');
        });
    }

    // ... other resource methods (destroy)

    /**
     * Generate PDF for the bill
     */
    public function printPdf(Bill $bill)
    {
        $bill->load(['supplier.addresses', 'items.product']);
        
        // Get company logo if available
        $logoData = null;
        $companyLogo = \App\Models\Setting::where('key', 'company_logo')->first();
        if ($companyLogo && $companyLogo->value) {
            $logoData = 'data:image/png;base64,' . $companyLogo->value;
        }
        
        $pdf = \PDF::loadView('bills.pdf', compact('bill', 'logoData'));
        
        return $pdf->stream("bill-{$bill->bill_number}.pdf");
    }
}