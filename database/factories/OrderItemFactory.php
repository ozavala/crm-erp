<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Order;
use App\Models\CrmUser;
use App\Models\OrderItem;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            $product= Product::inRandomOrder()->first() ?? Product::factory(),
            
            'quantity' => $this->faker->number(),
            'unit_price' => $this->faker->randomFloat(2, 1, 1000),
            'item_total' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_price'];
            },
            'item_name' => $this->faker->word,
            'item_description' => $this->faker->sentence,
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'created_by_user_id' => CrmUser::factory(),
            'updated_by_user_id' => CrmUser::factory(),
            'is_service' => $this->faker->boolean(20), // 20%
            'is_taxable' => $this->faker->boolean(80), // 80% chance of being taxable
            'tax_percentage' => $this->faker->randomElement([0, 5, 10, 15, 20]),
            'tax_amount' =>     function (array $attributes) {
                return ($attributes['is_taxable'] ? $attributes['item_total'] * ($attributes['tax_percentage'] / 100) : 0);
            },
            'discount_percentage' => $this->faker->randomFloat(2, 0, 50), // Up to 50% discount
            'discount_amount' => function (array $attributes) {
                return $attributes['item_total'] * ($attributes['discount_percentage'] / 100);
            },
            'total_price' => function (array $attributes) {
                return $attributes['item_total'] - $attributes['discount_amount'] + $attributes['tax_amount'
        ];
            },
        ];  
    }
}
