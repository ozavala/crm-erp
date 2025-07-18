<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CrmUser;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
<<<<<<< HEAD
            'invoice_number' => 'INV-' . fake()->unique()->numberBetween(100000, 99999999),
=======
            'owner_company_id' => \App\Models\OwnerCompany::factory(),
            'invoice_number' => 'INV-' . fake()->unique()->numberBetween(1000, 9999),
>>>>>>> cd8ff788ab38f6404d3b9eff7ea7da045bbe4635
            'customer_id' => Customer::factory(),
            'created_by_user_id' => CrmUser::factory(),
            'invoice_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'subtotal' => fake()->randomFloat(2, 100, 10000),
            'tax_amount' => fake()->randomFloat(2, 10, 1000),
            'discount_amount' => fake()->randomFloat(2, 0, 500),
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Indicate that the invoice is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the invoice is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
} 