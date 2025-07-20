<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier; // Adjust the namespace according to your application structure

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('suppliers')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Supplier::create([
            'name' => 'ABC Supplies',
            'legal_id' => 'SUP-0001',
            'contact_person' => 'John Smith',
            'email' => 'john@example.com',
            'phone_number' => '123-456-7890',
            'notes' => 'Reliable supplier with quick delivery times.',
        ])->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '123 Main St',
            'city' => 'Springfield',
            'state_province' => 'IL',
            'postal_code' => '62701',
            'country_code' => 'US',
            'is_primary' => true,
        ]);
        Supplier::create([
            'name' => 'XYZ Distributors',
            'legal_id' => 'SUP-0002',
            'contact_person' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone_number' => '987-654-3210',
            'notes' => 'Specializes in electronic components.',
        ])->addresses()->create([
            'address_type' => 'Billing',
            'street_address_line_1' => '456 Elm St',
            'city' => 'Shelbyville',
            'state_province' => 'IL',
            'postal_code' => '62565',
            'country_code' => 'US',
            'is_primary' => true,
        ]);
        Supplier::create([
            'name' => 'Global Traders',
            'legal_id' => 'SUP-0003',
            'contact_person' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'phone_number' => '555-123-4567',
            'notes' => 'Offers a wide range of products at competitive prices.',
        ])->addresses()->create([
            'address_type' => 'Shipping',
            'street_address_line_1' => '789 Oak St',
            'city' => 'Capital City',
            'state_province' => 'IL',
            'postal_code' => '62702',
            'country_code' => 'US',
            'is_primary' => true,
        ]);
        Supplier::create([
            'name' => 'Local Goods',
            'legal_id' => 'SUP-0004',
            'contact_person' => 'Bob Brown',
            'email' => 'bob@example.com',
            'phone_number' => '321-654-0987',
            'notes' => 'Focuses on locally sourced products.',
        ])->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '321 Pine St',
            'city' => 'Greenfield',
            'state_province' => 'IL',
            'postal_code' => '62703',
            'country_code' => 'US',
            'is_primary' => true,
        ]);     
    }
}
