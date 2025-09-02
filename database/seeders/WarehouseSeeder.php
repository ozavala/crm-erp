<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse; // Assuming you have a Warehouse model

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouse1 = Warehouse::create([
            'name' => 'Main Warehouse',
            'location' => '123 Main St, City, Country',
            // 'address' => '123 Main St, City, Country', // Remove this line
            'is_active' => true,
        ]);
        $warehouse1->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '123 Main St',
            'city' => 'Anytown',
            'state_province' => 'CA',
            'postal_code' => '90210',
            'country_code' => 'US',
            'is_primary' => true,
        ]);

        $warehouse2 = Warehouse::create([
            'name' => 'Secondary Warehouse',
            'location' => '456 Secondary St, City, Country',
            // 'address' => '456 Secondary St, City, Country', // Remove this line
            'is_active' => true,
        ]);
        $warehouse2->addresses()->create([
            'address_type' => 'Primary',
            'street_address_line_1' => '456 Oak Ave',
            'city' => 'Otherville',
            'state_province' => 'NY',
            'postal_code' => '10001',
            'country_code' => 'US',
            'is_primary' => true,
        ]);
    }
}
