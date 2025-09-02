<?php

namespace Database\Seeders;

use App\Models\CalendarSetting;
use App\Models\CrmUser;
use App\Models\OwnerCompany;
use Illuminate\Database\Seeder;

class CalendarSettingSeeder extends Seeder
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

        // Create company-wide calendar settings
        CalendarSetting::create([
            'owner_company_id' => $ownerCompany->id,
            'user_id' => null, // null for company-wide settings
            'google_calendar_id' => 'company_' . strtolower(str_replace(' ', '_', $ownerCompany->name)) . '@group.calendar.google.com',
            'is_primary' => true,
            'auto_sync' => true,
            'sync_frequency_minutes' => 60,
        ]);

        // Create user-specific calendar settings for some users
        foreach ($users->take(5) as $index => $user) {
            CalendarSetting::create([
                'owner_company_id' => $ownerCompany->id,
                'user_id' => $user->user_id,
                'google_calendar_id' => 'user_' . $user->email,
                'is_primary' => $index === 0, // First user gets primary calendar
                'auto_sync' => rand(0, 1) === 1,
                'sync_frequency_minutes' => [15, 30, 60, 120][rand(0, 3)],
            ]);
        }

        $this->command->info('Calendar settings seeded successfully.');
    }
}