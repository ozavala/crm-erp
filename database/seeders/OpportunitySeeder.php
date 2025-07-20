<?php

namespace Database\Seeders;

use App\Models\Opportunity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Opportunity::create([
            'name' => 'New Product Launch',
            'description' => 'Opportunity to sell new product line.',
            'lead_id' => 1,
            'customer_id' => 1, // Assuming customer with ID 1 exists
            'stage' => 'prospecting',
            'amount' => 15000.00,
            'expected_close_date' => now()->addDays(60), // 60 days from now
            'assigned_to_user_id' => 1, // Assuming user with ID 1 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'probability' => 75, // 75% probability of closing
        ]);
        Opportunity::create([
            'name' => 'Contract Renewal',
            'description' => 'Renewal of existing contract with customer.',
            'lead_id' => 2,
            'customer_id' => 2, // Assuming customer with ID 2 exists
            'stage' => 'negotiation',
            'amount' => 20000.00,
            'expected_close_date' => now()->addDays(30), // 30 days from now
            'assigned_to_user_id' => 2, // Assuming user with ID 2 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'probability' => 85, // 85% probability of closing
        ]);
        Opportunity::create([
            'name' => 'Upsell Existing Customer',
            'description' => 'Opportunity to upsell additional services to existing customer.',
            'lead_id' => 3,
            'customer_id' => 3, // Assuming customer with ID 3 exists
            'stage' => 'qualification',
            'amount' => 10000.00,
            'expected_close_date' => now()->addDays(45), // 45 days from now
            'assigned_to_user_id' => 3, // Assuming user with ID 3 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'probability' => 60, // 60% probability of closing
        ]);
        Opportunity::create([
            'name' => 'New Market Entry',
            'description' => 'Exploring opportunities in a new market segment.',
            'lead_id' => 4,
            'customer_id' => 4, // Assuming customer with ID 4 exists
            'stage' => 'prospecting',
            'amount' => 30000.00,
            'expected_close_date' => now()->addDays(90), // 90 days from now
            'assigned_to_user_id' => 4, // Assuming user with ID 4 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'probability' => 50, // 50% probability of closing
        ]);


    }
}
