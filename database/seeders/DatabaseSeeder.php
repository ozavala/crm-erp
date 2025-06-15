<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class, // Create Permissions first
            UserRoleSeeder::class,   // Create Roles and assign Permissions to Roles
            CrmUserSeeder::class,
            ProductCategorySeeder::class, // Product categories before products
            ProductFeatureSeeder::class, // Product features before products
            WarehouseSeeder::class, // Warehouses
            CustomerSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            LeadSeeder::class,
            OpportunitySeeder::class,
            QuotationSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
            PurchaseOrderSeeder::class,
            // Other seeders can be added here
        ]);
        
        // User::factory(10)->create();

        
    }
}
