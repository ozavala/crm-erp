<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\CrmUser;
use App\Models\Warehouse; // To get a shipping address
use App\Models\Address;   // To link warehouse address
use Illuminate\Support\Str;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::where('is_service', false)->get(); // Typically purchase physical products
        $users = CrmUser::all();
        $warehouses = Warehouse::with('addresses')->get(); // Assuming Warehouse model has 'addresses' relationship

        if ($suppliers->isEmpty() || $products->isEmpty() || $users->isEmpty() || $warehouses->isEmpty()) {
            $this->command->info('Skipping PurchaseOrderSeeder: Missing Suppliers, Products, Users, or Warehouses.');
            return;
        }

        for ($i = 0; $i < 5; $i++) { // Create 5 sample purchase orders
            $supplier = $suppliers->random();
            $user = $users->random();
            $warehouse = $warehouses->random();
            $shippingAddress = $warehouse->addresses()->where('is_primary', true)->first() ?? $warehouse->addresses()->first();

            $purchaseOrderData = [
                'supplier_id' => $supplier->supplier_id,
                'shipping_address_id' => $shippingAddress ? $shippingAddress->address_id : null,
                'purchase_order_number' => 'PO-' . strtoupper(Str::random(8)),
                'order_date' => now()->subDays(rand(1, 30)),
                'expected_delivery_date' => now()->addDays(rand(7, 45)),
                'type' => PurchaseOrder::$types[array_rand(PurchaseOrder::$types)],
                'status' => PurchaseOrder::$statuses[array_rand(PurchaseOrder::$statuses)],
                'terms_and_conditions' => 'Net 30. Delivery to ' . ($shippingAddress ? $shippingAddress->street_address_line_1 : $warehouse->name),
                'notes' => 'Urgent restock for ' . $products->random()->name,
                'created_by_user_id' => $user->user_id,
            ];

            // Calculate totals
            $itemsData = [];
            $subtotal = 0;
            $productSample = $products->random(mt_rand(1, 3));

            foreach($productSample as $p) {
                $qty = mt_rand(5, 20); // Purchase in larger quantities
                $unitPrice = $p->cost ?? $p->price * 0.7; // Use cost if available, else estimate
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

            $purchaseOrderData['subtotal'] = $subtotal;
            // For simplicity, no discount and fixed tax/shipping for POs in seeder
            $purchaseOrderData['tax_percentage'] = 5; // 5%
            $purchaseOrderData['tax_amount'] = ($subtotal * 5) / 100;
            $purchaseOrderData['shipping_cost'] = rand(10, 50);
            $purchaseOrderData['total_amount'] = $subtotal + $purchaseOrderData['tax_amount'] + $purchaseOrderData['shipping_cost'];

            $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

            foreach ($itemsData as $item) {
                $purchaseOrder->items()->create($item);
            }
        }
    }
}