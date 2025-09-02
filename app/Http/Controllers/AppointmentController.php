<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentParticipant;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Contact;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
    public function index(Request $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get filter parameters
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        
        // Base query with relationships
        $query = Appointment::with(['ownerCompany', 'createdBy', 'participants.participant'])
            ->where('owner_company_id', $ownerCompanyId);
        
        // Apply filters if provided
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($startDate) {
            $query->where('start_datetime', '>=', Carbon::parse($startDate)->startOfDay());
        }
        
        if ($endDate) {
            $query->where('end_datetime', '<=', Carbon::parse($endDate)->endOfDay());
        }
        
        if ($userId) {
            $query->whereHas('participants', function ($q) use ($userId) {
                $q->where('participant_type', 'crm_user')
                  ->where('participant_id', $userId);
            });
        }
        
        // Get appointments with pagination
        $appointments = $query->orderBy('start_datetime', 'desc')->paginate(10);
        
        // Get users for filter dropdown
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('appointments.index', compact('appointments', 'users'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get users, customers, and contacts for participant selection
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        $customers = Customer::where('owner_company_id', $ownerCompanyId)->get();
        $contacts = Contact::whereHasMorph('contactable', [Customer::class], function ($query) use ($ownerCompanyId) {
            $query->where('owner_company_id', $ownerCompanyId);
        })->get();
        
        // Get calendar settings for Google Calendar integration
        $calendarSettings = CalendarSetting::where('owner_company_id', $ownerCompanyId)
            ->where(function ($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
            })
            ->get();
        
        return view('appointments.create', compact('users', 'customers', 'contacts', 'calendarSettings'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        $validatedData = $request->validated();
        $validatedData['owner_company_id'] = $ownerCompanyId;
        $validatedData['created_by_user_id'] = Auth::id();
        
        // Format dates
        $validatedData['start_datetime'] = Carbon::parse($validatedData['start_datetime']);
        $validatedData['end_datetime'] = Carbon::parse($validatedData['end_datetime']);
        
        DB::transaction(function () use ($validatedData, $request) {
            // Create the appointment
            $appointment = Appointment::create($validatedData);
            
            // Add the creator as an organizer participant
            AppointmentParticipant::create([
                'appointment_id' => $appointment->appointment_id,
                'participant_type' => 'crm_user',
                'participant_id' => Auth::id(),
                'is_organizer' => true,
                'response_status' => 'accepted',
            ]);
            
            // Add other participants
            if ($request->has('user_participants')) {
                foreach ($request->input('user_participants') as $userId) {
                    if ($userId != Auth::id()) { // Skip creator as they're already added
                        AppointmentParticipant::create([
                            'appointment_id' => $appointment->appointment_id,
                            'participant_type' => 'crm_user',
                            'participant_id' => $userId,
                            'is_organizer' => false,
                            'response_status' => 'pending',
                        ]);
                    }
                }
            }
            
            if ($request->has('customer_participants')) {
                foreach ($request->input('customer_participants') as $customerId) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_type' => 'customer',
                        'participant_id' => $customerId,
                        'is_organizer' => false,
                        'response_status' => 'pending',
                    ]);
                }
            }
            
            if ($request->has('contact_participants')) {
                foreach ($request->input('contact_participants') as $contactId) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_type' => 'contact',
                        'participant_id' => $contactId,
                        'is_organizer' => false,
                        'response_status' => 'pending',
                    ]);
                }
            }
            
            // Create Google Calendar event if requested
            if ($request->has('sync_to_google_calendar') && $request->input('sync_to_google_calendar')) {
                $calendarId = $request->input('google_calendar_id');
                
                // Create a calendar event record
                CalendarEvent::create([
                    'owner_company_id' => $appointment->owner_company_id,
                    'google_calendar_id' => $calendarId,
                    'google_event_id' => null, // Will be updated after sync
                    'related_type' => 'appointment',
                    'related_id' => $appointment->appointment_id,
                    'sync_status' => 'pending',
                    'last_synced_at' => null,
                ]);
                
                // TODO: Implement actual Google Calendar sync
                // This would be handled by a job or service
            }
        });
        
        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $appointment->load(['ownerCompany', 'createdBy', 'participants.participant', 'calendarEvent']);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $appointment->load(['participants.participant']);
        
        // Get users, customers, and contacts for participant selection
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        $customers = Customer::where('owner_company_id', $ownerCompanyId)->get();
        $contacts = Contact::whereHasMorph('contactable', [Customer::class], function ($query) use ($ownerCompanyId) {
            $query->where('owner_company_id', $ownerCompanyId);
        })->get();
        
        // Get calendar settings for Google Calendar integration
        $calendarSettings = CalendarSetting::where('owner_company_id', $ownerCompanyId)
            ->where(function ($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', Auth::id());
            })
            ->get();
        
        // Get current participants by type
        $userParticipantIds = $appointment->participants
            ->where('participant_type', 'crm_user')
            ->pluck('participant_id')
            ->toArray();
            
        $customerParticipantIds = $appointment->participants
            ->where('participant_type', 'customer')
            ->pluck('participant_id')
            ->toArray();
            
        $contactParticipantIds = $appointment->participants
            ->where('participant_type', 'contact')
            ->pluck('participant_id')
            ->toArray();
        
        return view('appointments.edit', compact(
            'appointment', 
            'users', 
            'customers', 
            'contacts', 
            'calendarSettings',
            'userParticipantIds',
            'customerParticipantIds',
            'contactParticipantIds'
        ));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validated();
        
        // Format dates
        $validatedData['start_datetime'] = Carbon::parse($validatedData['start_datetime']);
        $validatedData['end_datetime'] = Carbon::parse($validatedData['end_datetime']);
        
        DB::transaction(function () use ($appointment, $validatedData, $request) {
            // Update the appointment
            $appointment->update($validatedData);
            
            // Update participants
            // First, remove all non-organizer participants
            $appointment->participants()
                ->where('is_organizer', false)
                ->delete();
            
            // Add user participants
            if ($request->has('user_participants')) {
                foreach ($request->input('user_participants') as $userId) {
                    // Skip if this user is already an organizer
                    $exists = $appointment->participants()
                        ->where('participant_type', 'crm_user')
                        ->where('participant_id', $userId)
                        ->where('is_organizer', true)
                        ->exists();
                        
                    if (!$exists) {
                        AppointmentParticipant::create([
                            'appointment_id' => $appointment->appointment_id,
                            'participant_type' => 'crm_user',
                            'participant_id' => $userId,
                            'is_organizer' => false,
                            'response_status' => 'pending',
                        ]);
                    }
                }
            }
            
            // Add customer participants
            if ($request->has('customer_participants')) {
                foreach ($request->input('customer_participants') as $customerId) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_type' => 'customer',
                        'participant_id' => $customerId,
                        'is_organizer' => false,
                        'response_status' => 'pending',
                    ]);
                }
            }
            
            // Add contact participants
            if ($request->has('contact_participants')) {
                foreach ($request->input('contact_participants') as $contactId) {
                    AppointmentParticipant::create([
                        'appointment_id' => $appointment->appointment_id,
                        'participant_type' => 'contact',
                        'participant_id' => $contactId,
                        'is_organizer' => false,
                        'response_status' => 'pending',
                    ]);
                }
            }
            
            // Update Google Calendar event if it exists
            if ($appointment->calendarEvent) {
                $appointment->calendarEvent->update([
                    'sync_status' => 'pending',
                    'last_synced_at' => null,
                ]);
                
                // TODO: Implement actual Google Calendar sync
                // This would be handled by a job or service
            }
            // Create new Google Calendar event if requested
            elseif ($request->has('sync_to_google_calendar') && $request->input('sync_to_google_calendar')) {
                $calendarId = $request->input('google_calendar_id');
                
                // Create a calendar event record
                CalendarEvent::create([
                    'owner_company_id' => $appointment->owner_company_id,
                    'google_calendar_id' => $calendarId,
                    'google_event_id' => null, // Will be updated after sync
                    'related_type' => 'appointment',
                    'related_id' => $appointment->appointment_id,
                    'sync_status' => 'pending',
                    'last_synced_at' => null,
                ]);
                
                // TODO: Implement actual Google Calendar sync
                // This would be handled by a job or service
            }
        });
        
        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        DB::transaction(function () use ($appointment) {
            // Delete Google Calendar event if it exists
            if ($appointment->calendarEvent) {
                // TODO: Implement actual Google Calendar deletion
                // This would be handled by a job or service
                
                $appointment->calendarEvent->delete();
            }
            
            // Delete participants
            $appointment->participants()->delete();
            
            // Delete the appointment
            $appointment->delete();
        });
        
        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Update the status of an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
        ]);
        
        $appointment->update([
            'status' => $validatedData['status'],
        ]);
        
        // Update Google Calendar event if it exists
        if ($appointment->calendarEvent) {
            $appointment->calendarEvent->update([
                'sync_status' => 'pending',
                'last_synced_at' => null,
            ]);
            
            // TODO: Implement actual Google Calendar sync
            // This would be handled by a job or service
        }
        
        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment status updated successfully.');
    }

    /**
     * Display the calendar view.
     */
    public function calendar(Request $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get filter parameters
        $userId = $request->input('user_id');
        $view = $request->input('view', 'month'); // Default to month view
        
        // Get users for filter dropdown
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        
        return view('appointments.calendar', compact('users', 'view', 'userId'));
    }

    /**
     * Get appointments as JSON for calendar.
     */
    public function getAppointmentsJson(Request $request)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        // Get filter parameters
        $start = $request->input('start'); // Start date from calendar
        $end = $request->input('end'); // End date from calendar
        $userId = $request->input('user_id');
        
        // Base query
        $query = Appointment::where('owner_company_id', $ownerCompanyId);
        
        // Filter by date range
        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                  ->orWhereBetween('end_datetime', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_datetime', '<=', $start)
                         ->where('end_datetime', '>=', $end);
                  });
            });
        }
        
        // Filter by user if specified
        if ($userId) {
            $query->whereHas('participants', function ($q) use ($userId) {
                $q->where('participant_type', 'crm_user')
                  ->where('participant_id', $userId);
            });
        }
        
        // Get appointments
        $appointments = $query->get();
        
        // Format for calendar
        $events = $appointments->map(function ($appointment) {
            $color = '';
            switch ($appointment->status) {
                case 'scheduled':
                    $color = '#4CAF50'; // Green
                    break;
                case 'completed':
                    $color = '#2196F3'; // Blue
                    break;
                case 'cancelled':
                    $color = '#F44336'; // Red
                    break;
                case 'rescheduled':
                    $color = '#FF9800'; // Orange
                    break;
            }
            
            return [
                'id' => $appointment->appointment_id,
                'title' => $appointment->title,
                'start' => $appointment->start_datetime->toIso8601String(),
                'end' => $appointment->end_datetime->toIso8601String(),
                'allDay' => $appointment->all_day,
                'url' => route('appointments.show', $appointment),
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        });
        
        return response()->json($events);
    }
}