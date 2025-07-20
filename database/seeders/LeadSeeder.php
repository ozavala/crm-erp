<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead; // Adjust the namespace according to your application structure

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lead::create([
            'title' => 'New Customer Inquiry',
            'description' => 'Inquiry about our new product line.',
            'value' => 5000.00,
            'status' => 'new',
            'source' => 'website',
            'customer_id' => 1, // Assuming customer with ID 1 exists
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '123-456-7890',
            'assigned_to_user_id' => 1, // Assuming user with ID 1 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'expected_close_date' => now()->addDays(30), // 30 days from now
        ]);
        
        Lead::create([
            'title' => 'Follow-up on Previous Lead',
            'description' => 'Follow-up on the lead from last month regarding our services.',
            'value' => 3000.00,
            'status' => 'follow-up',
            'source' => 'email',
            'customer_id' => 2, // Assuming customer with ID 2 exists
            'contact_name' => 'Jane Smith',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '987-654-3210',
            'assigned_to_user_id' => 2, // Assuming user with ID 2 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'expected_close_date' => now()->addDays(15), // 15 days from now
        ]);
        Lead::create([
            'title' => 'Potential Partnership',
            'description' => 'Discussion about a potential partnership with another company.',
            'value' => 10000.00,
            'status' => 'negotiation',
            'source' => 'networking',
            'customer_id' => 3, // Assuming customer with ID 3 exists
            'contact_name' => 'Alice Johnson',
            'contact_email' => 'alice@example.com',
            'contact_phone' => '555-123-4567',
            'assigned_to_user_id' => 3, // Assuming user with ID 3 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'expected_close_date' => now()->addDays(45), // 45 days from now
        ]);
        Lead::create([
            'title' => 'Product Feedback',
            'description' => 'Feedback from a customer about our latest product.',
            'value' => 0.00, // No monetary value for feedback
            'status' => 'feedback',
            'source' => 'customer',
            'customer_id' => 4, // Assuming customer with ID 4 exists
            'contact_name' => 'Bob Brown',
            'contact_email' => 'bob@example.com',
            'contact_phone' => '111-222-3333',
            'assigned_to_user_id' => 4, // Assuming user with ID 4 exists
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
            'expected_close_date' => now()->addDays(10), // 10 days from now
        ]);
    }
}
