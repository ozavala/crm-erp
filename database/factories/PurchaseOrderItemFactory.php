<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $product->cost > 0 ? $product->cost : $this->faker->randomFloat(2, 5, 200);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => $product->product_id,
            'item_name' => $product->name,
            'item_description' => $product->description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'item_total' => $quantity * $unitPrice,
        ];
    }
}