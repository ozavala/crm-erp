<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'calendar_setting_id';

    protected $fillable = [
        'owner_company_id',
        'user_id',
        'google_calendar_id',
        'is_primary',
        'auto_sync',
        'sync_frequency_minutes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'auto_sync' => 'boolean',
        'sync_frequency_minutes' => 'integer',
    ];

    /**
     * Get the owner company that the calendar setting belongs to.
     */
    public function ownerCompany(): BelongsTo
    {
        return $this->belongsTo(OwnerCompany::class, 'owner_company_id');
    }

    /**
     * Get the user that the calendar setting belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'user_id', 'user_id');
    }

    /**
     * Get the calendar events associated with this calendar setting.
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'google_calendar_id', 'google_calendar_id');
    }

    /**
     * Scope a query to only include primary calendar settings.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to only include calendar settings with auto sync enabled.
     */
    public function scopeAutoSync($query)
    {
        return $query->where('auto_sync', true);
    }

    /**
     * Scope a query to only include company-wide calendar settings.
     */
    public function scopeCompanyWide($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope a query to only include user-specific calendar settings.
     */
    public function scopeUserSpecific($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope a query to only include calendar settings for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the next sync time based on the sync frequency.
     */
    public function getNextSyncTimeAttribute()
    {
        $lastEvent = $this->calendarEvents()
            ->orderBy('last_synced_at', 'desc')
            ->first();

        if (!$lastEvent || !$lastEvent->last_synced_at) {
            return now();
        }

        return $lastEvent->last_synced_at->addMinutes($this->sync_frequency_minutes);
    }

    /**
     * Check if the calendar is due for syncing.
     */
    public function isDueForSync(): bool
    {
        return $this->auto_sync && now()->gte($this->next_sync_time);
    }
}