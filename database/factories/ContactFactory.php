<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CrmUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'title' => $this->faker->jobTitle(),
            'created_by_user_id' => CrmUser::inRandomOrder()->first()->user_id ?? CrmUser::factory(),
        ];
    }
}