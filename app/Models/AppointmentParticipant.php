<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AppointmentParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'participant_type',
        'participant_id',
        'is_organizer',
        'response_status',
    ];

    protected $casts = [
        'is_organizer' => 'boolean',
    ];

    public static array $participantTypes = ['crm_user', 'customer', 'contact'];
    public static array $responseStatuses = ['pending', 'accepted', 'declined'];

    /**
     * Get the appointment that the participant belongs to.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    /**
     * Get the participant model (polymorphic).
     */
    public function participant(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include organizers.
     */
    public function scopeOrganizers($query)
    {
        return $query->where('is_organizer', true);
    }

    /**
     * Scope a query to only include participants with a specific response status.
     */
    public function scopeWithResponseStatus($query, $status)
    {
        return $query->where('response_status', $status);
    }

    /**
     * Scope a query to only include participants of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('participant_type', $type);
    }
}