<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $primaryKey = 'calendar_event_id';

    protected $fillable = [
        'owner_company_id',
        'google_calendar_id',
        'google_event_id',
        'related_type',
        'related_id',
        'sync_status',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public static array $syncStatuses = ['synced', 'pending', 'failed'];
    public static array $relatedTypes = ['appointment', 'task'];

    /**
     * Get the owner company that the calendar event belongs to.
     */
    public function ownerCompany(): BelongsTo
    {
        return $this->belongsTo(OwnerCompany::class, 'owner_company_id');
    }

    /**
     * Get the related model (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include calendar events with a specific sync status.
     */
    public function scopeWithSyncStatus($query, $status)
    {
        return $query->where('sync_status', $status);
    }

    /**
     * Scope a query to only include calendar events for a specific Google Calendar.
     */
    public function scopeForGoogleCalendar($query, $googleCalendarId)
    {
        return $query->where('google_calendar_id', $googleCalendarId);
    }

    /**
     * Scope a query to only include calendar events that need to be synced.
     */
    public function scopeNeedsSyncing($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope a query to only include calendar events for a specific related type.
     */
    public function scopeForRelatedType($query, $type)
    {
        return $query->where('related_type', $type);
    }

    /**
     * Mark the calendar event as synced.
     */
    public function markAsSynced()
    {
        $this->update([
            'sync_status' => 'synced',
            'last_synced_at' => now(),
        ]);
    }

    /**
     * Mark the calendar event as failed.
     */
    public function markAsFailed()
    {
        $this->update([
            'sync_status' => 'failed',
            'last_synced_at' => now(),
        ]);
    }

    /**
     * Mark the calendar event as pending.
     */
    public function markAsPending()
    {
        $this->update([
            'sync_status' => 'pending',
            'last_synced_at' => null,
        ]);
    }
}