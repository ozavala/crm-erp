<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\User; // Assuming you have a User model for created_by
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bill = \App\Models\Bill::factory()->create();
        return [
            'payable_id' => $bill->bill_id,
            'owner_company_id' => \App\Models\OwnerCompany::factory(),
            'payable_type' => \App\Models\Bill::class,
            'created_by_user_id' => \App\Models\User::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_date' => $this->faker->date(),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card', 'cheque']),
            'reference_number' => $this->faker->optional()->bothify('REF-########-??????'),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}