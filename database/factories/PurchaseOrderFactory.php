<?php

namespace Database\Factories;

use App\Models\CrmUser;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $tax = $subtotal * 0.10; // Example 10% tax
        $total = $subtotal + $tax;

        return [
            'supplier_id' => Supplier::factory(),
            'purchase_order_number' => 'PO-' . $this->faker->unique()->numerify('######'),
            'order_date' => $this->faker->date(),
            'expected_delivery_date' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'status' => $this->faker->randomElement(array_keys(PurchaseOrder::$statuses)),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'amount_paid' => 0.00,
            'notes' => $this->faker->optional()->sentence,
            'created_by_user_id' => CrmUser::factory(),
        ];
    }
}