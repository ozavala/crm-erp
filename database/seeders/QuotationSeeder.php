<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quotation;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\CrmUser;
use Illuminate\Support\Str;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opportunities = Opportunity::all();
        $products = Product::where('is_service', false)->get(); // Get some products
        $services = Product::where('is_service', true)->get(); // Get some services
        $users = CrmUser::all();

        if ($opportunities->isEmpty() || $products->isEmpty() || $users->isEmpty()) {
            $this->command->info('Skipping QuotationSeeder: Missing Opportunities, Products, or Users.');
            return;
        }

        foreach ($opportunities as $index => $opportunity) {
            if ($users->isEmpty()) continue;
            $user = $users->random();

            $quotationData = [
                'opportunity_id' => $opportunity->opportunity_id,
                'subject' => 'Quotation for ' . $opportunity->name,
                'quotation_date' => now()->subDays(10 - $index),
                'expiry_date' => now()->addDays(20 + $index),
                'status' => Quotation::$statuses[array_rand(Quotation::$statuses)],
                'terms_and_conditions' => 'Payment due within 30 days. All sales are final.',
                'notes' => 'Special discount applied for this quotation.',
                'created_by_user_id' => $user->user_id,
            ];

            // Calculate totals
            $itemsData = [];
            $subtotal = 0;
            $productSample = $products->random(mt_rand(1, 2));

            foreach($productSample as $p) {
                $qty = mt_rand(1,5);
                $unitPrice = $p->price;
                $itemTotal = $qty * $unitPrice;
                $itemsData[] = [
                    'product_id' => $p->product_id,
                    'item_name' => $p->name,
                    'item_description' => $p->description,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'item_total' => $itemTotal,
                ];
                $subtotal += $itemTotal;
            }

            $quotationData['subtotal'] = $subtotal;
            // For simplicity, fixed discount and tax
            $quotationData['discount_type'] = 'percentage';
            $quotationData['discount_value'] = 5; // 5%
            $quotationData['discount_amount'] = ($subtotal * 5) / 100;
            $subtotalAfterDiscount = $subtotal - $quotationData['discount_amount'];
            $quotationData['tax_percentage'] = 10; // 10%
            $quotationData['tax_amount'] = ($subtotalAfterDiscount * 10) / 100;
            $quotationData['total_amount'] = $subtotalAfterDiscount + $quotationData['tax_amount'];

            $quotation = Quotation::create($quotationData);

            foreach ($itemsData as $item) {
                $quotation->items()->create($item);
            }
        }
    }
}