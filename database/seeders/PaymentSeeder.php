<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\OwnerCompany;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder should run AFTER Orders and PurchaseOrders have been seeded.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('payments')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeding payments for Orders and Purchase Orders...');

        // Seed payments for Sales Orders
        $orders = Order::where('status', '!=', 'Paid')->where('status', '!=', 'Cancelled')->get();
        if ($orders->isNotEmpty()) {
            foreach ($orders->random(min(10, $orders->count())) as $order) {
                $this->createPaymentsForPayable($order);
            }
        }

        // Seed payments for Purchase Orders
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'Paid')->where('status', '!=', 'Cancelled')->get();
        if ($purchaseOrders->isNotEmpty()) {
            foreach ($purchaseOrders->random(min(10, $purchaseOrders->count())) as $po) {
                $this->createPaymentsForPayable($po);
            }
        }

        $this->command->info('Payments seeded successfully.');
    }

    /**
     * Create one full or partial payment for a given payable model.
     *
     * @param \Illuminate\Database\Eloquent\Model $payable
     */
    private function createPaymentsForPayable($payable): void
    {
        $totalAmount = $payable->total_amount;
        if ($totalAmount <= 0) {
            return;
        }

        // Decide whether to make one full payment or one partial payment
        // Get owner company from payable entity
        $ownerCompanyId = $payable->owner_company_id ?? null;
        
        // If no owner company found, get the first one or create one
        if (!$ownerCompanyId) {
            $ownerCompany = OwnerCompany::first() ?? OwnerCompany::factory()->create();
            $ownerCompanyId = $ownerCompany->id;
        }
        
        if (rand(0, 2) == 0) { // ~33% chance of full payment
            Payment::factory()->create([
                'payable_id' => $payable->getKey(),
                'payable_type' => get_class($payable),
                'amount' => $totalAmount,
                'created_by_user_id' => $payable->created_by_user_id,
                'owner_company_id' => $ownerCompanyId
            ]);
        } else { // ~66% chance of partial payment
            $partialAmount = round($totalAmount * rand(20, 80) / 100, 2);
            if ($partialAmount > 0) {
                Payment::factory()->create([
                    'payable_id' => $payable->getKey(),
                    'payable_type' => get_class($payable),
                    'amount' => $partialAmount,
                    'created_by_user_id' => $payable->created_by_user_id,
                    'owner_company_id' => $ownerCompanyId
                ]);
            }
        }
    }
}