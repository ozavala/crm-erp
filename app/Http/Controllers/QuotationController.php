<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Opportunity;
use App\Models\Product;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quotation::with(['opportunity', 'opportunity.customer'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('subject', 'like', "%{$searchTerm}%")
                  ->orWhereHas('opportunity', fn($oq) => $oq->where('name', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('opportunity.customer', fn($cq) => 
                        $cq->where('first_name', 'like', "%{$searchTerm}%")
                           ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    );
            });
        }
        if ($request->filled('status_filter')) {
            $query->where('status', $request->input('status_filter'));
        }

        $quotations = $query->paginate(10)->withQueryString();
        $statuses = Quotation::$statuses;
        return view('quotations.index', compact('quotations', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $statuses = Quotation::$statuses;
        $opportunities = Opportunity::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedOpportunityId = $request->query('opportunity_id');

        return view('quotations.create', compact('statuses', 'opportunities', 'products', 'selectedOpportunityId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuotationRequest $request)
    {
        $validatedData = $request->validated();
        
        return DB::transaction(function () use ($validatedData) {
            $quotationData = collect($validatedData)->except(['items'])->all();
            $quotationData['created_by_user_id'] = Auth::id();
            
            // Calculate totals before creating quotation
            $totals = $this->calculateTotals(
                $validatedData['items'] ?? [],
                $validatedData['discount_type'] ?? null,
                $validatedData['discount_value'] ?? 0,
                $validatedData['tax_percentage'] ?? 0
            );
            $quotationData = array_merge($quotationData, $totals);

            $quotation = Quotation::create($quotationData);

            // Create quotation items
            foreach ($validatedData['items'] ?? [] as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                $quotation->items()->create($itemData);
            }

            return redirect()->route('quotations.index')
                             ->with('success', 'Quotation created successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['opportunity', 'opportunity.customer', 'createdBy', 'items', 'items.product']);
        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        $statuses = Quotation::$statuses;
        $opportunities = Opportunity::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $quotation->load('items');
        return view('quotations.edit', compact('quotation', 'statuses', 'opportunities', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData, $quotation) {
            $quotationData = collect($validatedData)->except(['items'])->all();

            // Calculate totals before updating quotation
            $totals = $this->calculateTotals(
                $validatedData['items'] ?? [],
                $validatedData['discount_type'] ?? null,
                $validatedData['discount_value'] ?? 0,
                $validatedData['tax_percentage'] ?? 0
            );
            $quotationData = array_merge($quotationData, $totals);
            
            $quotation->update($quotationData);

            // Sync quotation items
            $existingItemIds = $quotation->items->pluck('quotation_item_id')->all();
            $newItemIds = [];

            foreach ($validatedData['items'] ?? [] as $itemData) {
                $itemData['item_total'] = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                if (isset($itemData['quotation_item_id']) && in_array($itemData['quotation_item_id'], $existingItemIds)) {
                    // Update existing item
                    $item = $quotation->items()->find($itemData['quotation_item_id']);
                    $item->update($itemData);
                    $newItemIds[] = $item->quotation_item_id;
                } else {
                    // Create new item
                    $newItem = $quotation->items()->create($itemData);
                    $newItemIds[] = $newItem->quotation_item_id;
                }
            }
            // Delete items that were removed from the form
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                $quotation->items()->whereIn('quotation_item_id', $itemsToDelete)->delete();
            }

            return redirect()->route('quotations.index')
                             ->with('success', 'Quotation updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        // Add checks if quotation is linked to invoices etc.
        $quotation->items()->delete(); // Delete related items first
        $quotation->delete();
        return redirect()->route('quotations.index')
                         ->with('success', 'Quotation deleted successfully.');
    }

    /**
     * Calculate subtotal, tax, discount, total for the quotation.
     * This is a basic calculation. You might need more complex logic for tax/discount.
     */
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