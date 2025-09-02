<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\CrmUser;
use App\Models\OwnerCompany;

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

        // Get owner companies or create a default one if none exist
        $ownerCompanies = OwnerCompany::all();
        if ($ownerCompanies->isEmpty()) {
            $ownerCompanies = collect([OwnerCompany::factory()->create()]);
        }
        
        foreach ($suppliersToUse as $supplier) {
            // Get owner company from supplier or user
            $ownerCompanyId = $supplier->owner_company_id ?? null;
            
            // If no owner company found, get the first one
            if (!$ownerCompanyId) {
                $ownerCompanyId = $ownerCompanies->first()->id;
            }
            
            PurchaseOrder::factory(rand(2, 5))->create([
                'supplier_id' => $supplier->supplier_id,
                'created_by_user_id' => $users->random()->user_id,
                'owner_company_id' => $ownerCompanyId,
            ]);
        }
        $this->command->info('Purchase Orders created.');
    }
}