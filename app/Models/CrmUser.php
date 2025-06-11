<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\UserRole; // Ensure you have the correct namespace for UserRole

 
class CrmUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * The roles that belong to the CRM user.
     */
    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'crm_user_user_role', 'crm_user_id', 'role_id')->withTimestamps();
    }

    /**
     * Accessor for the 'name' attribute, for compatibility with Breeze.
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }
}
   
