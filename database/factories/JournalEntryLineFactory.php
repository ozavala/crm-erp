<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntryLine>
 */
class JournalEntryLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 5000);
        $isDebit = $this->faker->boolean();
        
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'account_code' => $this->faker->randomElement(['1101', '2101', '2102', '3101', '4101']),
            'account_name' => $this->faker->randomElement([
                'Accounts Receivable',
                'Accounts Payable', 
                'Cash',
                'Bank',
                'Sales Revenue',
                'Cost of Goods Sold',
                'Inventory',
                'Tax Payable',
                'Tax Receivable'
            ]),
            'debit_amount' => $isDebit ? $amount : 0,
            'credit_amount' => $isDebit ? 0 : $amount,
            'entity_type' => $this->faker->optional()->randomElement(['App\Models\Customer', 'App\Models\Supplier']),
            'entity_id' => $this->faker->optional()->numberBetween(1, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that this is a debit line.
     */
    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'debit_amount' => $this->faker->randomFloat(2, 100, 5000),
            'credit_amount' => 0,
        ]);
    }

    /**
     * Indicate that this is a credit line.
     */
    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'debit_amount' => 0,
            'credit_amount' => $this->faker->randomFloat(2, 100, 5000),
        ]);
    }
} 