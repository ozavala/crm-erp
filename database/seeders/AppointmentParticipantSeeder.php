<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentParticipant;
use App\Models\Contact;
use App\Models\CrmUser;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class AppointmentParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all appointments
        $appointments = Appointment::all();
        
        if ($appointments->isEmpty()) {
            $this->command->error('No appointments found. Please run AppointmentSeeder first.');
            return;
        }

        // Get users, customers, and contacts for participants
        $users = CrmUser::all();
        $customers = Customer::all();
        $contacts = Contact::all();
        
        if ($users->isEmpty() || $customers->isEmpty()) {
            $this->command->error('No CRM users or customers found. Please run CrmUserSeeder and CustomerSeeder first.');
            return;
        }

        foreach ($appointments as $appointment) {
            // The user who created the appointment is always a participant and organizer
            AppointmentParticipant::create([
                'appointment_id' => $appointment->appointment_id,
                'participant_type' => 'crm_user',
                'participant_id' => $appointment->created_by_user_id,
                'is_organizer' => true,
                'response_status' => 'accepted',
            ]);

            // Add 1-3 additional CRM users as participants
            $additionalUsers = $users->where('user_id', '!=', $appointment->created_by_user_id)
                                    ->random(min(3, $users->count() - 1));
            
            foreach ($additionalUsers as $user) {
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_type' => 'crm_user',
                    'participant_id' => $user->user_id,
                    'is_organizer' => false,
                    'response_status' => ['pending', 'accepted', 'declined'][rand(0, 2)],
                ]);
            }

            // For customer meetings, add a customer and possibly their contacts
            if (strpos($appointment->title, 'Customer') !== false) {
                $customer = $customers->random();
                
                AppointmentParticipant::create([
                    'appointment_id' => $appointment->appointment_id,
                    'participant_type' => 'customer',
                    'participant_id' => $customer->customer_id,
                    'is_organizer' => false,
                    'response_status' => 'accepted',
                ]);

                // If the customer has contacts, add some of them
                $customerContacts = $contacts->where('contactable_id', $customer->customer_id)
                                           ->where('contactable_type', 'customer');
                
                if ($customerContacts->isNotEmpty()) {
                    foreach ($customerContacts->take(rand(1, $customerContacts->count())) as $contact) {
                        AppointmentParticipant::create([
                            'appointment_id' => $appointment->appointment_id,
                            'participant_type' => 'contact',
                            'participant_id' => $contact->contact_id,
                            'is_organizer' => false,
                            'response_status' => ['pending', 'accepted'][rand(0, 1)],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Appointment participants seeded successfully.');
    }
}