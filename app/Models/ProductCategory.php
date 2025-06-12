<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'description',
        'parent_category_id',
    ];

    /**
     * Get the parent category.
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_category_id', 'category_id');
    }

    /**
     * Get the child categories.
     */
    public function childCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_category_id', 'category_id');
    }

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id', 'category_id');
    }
}