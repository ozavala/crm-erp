<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

class CalendarEventController extends Controller
{
    protected $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CalendarEvent::class);

        // For simplicity, assuming a default calendar ID and owner company ID for now.
        // In a real application, these would be dynamically determined (e.g., from user settings).
        $calendarId = config('google-calendar.calendar_id'); // Get default calendar ID from config
        $ownerCompanyId = auth()->user()->owner_company_id; // Assuming authenticated user has an owner_company_id

        $startDate = Carbon::parse($request->input('start', Carbon::now()->subMonths(3)->toDateString()));
        $endDate = Carbon::parse($request->input('end', Carbon::now()->addMonths(3)->toDateString()));

        // Import events from Google Calendar to ensure local data is up-to-date
        $this->googleCalendarService->importEventsFromGoogle($calendarId, $startDate, $endDate, $ownerCompanyId);

        // Fetch events from the local database that are within the requested range
        $events = CalendarEvent::where('owner_company_id', $ownerCompanyId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start', [$startDate, $endDate])
                      ->orWhereBetween('end', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start', '<=', $startDate)
                                ->where('end', '>=', $endDate);
                      });
            })
            ->get();

        // Transform events to FullCalendar format
        return response()->json($events->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end ? $event->end->toIso8601String() : null,
                'description' => $event->description,
                'allDay' => (bool)$event->allDay,
                'google_event_id' => $event->google_event_id, // Include Google Event ID
            ];
        }));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', CalendarEvent::class);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'description' => 'nullable|string',
            'allDay' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ownerCompanyId = auth()->user()->owner_company_id;

        // Create a related Appointment for the CalendarEvent
        $appointment = Appointment::create([
            'owner_company_id' => $ownerCompanyId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_datetime' => $request->input('start'),
            'end_datetime' => $request->input('end'),
            'all_day' => $request->input('allDay', false),
            'status' => 'scheduled',
            'created_by_user_id' => auth()->id(),
        ]);

        // Create the local CalendarEvent record
        $calendarEvent = CalendarEvent::create([
            'owner_company_id' => $ownerCompanyId,
            'title' => $request->input('title'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'description' => $request->input('description'),
            'allDay' => $request->input('allDay', false),
            'related_type' => 'appointment',
            'related_id' => $appointment->appointment_id,
            'sync_status' => 'pending', // Mark as pending for Google Calendar sync
        ]);

        // Sync the new event to Google Calendar
        $this->googleCalendarService->syncAppointmentToGoogle($calendarEvent);

        return response()->json($calendarEvent, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        $this->authorize('view', $calendarEvent);
        return response()->json($calendarEvent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $this->authorize('update', $calendarEvent);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'start' => 'sometimes|required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'description' => 'nullable|string',
            'allDay' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $calendarEvent->update($request->all());

        // Update the related Appointment as well
        if ($calendarEvent->related_type === 'appointment' && $calendarEvent->related)
        {
            $calendarEvent->related->update([
                'title' => $request->input('title', $calendarEvent->related->title),
                'description' => $request->input('description', $calendarEvent->related->description),
                'start_datetime' => $request->input('start', $calendarEvent->related->start_datetime),
                'end_datetime' => $request->input('end', $calendarEvent->related->end_datetime),
                'all_day' => $request->input('allDay', $calendarEvent->related->all_day),
            ]);
        }

        // Mark as pending for Google Calendar sync and then sync
        $calendarEvent->sync_status = 'pending';
        $calendarEvent->save();
        $this->googleCalendarService->syncAppointmentToGoogle($calendarEvent);

        return response()->json($calendarEvent);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorize('delete', $calendarEvent);

        // Delete from Google Calendar first
        $this->googleCalendarService->deleteFromGoogle($calendarEvent);

        // Delete the related Appointment
        if ($calendarEvent->related_type === 'appointment' && $calendarEvent->related) {
            $calendarEvent->related->delete();
        }

        // Delete the local CalendarEvent record
        $calendarEvent->delete();

        return response()->json(null, 204);
    }
}
