<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'owner_company_id',
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
        'all_day',
        'status',
        'google_calendar_event_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_day' => 'boolean',
    ];

    public static array $statuses = ['scheduled', 'completed', 'cancelled', 'rescheduled'];

    /**
     * Get the owner company that the appointment belongs to.
     */
    public function ownerCompany(): BelongsTo
    {
        return $this->belongsTo(OwnerCompany::class, 'owner_company_id');
    }

    /**
     * Get the user who created the appointment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'created_by_user_id');
    }

    /**
     * Get the participants of the appointment.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(AppointmentParticipant::class, 'appointment_id');
    }

    /**
     * Get the calendar event associated with the appointment.
     */
    public function calendarEvent(): HasOne
    {
        return $this->hasOne(CalendarEvent::class, 'related_id')
            ->where('related_type', 'appointment');
    }

    /**
     * Get the duration of the appointment in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime);
    }
}
