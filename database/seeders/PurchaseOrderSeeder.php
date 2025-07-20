<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\CrmUser;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('purchase_orders')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $suppliers = Supplier::all();
        $users = CrmUser::all();

        if ($suppliers->isEmpty() || $users->isEmpty()) {
            $this->command->info('Skipping PurchaseOrderSeeder: No suppliers or users found. Please seed them first.');
            return;
        }

        // Create a few purchase orders for a subset of suppliers
        $suppliersToUse = $suppliers->random(min(5, $suppliers->count()));

        foreach ($suppliersToUse as $supplier) {
            PurchaseOrder::factory(rand(2, 5))->create([
                'supplier_id' => $supplier->supplier_id,
                'created_by_user_id' => $users->random()->user_id,
            ]);
        }
        $this->command->info('Purchase Orders created.');
    }
}