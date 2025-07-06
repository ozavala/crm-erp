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
        $product = Product::where('is_service', false)->inRandomOrder()->first() ?? Product::factory()->create(['is_service' => false]);
        $quantity = $this->faker->numberBetween(5, 50);
        $unitPrice = $product->cost ?? $product->price * 0.7; // Use cost if available, else estimate

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => $product->product_id,
            'item_name' => $product->name,
            'item_description' => $product->description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'item_total' => $quantity * $unitPrice,
            'landed_cost_per_unit' => null, // Will be calculated by LandedCostService
        ];
    }
}