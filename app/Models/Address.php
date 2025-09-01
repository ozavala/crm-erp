<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $primaryKey = 'address_id';

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'address_type',
        'street_address_line_1',
        'street_address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country_code',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the parent addressable model (Customer, CrmUser, etc.).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the name of the owner of the address.
     *
     * @return string
     */
    public function getOwnerNameAttribute(): string
    {
        if (!$this->addressable) {
            return 'N/A';
        }

        switch ($this->addressable_type) {
            case Customer::class:
                return $this->addressable->company_name ?? ($this->addressable->first_name . ' ' . $this->addressable->last_name);
            case Supplier::class:
                return $this->addressable->name;
            default:
                return 'N/A';
        }
    }
}