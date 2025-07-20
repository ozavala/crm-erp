<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entry_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'reference_number' => 'JE-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(),
            'created_by_user_id' => \App\Models\CrmUser::factory(),
            'transaction_type' => $this->faker->randomElement(['payment', 'invoice', 'bill', 'adjustment']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the journal entry is posted.
     */
    public function posted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'posted',
        ]);
    }

    /**
     * Indicate that the journal entry is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
} 