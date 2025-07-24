<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\Appointment;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the calendar events.
     */
    public function index(Request $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get filter parameters
        $calendarId = $request->input('calendar_id');
        $relatedType = $request->input('related_type');
        $syncStatus = $request->input('sync_status');
        
        // Base query with relationships
        $query = CalendarEvent::with(['ownerCompany', 'related'])
            ->where('owner_company_id', $ownerCompanyId);
        
        // Apply filters if provided
        if ($calendarId) {
            $query->where('google_calendar_id', $calendarId);
        }
        
        if ($relatedType) {
            $query->where('related_type', $relatedType);
        }
        
        if ($syncStatus) {
            $query->where('sync_status', $syncStatus);
        }
        
        // Get calendar events with pagination
        $calendarEvents = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get calendar settings for filter dropdown
        $calendarSettings = CalendarSetting::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('calendar_events.index', compact('calendarEvents', 'calendarSettings'));
    }

    /**
     * Display the specified calendar event.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarEvent->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $calendarEvent->load(['ownerCompany', 'related']);
        
        return view('calendar_events.show', compact('calendarEvent'));
    }

    /**
     * Sync the calendar event with Google Calendar.
     */
    public function sync(CalendarEvent $calendarEvent, GoogleCalendarService $googleCalendarService)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarEvent->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $success = false;
        
        if ($calendarEvent->related_type === 'appointment') {
            $success = $googleCalendarService->syncAppointmentToGoogle($calendarEvent);
        } elseif ($calendarEvent->related_type === 'task') {
            $success = $googleCalendarService->syncTaskToGoogle($calendarEvent);
        }
        
        if ($success) {
            return redirect()->route('calendar-events.show', $calendarEvent)
                ->with('success', 'Calendar event synced successfully with Google Calendar.');
        } else {
            return redirect()->route('calendar-events.show', $calendarEvent)
                ->with('error', 'Failed to sync calendar event with Google Calendar.');
        }
    }

    /**
     * Remove the specified calendar event from storage.
     */
    public function destroy(CalendarEvent $calendarEvent, GoogleCalendarService $googleCalendarService)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($calendarEvent->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete from Google Calendar
        $googleCalendarService->deleteFromGoogle($calendarEvent);
        
        // Delete the calendar event
        $calendarEvent->delete();
        
        return redirect()->route('calendar-events.index')
            ->with('success', 'Calendar event deleted successfully.');
    }

    /**
     * Sync all pending calendar events with Google Calendar.
     */
    public function syncAll(GoogleCalendarService $googleCalendarService)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        $result = $googleCalendarService->syncAllPendingEvents($ownerCompanyId);
        
        return redirect()->route('calendar-events.index')
            ->with('success', "Synced {$result['succeeded']} of {$result['total']} calendar events successfully. {$result['failed']} failed.");
    }

    /**
     * Create a calendar event for an appointment.
     */
    public function createForAppointment(Request $request, Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validate([
            'google_calendar_id' => 'required|string|max:255',
        ]);
        
        // Check if a calendar event already exists for this appointment
        $existingEvent = CalendarEvent::where('related_type', 'appointment')
            ->where('related_id', $appointment->appointment_id)
            ->first();
            
        if ($existingEvent) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'A calendar event already exists for this appointment.');
        }
        
        // Create a calendar event
        CalendarEvent::create([
            'owner_company_id' => $ownerCompanyId,
            'google_calendar_id' => $validatedData['google_calendar_id'],
            'google_event_id' => null, // Will be updated after sync
            'related_type' => 'appointment',
            'related_id' => $appointment->appointment_id,
            'sync_status' => 'pending',
            'last_synced_at' => null,
        ]);
        
        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Calendar event created successfully.');
    }

    /**
     * Create a calendar event for a task.
     */
    public function createForTask(Request $request, Task $task)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($task->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validate([
            'google_calendar_id' => 'required|string|max:255',
        ]);
        
        // Check if a calendar event already exists for this task
        $existingEvent = CalendarEvent::where('related_type', 'task')
            ->where('related_id', $task->task_id)
            ->first();
            
        if ($existingEvent) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'A calendar event already exists for this task.');
        }
        
        // Create a calendar event
        CalendarEvent::create([
            'owner_company_id' => $ownerCompanyId,
            'google_calendar_id' => $validatedData['google_calendar_id'],
            'google_event_id' => null, // Will be updated after sync
            'related_type' => 'task',
            'related_id' => $task->task_id,
            'sync_status' => 'pending',
            'last_synced_at' => null,
        ]);
        
        return redirect()->route('tasks.show', $task)
            ->with('success', 'Calendar event created successfully.');
    }

    /**
     * Import events from Google Calendar.
     */
    public function import(Request $request, GoogleCalendarService $googleCalendarService)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        $validatedData = $request->validate([
            'google_calendar_id' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        
        $result = $googleCalendarService->importEventsFromGoogle(
            $validatedData['google_calendar_id'],
            $startDate,
            $endDate,
            $ownerCompanyId
        );
        
        if ($result['success']) {
            return redirect()->route('calendar-events.index')
                ->with('success', "Imported {$result['imported']} events from Google Calendar. {$result['skipped']} events were skipped.");
        } else {
            return redirect()->route('calendar-events.index')
                ->with('error', 'Failed to import events from Google Calendar: ' . ($result['error'] ?? 'Unknown error'));
        }
    }
    
    /**
     * Show the form for exporting calendar events.
     */
    public function exportForm()
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get calendar settings for filter dropdown
        $calendarSettings = CalendarSetting::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('calendar_events.export', compact('calendarSettings'));
    }

    /**
     * Export calendar events to iCalendar format.
     */
    public function export(Request $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'calendar_setting_id' => 'nullable|exists:calendar_settings,id',
            'related_type' => 'nullable|in:appointment,task',
        ]);
        
        // Base query with relationships
        $query = CalendarEvent::with(['related'])
            ->where('owner_company_id', $ownerCompanyId);
        
        // Apply filters if provided
        if (isset($validatedData['start_date']) && isset($validatedData['end_date'])) {
            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            
            $query->whereHas('related', function ($q) use ($startDate, $endDate) {
                $q->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '>=', $startDate)
                      ->where('start_date', '<=', $endDate);
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('due_date', '>=', $startDate)
                      ->where('due_date', '<=', $endDate);
                });
            });
        }
        
        if (isset($validatedData['calendar_setting_id'])) {
            $calendarSetting = CalendarSetting::findOrFail($validatedData['calendar_setting_id']);
            $query->where('google_calendar_id', $calendarSetting->calendar_id);
        }
        
        if (isset($validatedData['related_type'])) {
            $query->where('related_type', $validatedData['related_type']);
        }
        
        // Get calendar events
        $calendarEvents = $query->get();
        
        // Generate iCalendar content
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//CRM-ERP//Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        
        foreach ($calendarEvents as $event) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $event->id . "@crm-erp.com\r\n";
            
            if ($event->related_type === 'appointment' && $event->related) {
                $appointment = $event->related;
                $ical .= "SUMMARY:" . $this->escapeString($appointment->title) . "\r\n";
                $ical .= "DESCRIPTION:" . $this->escapeString($appointment->description ?? '') . "\r\n";
                $ical .= "LOCATION:" . $this->escapeString($appointment->location ?? '') . "\r\n";
                $ical .= "DTSTART:" . $appointment->start_date->format('Ymd\THis\Z') . "\r\n";
                $ical .= "DTEND:" . $appointment->end_date->format('Ymd\THis\Z') . "\r\n";
                $ical .= "STATUS:" . strtoupper($appointment->status) . "\r\n";
                $ical .= "CREATED:" . $appointment->created_at->format('Ymd\THis\Z') . "\r\n";
                $ical .= "LAST-MODIFIED:" . $appointment->updated_at->format('Ymd\THis\Z') . "\r\n";
            } elseif ($event->related_type === 'task' && $event->related) {
                $task = $event->related;
                $ical .= "SUMMARY:" . $this->escapeString($task->title) . "\r\n";
                $ical .= "DESCRIPTION:" . $this->escapeString($task->description ?? '') . "\r\n";
                
                if ($task->start_date) {
                    $ical .= "DTSTART:" . $task->start_date->format('Ymd\THis\Z') . "\r\n";
                }
                
                if ($task->due_date) {
                    $ical .= "DTEND:" . $task->due_date->format('Ymd\THis\Z') . "\r\n";
                    $ical .= "DUE:" . $task->due_date->format('Ymd\THis\Z') . "\r\n";
                }
                
                $ical .= "STATUS:" . strtoupper(str_replace('_', '-', $task->status)) . "\r\n";
                $ical .= "CREATED:" . $task->created_at->format('Ymd\THis\Z') . "\r\n";
                $ical .= "LAST-MODIFIED:" . $task->updated_at->format('Ymd\THis\Z') . "\r\n";
            }
            
            $ical .= "END:VEVENT\r\n";
        }
        
        $ical .= "END:VCALENDAR\r\n";
        
        // Generate filename
        $filename = 'calendar_export_' . Carbon::now()->format('Y-m-d_His') . '.ics';
        
        // Return as downloadable file
        return Response::make($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Escape special characters in iCalendar strings.
     */
    private function escapeString($string)
    {
        $string = str_replace(["\r\n", "\n"], "\\n", $string);
        $string = str_replace(["\\", ";", ","], ["\\\\", "\\;", "\\,"], $string);
        return $string;
    }
}