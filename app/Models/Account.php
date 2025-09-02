<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_company_id',
        'code',
        'name',
        'type',
        'parent_id',
        'description',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function ownerCompany()
    {
        return $this->belongsTo(OwnerCompany::class, 'owner_company_id');
    }
} 