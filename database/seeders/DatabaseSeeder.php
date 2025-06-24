<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Contact;
use App\Models\CrmUser;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // For a clean slate, you might want to disable foreign key checks before truncating
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // CrmUser::truncate(); // etc.
        // \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed foundational data that is mostly static
        $this->call([
            PermissionSeeder::class,
            UserRoleSeeder::class,
            CrmUserSeeder::class, // Creates specific users like 'Admin', 'Sales'
            ProductCategorySeeder::class,
            ProductFeatureSeeder::class,
            WarehouseSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class, // This seeder creates specific products, which is fine
        ]);

        // 2. Use factories to create a rich, dynamic dataset for testing
        $this->command->info('Creating dynamic data using factories...');

        // Get all users (from seeder and potentially new ones) to assign tasks/opportunities
        $users = CrmUser::all();
        if ($users->count() < 10) {
            $users = $users->merge(CrmUser::factory(10 - $users->count())->create());
        }

        // Create customers, each with contacts and opportunities
        Customer::factory(25)
            ->has(Contact::factory()->count(rand(1, 3)), 'contacts')
            ->create(['created_by_user_id' => $users->random()->user_id])
            ->each(function ($customer) use ($users) {
                // For each customer, create some opportunities to populate the Kanban board
                if ($customer->contacts->isNotEmpty()) {
                    Opportunity::factory(rand(1, 4))->create([
                        'customer_id' => $customer->customer_id,
                        'contact_id' => $customer->contacts->random()->contact_id,
                        'assigned_to_user_id' => $users->random()->user_id,
                        'created_by_user_id' => $users->random()->user_id,
                    ]);
                }
            });
        $this->command->info('Customers, Contacts, and Opportunities created.');

        // Create some standalone leads that haven't been converted yet
        Lead::factory(15)->create([
            'customer_id' => Customer::all()->random()->customer_id,
            'assigned_to_user_id' => $users->random()->user_id,
            'created_by_user_id' => $users->random()->user_id,
        ]);
        $this->command->info('Leads created.');

        // You can continue to call other factory-based seeders if needed
        // For example, if you refactor QuotationSeeder to use factories:
        // $this->call(QuotationSeeder::class);
    }
}