<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LandedCost extends Model
{
    use HasFactory;

    protected $primaryKey = 'landed_cost_id';

    protected $fillable = [
        'costable_id',
        'costable_type',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function costable(): MorphTo
    {
        return $this->morphTo();
    }
}