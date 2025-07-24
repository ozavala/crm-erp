<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentParticipant;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AppointmentParticipantController extends Controller
{
    /**
     * Display a listing of the participants for an appointment.
     */
    public function index(Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load participants with their related models
        $appointment->load(['participants.participant']);
        
        // Group participants by type
        $userParticipants = $appointment->participants->where('participant_type', 'crm_user');
        $customerParticipants = $appointment->participants->where('participant_type', 'customer');
        $contactParticipants = $appointment->participants->where('participant_type', 'contact');
        
        return view('appointment_participants.index', compact(
            'appointment',
            'userParticipants',
            'customerParticipants',
            'contactParticipants'
        ));
    }

    /**
     * Show the form for adding participants to an appointment.
     */
    public function create(Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get users, customers, and contacts for participant selection
        $users = CrmUser::where('owner_company_id', $ownerCompanyId)->get();
        $customers = Customer::where('owner_company_id', $ownerCompanyId)->get();
        $contacts = Contact::whereHasMorph('contactable', [Customer::class], function ($query) use ($ownerCompanyId) {
            $query->where('owner_company_id', $ownerCompanyId);
        })->get();
        
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
        
        return view('appointment_participants.create', compact(
            'appointment',
            'users',
            'customers',
            'contacts',
            'userParticipantIds',
            'customerParticipantIds',
            'contactParticipantIds'
        ));
    }

    /**
     * Store newly created participants in storage.
     */
    public function store(Request $request, Appointment $appointment)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validate([
            'user_participants' => 'nullable|array',
            'user_participants.*' => 'exists:crm_users,user_id',
            'customer_participants' => 'nullable|array',
            'customer_participants.*' => 'exists:customers,customer_id',
            'contact_participants' => 'nullable|array',
            'contact_participants.*' => 'exists:contacts,contact_id',
        ]);
        
        DB::transaction(function () use ($appointment, $validatedData, $request) {
            // Add user participants
            if ($request->has('user_participants')) {
                foreach ($request->input('user_participants') as $userId) {
                    // Skip if this user is already a participant
                    $exists = $appointment->participants()
                        ->where('participant_type', 'crm_user')
                        ->where('participant_id', $userId)
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
                    // Skip if this customer is already a participant
                    $exists = $appointment->participants()
                        ->where('participant_type', 'customer')
                        ->where('participant_id', $customerId)
                        ->exists();
                        
                    if (!$exists) {
                        AppointmentParticipant::create([
                            'appointment_id' => $appointment->appointment_id,
                            'participant_type' => 'customer',
                            'participant_id' => $customerId,
                            'is_organizer' => false,
                            'response_status' => 'pending',
                        ]);
                    }
                }
            }
            
            // Add contact participants
            if ($request->has('contact_participants')) {
                foreach ($request->input('contact_participants') as $contactId) {
                    // Skip if this contact is already a participant
                    $exists = $appointment->participants()
                        ->where('participant_type', 'contact')
                        ->where('participant_id', $contactId)
                        ->exists();
                        
                    if (!$exists) {
                        AppointmentParticipant::create([
                            'appointment_id' => $appointment->appointment_id,
                            'participant_type' => 'contact',
                            'participant_id' => $contactId,
                            'is_organizer' => false,
                            'response_status' => 'pending',
                        ]);
                    }
                }
            }
        });
        
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
            ->with('success', 'Participants added successfully.');
    }

    /**
     * Update the response status of a participant.
     */
    public function updateStatus(Request $request, AppointmentParticipant $participant)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        $appointment = $participant->appointment;
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        $validatedData = $request->validate([
            'response_status' => 'required|in:pending,accepted,declined',
        ]);
        
        $participant->update([
            'response_status' => $validatedData['response_status'],
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
            ->with('success', 'Participant status updated successfully.');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy(AppointmentParticipant $participant)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        $appointment = $participant->appointment;
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Don't allow removing the organizer
        if ($participant->is_organizer) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Cannot remove the organizer from the appointment.');
        }
        
        // Delete the participant
        $participant->delete();
        
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
            ->with('success', 'Participant removed successfully.');
    }

    /**
     * Make a participant the organizer of the appointment.
     */
    public function makeOrganizer(AppointmentParticipant $participant)
    {
        $ownerCompanyId = Session::get('owner_company_id');
        $appointment = $participant->appointment;
        
        if ($appointment->owner_company_id != $ownerCompanyId) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only CRM users can be organizers
        if ($participant->participant_type !== 'crm_user') {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Only CRM users can be organizers.');
        }
        
        DB::transaction(function () use ($appointment, $participant) {
            // Unset current organizer
            $appointment->participants()
                ->where('is_organizer', true)
                ->update(['is_organizer' => false]);
                
            // Set new organizer
            $participant->update([
                'is_organizer' => true,
                'response_status' => 'accepted',
            ]);
        });
        
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
            ->with('success', 'Organizer updated successfully.');
    }
}