<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer; // Adjust the namespace according to your application structure
use App\Models\Address; // Add this line

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer1 = Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone_number' => '123-456-7890', 
            'company_name' => 'Doe Enterprises',
            'status' => 'active', // Example: Active, Inactive, Lead
            //'notes' => 'Important customer, handle with care.',
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
        ]);

        $customer1->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '123 Elm St',
            'city' => 'Springfield',
            'state_province' => 'IL',
            'postal_code' => '62701', 
            'country_code' => 'US',
            'is_primary' => true,
        ]);

        $customer2 = Customer::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone_number' => '987-654-3210',
            'company_name' => 'Smith LLC',
            'status' => 'inactive', // Example: Active, Inactive, Lead
            //'notes' => 'Potential lead, follow up next month.',
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
        ]);

        $customer2->addresses()->create([
            'address_type' => 'Billing',
            'street_address_line_1' => '456 Oak St',
            'city' => 'Shelbyville',
            'state_province' => 'IL',
            'postal_code' => '62565',
            'country_code' => 'US', 
            'is_primary' => true,
        ]);

        $customer3 = Customer::create([
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'phone_number' => '555-123-4567',
            'company_name' => 'Johnson Corp',       
            'status' => 'lead', // Example: Active, Inactive, Lead  
            //'notes' => 'Interested in our services, needs more information.',
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
        ]);

        $customer3->addresses()->create([
            'address_type' => 'Shipping',
            'street_address_line_1' => '789 Pine St',
            'city' => 'Capital City',
            'state_province' => 'IL',
            'postal_code' => '62702',
            'country_code' => 'US',
            'is_primary' => true,
        ]);

        $customer4 = Customer::create([
            'first_name' => 'Bob',
            'last_name' => 'Brown',
            'email' => 'bob@example.com',
            'phone_number' => '111-222-3333',
            'company_name' => 'Brown Industries',
            'status' => 'active', // Example: Active, Inactive, Lead
            //'notes' => 'Regular customer, always on time with payments.',
            'created_by_user_id' => 1, // Assuming user with ID 1 exists
        ]);

        $customer4->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '321 Maple St',
            'city' => 'Ogdenville',
            'state_province' => 'IL',
            'postal_code' => '62550',
            'country_code' => 'US',
            'is_primary' => true,
        ]);
            
    }
}
