<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'bill_item_id';

    protected $fillable = [
        'bill_id',
        'purchase_order_item_id',
        'product_id',
        'item_name',
        'item_description',
        'quantity',
        'unit_price',
        'item_total',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'bill_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id', 'purchase_order_item_id');
    }
}