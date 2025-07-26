<?php

namespace App\Policies;

use App\Models\CrmUser;
use App\Models\CalendarEvent;
use App\Models\AppointmentParticipant;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any calendar events.
     */
    public function viewAny(CrmUser $user): bool
    {
        // All authenticated users can view calendar events within their company scope
        return true;
    }

    /**
     * Determine whether the user can view the calendar event.
     */
    public function view(CrmUser $user, CalendarEvent $calendarEvent): bool
    {
        // Check if the user belongs to the same company as the calendar event
        if ($user->owner_company_id === $calendarEvent->owner_company_id) {
            return true;
        }

        // If the event is related to an appointment, check if the user is a participant
        if ($calendarEvent->related_type === 'appointment' && $calendarEvent->related) {
            return AppointmentParticipant::where('appointment_id', $calendarEvent->related->appointment_id)
                ->where(function ($query) use ($user) {
                    $query->where('participant_type', 'crm_user')
                          ->where('participant_id', $user->crm_user_id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create calendar events.
     */
    public function create(CrmUser $user): bool
    {
        // All authenticated users can create calendar events within their company scope
        return true;
    }

    /**
     * Determine whether the user can update the calendar event.
     */
    public function update(CrmUser $user, CalendarEvent $calendarEvent): bool
    {
        // Only users from the same company can update the event
        if ($user->owner_company_id !== $calendarEvent->owner_company_id) {
            return false;
        }

        // Additional check: Only the creator or a participant can update
        if ($calendarEvent->related_type === 'appointment' && $calendarEvent->related) {
            // Check if the user is the creator of the related appointment
            if ($calendarEvent->related->created_by_user_id === $user->crm_user_id) {
                return true;
            }

            // Check if the user is a participant of the related appointment
            return AppointmentParticipant::where('appointment_id', $calendarEvent->related->appointment_id)
                ->where(function ($query) use ($user) {
                    $query->where('participant_type', 'crm_user')
                          ->where('participant_id', $user->crm_user_id);
                })
                ->exists();
        }

        // If not related to an appointment, only the owner company check applies
        return true;
    }

    /**
     * Determine whether the user can delete the calendar event.
     */
    public function delete(CrmUser $user, CalendarEvent $calendarEvent): bool
    {
        // Only users from the same company can delete the event
        if ($user->owner_company_id !== $calendarEvent->owner_company_id) {
            return false;
        }

        // Additional check: Only the creator or a participant can delete
        if ($calendarEvent->related_type === 'appointment' && $calendarEvent->related) {
            // Check if the user is the creator of the related appointment
            if ($calendarEvent->related->created_by_user_id === $user->crm_user_id) {
                return true;
            }

            // Check if the user is a participant of the related appointment
            return AppointmentParticipant::where('appointment_id', $calendarEvent->related->appointment_id)
                ->where(function ($query) use ($user) {
                    $query->where('participant_type', 'crm_user')
                          ->where('participant_id', $user->crm_user_id);
                })
                ->exists();
        }

        // If not related to an appointment, only the owner company check applies
        return true;
    }
}
