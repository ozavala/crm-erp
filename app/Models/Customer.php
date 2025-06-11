<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'company_name',
        'address_street',
        'address_city',
        'address_state',
        'address_postal_code',
        'address_country',
        'status', // Example: Active, Inactive, Lead
        'notes',
        'created_by_user_id',
    ];

    /**
     * Get the full name of the customer.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user who created this customer.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'created_by_user_id', 'user_id');
    }
}