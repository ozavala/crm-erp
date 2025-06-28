<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'cost',
        'quantity_on_hand',
        'is_service',
        'is_active',
        'created_by_user_id',
        'product_category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'quantity_on_hand' => 'integer',
        'is_service' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this product/service.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'created_by_user_id', 'user_id');
    }

    public function getTypeNameAttribute(): string
    {
        return $this->is_service ? 'Service' : 'Product';
    }

    /**
     * The features that belong to the product.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(ProductFeature::class, 'product_product_feature', 'product_id', 'feature_id')->withPivot('value')->withTimestamps();
    }

    /**
     * The warehouses that stock the product.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse', 'product_id', 'warehouse_id')->withPivot('quantity')->withTimestamps();
    }

    /**
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'category_id');
    }

    /**
     * Updates inventory quantity and recalculates the weighted average cost.
     *
     * @param int $quantityReceived The number of units being added to stock.
     * @param float $landedCostPerUnit The full landed cost for each of those units.
     */
    public function receiveStock(int $quantityReceived, float $landedCostPerUnit): void
    {
        $oldQuantity = $this->quantity_on_hand;
        $oldAverageCost = $this->cost;

        $newStockValue = $quantityReceived * $landedCostPerUnit;
        $oldStockValue = $oldQuantity * $oldAverageCost;

        $totalQuantity = $oldQuantity + $quantityReceived;
        $totalValue = $oldStockValue + $newStockValue;

        $this->quantity_on_hand = $totalQuantity;
        $this->cost = ($totalQuantity > 0) ? $totalValue / $totalQuantity : 0;

        $this->save();
    }
}