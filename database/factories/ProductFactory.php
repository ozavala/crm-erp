<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\CrmUser;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $isService = fake()->boolean(30);
        $taxCategories = ['goods', 'services', 'transport', 'insurance', 'storage', 'transport_public'];
        $taxRates = [0, 15, 22]; // Tasas de IVA para Ecuador
        
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->paragraph(),
            'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'cost' => fake()->randomFloat(2, 5, 800),
            'quantity_on_hand' => $isService ? 0 : fake()->numberBetween(0, 100),
            'reorder_point' => $isService ? 0 : fake()->numberBetween(5, 20),
            'is_service' => $isService,
            'is_active' => true,
            'created_by_user_id' => CrmUser::factory(),
            'product_category_id' => ProductCategory::factory(),
            'tax_rate_id' => TaxRate::factory(),
            'is_taxable' => fake()->boolean(80), // 80% de productos pagan IVA
            'tax_rate_percentage' => fake()->randomElement($taxRates),
            'tax_category' => fake()->randomElement($taxCategories),
            'tax_country_code' => 'EC', // Ecuador por defecto
        ];
    }
}