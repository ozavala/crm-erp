<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationship with UserRole (many-to-many)
    // Will be defined fully after pivot table migration
    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'permission_user_role', 'permission_id', 'role_id')->withTimestamps();
    }
}