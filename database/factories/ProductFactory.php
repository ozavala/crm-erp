<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\CrmUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $isService = $this->faker->boolean();

        return [
            'name' => $this->faker->unique()->words(2, true) . ' ' . $this->faker->word(),
            'description' => $this->faker->sentence(),
            'sku' => $this->faker->unique()->ean8(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost' => $this->faker->randomFloat(2, 5, 500),
            'quantity_on_hand' => $isService ? 0 : $this->faker->numberBetween(0, 500),
            'is_service' => $isService,
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'product_category_id' => ProductCategory::factory(),
            'created_by_user_id' => CrmUser::factory(),
        ];
    }
}