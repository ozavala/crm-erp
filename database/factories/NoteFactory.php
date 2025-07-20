<?php

namespace Database\Factories;

use App\Models\CrmUser;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => $this->faker->paragraph,
            'noteable_id' => function (array $attributes) {
                // Ensure a Customer is created and its ID is used
                return Customer::factory()->create()->customer_id;
            },
            'noteable_type' => Customer::class,
            'created_by_user_id' => function (array $attributes) {
                // Ensure a CrmUser is created and its ID is used
                return CrmUser::factory()->create()->user_id;
            },
        ];
    }
}