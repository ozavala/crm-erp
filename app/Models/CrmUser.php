<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use App\Models\UserRole; // Ensure you have the correct namespace for UserRole

 
class CrmUser extends Authenticatable implements MustVerifyEmail
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
        'locale',
        'owner_company_id',
        'is_super_admin',
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
     * Determine if the user has verified their email address.
     * En desarrollo, siempre retorna true para evitar problemas de verificación.
     */
    public function hasVerifiedEmail(): bool
    {
        if (app()->environment('local', 'development')) {
            return true;
        }
        
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        if (app()->environment('local', 'development')) {
            return true;
        }
        
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        if (app()->environment('local', 'development')) {
            return; // No enviar emails en desarrollo
        }
        
        parent::sendEmailVerificationNotification();
    }

    /**
     * The roles that belong to the CRM user.
     */
    public function roles()
    {
        return $this->belongsToMany(UserRole::class, 'crm_user_user_role', 'crm_user_id', 'role_id')->withTimestamps();
    }
    public function hasPermissionTo(string $permissionName): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    /**
     * Accessor for the 'name' attribute, for compatibility with Breeze.
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function leads() 
    {
        return $this->hasMany(Lead::class, 'created_by_user_id', 'user_id');
    }
    public function customers() 
    {
        return $this->hasMany(Customer::class, 'created_by_user_id', 'user_id');
    }
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function ownerCompany()
    {
        return $this->belongsTo(OwnerCompany::class, 'owner_company_id', 'id');
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isCompanyAdmin(): bool
    {
        return $this->roles()->where('name', 'Company Admin')->exists();
    }

    public function canAccessCompany(int $companyId): bool
    {
        return $this->isSuperAdmin() || $this->owner_company_id === $companyId;
    }
}
   
