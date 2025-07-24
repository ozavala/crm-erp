<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{
    /**
     * Sync an appointment with Google Calendar.
     *
     * @param CalendarEvent $calendarEvent
     * @return bool
     */
    public function syncAppointmentToGoogle(CalendarEvent $calendarEvent): bool
    {
        try {
            // Check if the calendar event is related to an appointment
            if ($calendarEvent->related_type !== 'appointment') {
                return false;
            }

            // Get the related appointment
            $appointment = Appointment::find($calendarEvent->related_id);
            if (!$appointment) {
                return false;
            }

            // Get participants
            $participants = [];
            foreach ($appointment->participants as $participant) {
                if ($participant->participant_type === 'crm_user' && $participant->participant) {
                    $participants[] = ['email' => $participant->participant->email];
                }
            }

            // Create or update the Google Calendar event
            if ($calendarEvent->google_event_id) {
                // Update existing event
                $event = Event::find($calendarEvent->google_event_id, $calendarEvent->google_calendar_id);
                if (!$event) {
                    // Event not found in Google Calendar, create a new one
                    $event = new Event;
                }
            } else {
                // Create new event
                $event = new Event;
            }

            // Set event properties
            $event->name = $appointment->title;
            $event->description = $appointment->description;
            $event->startDateTime = $appointment->start_datetime;
            $event->endDateTime = $appointment->end_datetime;
            $event->addAttendee($participants);

            if ($appointment->location) {
                $event->location = $appointment->location;
            }

            // Save the event to Google Calendar
            $event->setCalendarId($calendarEvent->google_calendar_id);
            $event->save();

            // Update the calendar event with the Google event ID
            $calendarEvent->google_event_id = $event->id;
            $calendarEvent->sync_status = 'synced';
            $calendarEvent->last_synced_at = now();
            $calendarEvent->save();

            return true;
        } catch (Exception $e) {
            Log::error('Error syncing appointment to Google Calendar: ' . $e->getMessage());
            
            // Update the calendar event with the error status
            $calendarEvent->sync_status = 'failed';
            $calendarEvent->save();
            
            return false;
        }
    }

    /**
     * Sync a task with Google Calendar.
     *
     * @param CalendarEvent $calendarEvent
     * @return bool
     */
    public function syncTaskToGoogle(CalendarEvent $calendarEvent): bool
    {
        try {
            // Check if the calendar event is related to a task
            if ($calendarEvent->related_type !== 'task') {
                return false;
            }

            // Get the related task
            $task = Task::find($calendarEvent->related_id);
            if (!$task) {
                return false;
            }

            // Create or update the Google Calendar event
            if ($calendarEvent->google_event_id) {
                // Update existing event
                $event = Event::find($calendarEvent->google_event_id, $calendarEvent->google_calendar_id);
                if (!$event) {
                    // Event not found in Google Calendar, create a new one
                    $event = new Event;
                }
            } else {
                // Create new event
                $event = new Event;
            }

            // Set event properties
            $event->name = $task->title;
            $event->description = $task->description;
            
            // For tasks, we'll create an all-day event on the due date
            if ($task->due_date) {
                $event->startDateTime = Carbon::parse($task->due_date)->startOfDay();
                $event->endDateTime = Carbon::parse($task->due_date)->endOfDay();
                $event->allDayEvent = true;
            } else {
                // If no due date, set it to today
                $event->startDateTime = Carbon::now()->startOfDay();
                $event->endDateTime = Carbon::now()->endOfDay();
                $event->allDayEvent = true;
            }

            // Save the event to Google Calendar
            $event->setCalendarId($calendarEvent->google_calendar_id);
            $event->save();

            // Update the calendar event with the Google event ID
            $calendarEvent->google_event_id = $event->id;
            $calendarEvent->sync_status = 'synced';
            $calendarEvent->last_synced_at = now();
            $calendarEvent->save();

            return true;
        } catch (Exception $e) {
            Log::error('Error syncing task to Google Calendar: ' . $e->getMessage());
            
            // Update the calendar event with the error status
            $calendarEvent->sync_status = 'failed';
            $calendarEvent->save();
            
            return false;
        }
    }

    /**
     * Delete an event from Google Calendar.
     *
     * @param CalendarEvent $calendarEvent
     * @return bool
     */
    public function deleteFromGoogle(CalendarEvent $calendarEvent): bool
    {
        try {
            // Check if the event exists in Google Calendar
            if (!$calendarEvent->google_event_id) {
                return true; // Nothing to delete
            }

            // Find the event in Google Calendar
            $event = Event::find($calendarEvent->google_event_id, $calendarEvent->google_calendar_id);
            if (!$event) {
                return true; // Event not found in Google Calendar
            }

            // Delete the event from Google Calendar
            $event->delete();

            return true;
        } catch (Exception $e) {
            Log::error('Error deleting event from Google Calendar: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Import events from Google Calendar.
     *
     * @param string $calendarId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $ownerCompanyId
     * @return array
     */
    public function importEventsFromGoogle(string $calendarId, Carbon $startDate, Carbon $endDate, int $ownerCompanyId): array
    {
        try {
            // Get events from Google Calendar
            $events = Event::get($startDate, $endDate, [], $calendarId);
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($events as $event) {
                // Check if the event already exists in our system
                $existingEvent = CalendarEvent::where('google_event_id', $event->id)
                    ->where('google_calendar_id', $calendarId)
                    ->first();
                    
                if ($existingEvent) {
                    $skipped++;
                    continue;
                }
                
                // Create a new appointment for this event
                $appointment = Appointment::create([
                    'owner_company_id' => $ownerCompanyId,
                    'title' => $event->name,
                    'description' => $event->description,
                    'location' => $event->location,
                    'start_datetime' => $event->startDateTime,
                    'end_datetime' => $event->endDateTime,
                    'all_day' => $event->allDayEvent,
                    'status' => 'scheduled',
                    'created_by_user_id' => auth()->id(),
                ]);
                
                // Create a calendar event record
                CalendarEvent::create([
                    'owner_company_id' => $ownerCompanyId,
                    'google_calendar_id' => $calendarId,
                    'google_event_id' => $event->id,
                    'related_type' => 'appointment',
                    'related_id' => $appointment->appointment_id,
                    'sync_status' => 'synced',
                    'last_synced_at' => now(),
                ]);
                
                $imported++;
            }
            
            return [
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
            ];
        } catch (Exception $e) {
            Log::error('Error importing events from Google Calendar: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sync all pending calendar events with Google Calendar.
     *
     * @param int $ownerCompanyId
     * @return array
     */
    public function syncAllPendingEvents(int $ownerCompanyId): array
    {
        $pendingEvents = CalendarEvent::where('owner_company_id', $ownerCompanyId)
            ->where('sync_status', 'pending')
            ->get();
            
        $succeeded = 0;
        $failed = 0;
        
        foreach ($pendingEvents as $event) {
            $success = false;
            
            if ($event->related_type === 'appointment') {
                $success = $this->syncAppointmentToGoogle($event);
            } elseif ($event->related_type === 'task') {
                $success = $this->syncTaskToGoogle($event);
            }
            
            if ($success) {
                $succeeded++;
            } else {
                $failed++;
            }
        }
        
        return [
            'total' => $pendingEvents->count(),
            'succeeded' => $succeeded,
            'failed' => $failed,
        ];
    }

    /**
     * Test the connection to Google Calendar.
     *
     * @param string $calendarId
     * @return bool
     */
    public function testConnection(string $calendarId): bool
    {
        try {
            // Try to get a single event from the calendar
            Event::get(Carbon::now(), Carbon::now()->addDay(), ['maxResults' => 1], $calendarId);
            return true;
        } catch (Exception $e) {
            Log::error('Error testing connection to Google Calendar: ' . $e->getMessage());
            return false;
        }
    }
}