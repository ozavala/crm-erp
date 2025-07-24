<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\OwnerCompany;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
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

        // Get users for assignment
        $users = CrmUser::all();
        
        if ($users->isEmpty()) {
            $this->command->error('No CRM users found. Please run CrmUserSeeder first.');
            return;
        }

        // Create appointments for the next 30 days
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(30);

        // Create some past appointments (completed and cancelled)
        for ($i = 0; $i < 5; $i++) {
            $pastDate = Carbon::now()->subDays(rand(1, 15));
            $createdByUser = $users->random();
            
            Appointment::create([
                'owner_company_id' => $ownerCompany->id,
                'title' => 'Past Meeting ' . ($i + 1),
                'description' => 'This is a past meeting that has been ' . ($i % 2 == 0 ? 'completed' : 'cancelled'),
                'location' => 'Office',
                'start_datetime' => $pastDate->copy()->setHour(9 + $i)->setMinute(0),
                'end_datetime' => $pastDate->copy()->setHour(10 + $i)->setMinute(0),
                'all_day' => false,
                'status' => $i % 2 == 0 ? 'completed' : 'cancelled',
                'created_by_user_id' => $createdByUser->user_id,
            ]);
        }

        // Create some future appointments (scheduled)
        for ($i = 0; $i < 10; $i++) {
            $futureDate = Carbon::now()->addDays(rand(1, 30));
            $createdByUser = $users->random();
            $isAllDay = rand(0, 5) == 0; // 1 in 6 chance of being all-day event
            
            Appointment::create([
                'owner_company_id' => $ownerCompany->id,
                'title' => 'Meeting with ' . ($i % 2 == 0 ? 'Customer' : 'Team'),
                'description' => 'This is a ' . ($i % 2 == 0 ? 'customer' : 'team') . ' meeting scheduled for the future',
                'location' => $i % 3 == 0 ? 'Virtual (Zoom)' : ($i % 3 == 1 ? 'Office' : 'Client Site'),
                'start_datetime' => $isAllDay ? $futureDate->copy()->startOfDay() : $futureDate->copy()->setHour(9 + ($i % 8))->setMinute(0),
                'end_datetime' => $isAllDay ? $futureDate->copy()->endOfDay() : $futureDate->copy()->setHour(10 + ($i % 8))->setMinute(0),
                'all_day' => $isAllDay,
                'status' => 'scheduled',
                'created_by_user_id' => $createdByUser->user_id,
            ]);
        }

        // Create a few rescheduled appointments
        for ($i = 0; $i < 3; $i++) {
            $futureDate = Carbon::now()->addDays(rand(5, 20));
            $createdByUser = $users->random();
            
            Appointment::create([
                'owner_company_id' => $ownerCompany->id,
                'title' => 'Rescheduled Meeting ' . ($i + 1),
                'description' => 'This meeting was rescheduled from an earlier date',
                'location' => 'Conference Room',
                'start_datetime' => $futureDate->copy()->setHour(13)->setMinute(0),
                'end_datetime' => $futureDate->copy()->setHour(14)->setMinute(30),
                'all_day' => false,
                'status' => 'rescheduled',
                'created_by_user_id' => $createdByUser->user_id,
            ]);
        }

        $this->command->info('Appointments seeded successfully.');
    }
}