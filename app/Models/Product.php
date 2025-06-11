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
}