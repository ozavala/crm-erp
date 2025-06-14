<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['order', 'customer'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer', fn($cq) =>
                        $cq->where('first_name', 'like', "%{$searchTerm}%")
                           ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    )
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%{$searchTerm}%"));
            });
        }
        if ($request->filled('status_filter')) {
            $query->where('status', $request->input('status_filter'));
        }

        $invoices = $query->paginate(10)->withQueryString();
        $statuses = Invoice::$statuses;
        return view('invoices.index', compact('invoices', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $statuses = Invoice::$statuses;
        // Fetch orders that are not yet fully invoiced or are in a state that allows invoicing
        // This logic might need to be more sophisticated based on your workflow
        $orders = Order::whereNotIn('status', ['Cancelled', 'Completed']) // Example filter
                       ->orderBy('order_number')
                       ->get();
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $selectedOrderId = $request->query('order_id');
        $invoice = new Invoice();
        $orderItems = [];

        if ($selectedOrderId) {
            $order = Order::with('items.product', 'customer')->find($selectedOrderId);
            if ($order) {
                $invoice->order_id = $order->order_id;
                $invoice->customer_id = $order->customer_id;
                // Pre-fill items from order
                foreach ($order->items as $orderItem) {
                    $orderItems[] = [
                        'product_id' => $orderItem->product_id,
                        'item_name' => $orderItem->item_name,
                        'item_description' => $orderItem->item_description,
                        'quantity' => $orderItem->quantity,
                        'unit_price' => $orderItem->unit_price,
                    ];
                }
                // Pre-fill discount and tax from order if applicable
                $invoice->discount_type = $order->discount_type;
                $invoice->discount_value = $order->discount_value;
                $invoice->tax_percentage = $order->tax_percentage;
            }
        }
        $invoice->invoice_number = 'INV-' . strtoupper(Str::random(8)); // Suggest an invoice number

        return view('invoices.create', compact('invoice', 'statuses', 'orders', 'customers', 'products', 'orderItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData, $request) {
            $invoiceData = collect($validatedData)->except(['items'])->all();
            $invoiceData['created_by_user_id'] = Auth::id();
            $invoiceData['amount_paid'] = 0; // Initially no amount paid

            $totals = $this->calculateTotals(
                $request->input('items', []),
                $request->input('discount_type'),
                $request->input('discount_value', 0),
                $request->input('tax_percentage', 0)
            );
            $invoiceData = array_merge($invoiceData, $totals);

            $invoice = Invoice::create($invoiceData);

            foreach ($request->input('items', []) as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                $invoice->items()->create($itemData);
            }

            return redirect()->route('invoices.index')
                             ->with('success', 'Invoice created successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order', 'customer', 'createdBy', 'items', 'items.product', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $statuses = Invoice::$statuses;
        $orders = Order::orderBy('order_number')->get();
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $invoice->load('items');
        $orderItems = $invoice->items->toArray(); // For the form structure

        return view('invoices.edit', compact('invoice', 'statuses', 'orders', 'customers', 'products', 'orderItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData, $request, $invoice) {
            $invoiceData = collect($validatedData)->except(['items'])->all();

            $totals = $this->calculateTotals(
                $request->input('items', []),
                $request->input('discount_type'),
                $request->input('discount_value', 0),
                $request->input('tax_percentage', 0)
            );
            $invoiceData = array_merge($invoiceData, $totals);

            $invoice->update($invoiceData);

            $existingItemIds = $invoice->items->pluck('invoice_item_id')->all();
            $newItemIds = [];

            foreach ($request->input('items', []) as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                if (isset($itemData['invoice_item_id']) && in_array($itemData['invoice_item_id'], $existingItemIds)) {
                    $item = $invoice->items()->find($itemData['invoice_item_id']);
                    $item->update($itemData);
                    $newItemIds[] = $item->invoice_item_id;
                } else {
                    $newItem = $invoice->items()->create($itemData);
                    $newItemIds[] = $newItem->invoice_item_id;
                }
            }
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                $invoice->items()->whereIn('invoice_item_id', $itemsToDelete)->delete();
            }

            // Recalculate invoice status if total amount changed and it affects paid status
            if ($invoice->amount_paid >= $invoice->total_amount && $invoice->total_amount > 0) {
                $invoice->status = 'Paid';
            } elseif ($invoice->amount_paid > 0 && $invoice->amount_paid < $invoice->total_amount) {
                $invoice->status = 'Partially Paid';
            } elseif ($invoice->amount_paid <= 0 && $invoice->status === 'Paid') { // If total reduced below paid amount
                $invoice->status = 'Sent'; // Or other appropriate status
            }
            $invoice->save();


            return redirect()->route('invoices.index')
                             ->with('success', 'Invoice updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return redirect()->route('invoices.index')
                             ->with('error', 'Cannot delete invoice with existing payments. Please delete payments first.');
        }
        $invoice->items()->delete();
        $invoice->delete();
        return redirect()->route('invoices.index')
                         ->with('success', 'Invoice deleted successfully.');
    }

    protected function calculateTotals(array $items, ?string $discountType, float $discountValue, float $taxPercentage): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
        }

        $discountAmount = 0.00;
        if ($discountValue > 0) {
            if ($discountType === 'percentage') {
                $discountAmount = ($subtotal * $discountValue) / 100;
            } elseif ($discountType === 'fixed') {
                $discountAmount = $discountValue;
            }
        }

        $subtotalAfterDiscount = $subtotal - $discountAmount;

        $taxAmount = 0.00;
        if ($taxPercentage > 0) {
            $taxAmount = ($subtotalAfterDiscount * $taxPercentage) / 100;
        }
        $totalAmount = $subtotalAfterDiscount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ];
    }
}