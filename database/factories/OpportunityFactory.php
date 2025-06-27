<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CrmUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'lead_id' => Lead::factory(),
            'customer_id' => Customer::factory(),
            'contact_id' => function (array $attributes) {
                // Create a contact that belongs to the same customer as the opportunity, using the polymorphic relationship
                return Contact::factory()->create([
                    'contactable_id' => $attributes['customer_id'],
                    'contactable_type' => Customer::class,
                ]);
            },
            'stage' => $this->faker->randomElement(array_keys(Opportunity::$stages)),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'expected_close_date' => $this->faker->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'probability' => $this->faker->numberBetween(5, 95),
            'assigned_to_user_id' => CrmUser::factory(),
            'created_by_user_id' => CrmUser::factory(),
        ];
    }
}
