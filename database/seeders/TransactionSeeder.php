<?php

namespace Database\Seeders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\OwnerCompany;
use App\Models\Supplier;
use App\Models\Customer;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        // Asegúrate de tener datos base
        $ownerCompanies = OwnerCompany::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        // Si no hay datos, créalos
        if ($ownerCompanies->isEmpty()) {
            $ownerCompanies = OwnerCompany::factory(3)->create();
        }
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::factory(5)->create();
        }
        if ($customers->isEmpty()) {
            $customers = Customer::factory(5)->create();
        }

        // Crea transacciones de ejemplo
        foreach (range(1, 20) as $i) {
            Transaction::factory()->create([
                'owner_company_id' => $ownerCompanies->random()->id,
                'supplier_id'      => $suppliers->random()->id,
                'customer_id'      => $customers->random()->id,
            ]);
        }
    }
}