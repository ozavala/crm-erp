<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'purchase_order_id';

    protected $fillable = [
        'supplier_id',
        'shipping_address_id',
        'purchase_order_number',
        'order_date',
        'expected_delivery_date',
        'type', // e.g., Standard, Rush, Drop Ship
        'status',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'amount_paid', // Added
        'shipping_cost',
        'other_charges', // Any additional charges not covered by shipping or tax
        'terms_and_conditions',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2', // Added
    ];
    public static $types = [
        'Standard' => 'Standard',
        'Rush Order' => 'Rush Order',
        'Drop Ship' => 'Drop Ship',
    ];

    public static $statuses = [
        'Draft' => 'Draft',
        'Sent' => 'Sent',
        'Confirmed' => 'Confirmed', // Supplier confirmed
        'Partially Received' => 'Partially Received',
        'Received' => 'Received',
        'Completed' => 'Completed', // All items received and potentially billed
        'Cancelled' => 'Cancelled',
        'Partially Paid' => 'Partially Paid',
        'Paid' => 'Paid',
    ];
    

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'created_by_user_id', 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id', 'purchase_order_id');
    }

    /**
     * Get the shipping address for the purchase order.
     * This would typically be one of your company's addresses.
     * If you have a specific model for company addresses or use the generic Address model, adjust accordingly.
     */
    public function shippingAddress(): BelongsTo
    {
        // Assuming 'shipping_address_id' refers to an ID in the 'addresses' table
        // and these addresses might be linked to your company/warehouse rather than a supplier/customer.
        return $this->belongsTo(Address::class, 'shipping_address_id', 'address_id');
    }

    /**
     * Get all of the purchase order's payments.
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Get the amount due for the purchase order.
     */
    public function getAmountDueAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'purchase_order_id', 'purchase_order_id');
    }

    public function landedCosts(): MorphMany
    {
        return $this->morphMany(LandedCost::class, 'costable');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'purchase_order_id', 'purchase_order_id');
    }

    /**
     * Updates the status of the purchase order based on goods receipts.
     * Sets status to 'Partially Received' or 'Received'.
     */
    public function updateStatusAfterReceipt(): void
    {
        // Eager load the necessary relationships to avoid N+1 queries
        $this->load('items', 'goodsReceipts.items');

        // If there are no items on the PO, there's nothing to do.
        if ($this->items->isEmpty()) {
            return;
        }

        $isFullyReceived = true;
        $hasAnyReceipts = false;

        // Get a map of total received quantities for each PO item
        $receivedQuantitiesMap = $this->goodsReceipts
            ->flatMap->items
            ->groupBy('purchase_order_item_id')
            ->map->sum('quantity_received');

        foreach ($this->items as $item) {
            $totalReceivedForItem = $receivedQuantitiesMap->get($item->purchase_order_item_id, 0);

            if ($totalReceivedForItem > 0) {
                $hasAnyReceipts = true;
            }

            if ($totalReceivedForItem < $item->quantity) {
                $isFullyReceived = false;
            }
        }

        if ($hasAnyReceipts) {
            $this->status = $isFullyReceived ? 'Received' : 'Partially Received';
            $this->save();
        }
    }

    /**
     * Updates the status of the purchase order based on its payments.
     */
    public function updateStatusAfterPayment(): void
    {
        // Recalculate the amount paid from its payments
        $this->amount_paid = $this->payments()->sum('amount');

        // Update status based on the new amount_paid
        if ($this->amount_paid >= $this->total_amount) {
            $this->status = 'Paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'Partially Paid';
        }
        $this->save();
    }
}