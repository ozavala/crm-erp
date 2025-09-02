<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\CalendarEvent;
use App\Models\CalendarSetting;
use App\Models\OwnerCompany;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the owner company
        $ownerCompany = OwnerCompany::first();
        
        if (!$ownerCompany) {
            $this->command->error('Owner company not found. Please run OwnerCompanySeeder first.');
            return;
        }

        // Get calendar settings
        $calendarSettings = CalendarSetting::all();
        
        if ($calendarSettings->isEmpty()) {
            $this->command->error('No calendar settings found. Please run CalendarSettingSeeder first.');
            return;
        }

        // Get the primary company calendar
        $primaryCalendar = $calendarSettings->where('is_primary', true)->first();
        
        if (!$primaryCalendar) {
            $this->command->error('No primary calendar found. Please run CalendarSettingSeeder first.');
            return;
        }

        // Get appointments
        $appointments = Appointment::all();
        
        if ($appointments->isEmpty()) {
            $this->command->error('No appointments found. Please run AppointmentSeeder first.');
            return;
        }

        // Get tasks
        $tasks = Task::all();

        // Create calendar events for appointments
        foreach ($appointments as $appointment) {
            CalendarEvent::create([
                'owner_company_id' => $ownerCompany->id,
                'google_calendar_id' => $primaryCalendar->google_calendar_id,
                'google_event_id' => 'event_' . Str::random(16),
                'related_type' => 'appointment',
                'related_id' => $appointment->appointment_id,
                'sync_status' => ['synced', 'pending'][rand(0, 1)],
                'last_synced_at' => rand(0, 1) ? Carbon::now() : null,
            ]);
        }

        // Create calendar events for some tasks with due dates
        if ($tasks->isNotEmpty()) {
            $tasksWithDueDate = $tasks->filter(function ($task) {
                return $task->due_date !== null;
            });

            foreach ($tasksWithDueDate->take(5) as $task) {
                // Get a random user calendar or use the primary calendar
                $calendar = $calendarSettings->random();
                
                CalendarEvent::create([
                    'owner_company_id' => $ownerCompany->id,
                    'google_calendar_id' => $calendar->google_calendar_id,
                    'google_event_id' => 'task_' . Str::random(16),
                    'related_type' => 'task',
                    'related_id' => $task->task_id,
                    'sync_status' => ['synced', 'pending', 'failed'][rand(0, 2)],
                    'last_synced_at' => rand(0, 1) ? Carbon::now() : null,
                ]);
            }
        }

        $this->command->info('Calendar events seeded successfully.');
    }
}