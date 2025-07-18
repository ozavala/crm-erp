<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $primaryKey = 'warehouse_id';

    protected $fillable = [
        'name',
        'location',
        // 'address', // This field should be removed if using polymorphic addresses
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The products stocked in the warehouse.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'inventories', 'warehouse_id', 'product_id')->withPivot('quantity')->withTimestamps();
    }

    /**
     * Get all of the warehouse's addresses.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}