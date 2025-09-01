<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CrmUser;
use Illuminate\Database\Eloquent\Factories\Factory;
//use Database\Factories
use App\Models\Customer;
use App\Models\Supplier;

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
        $contactableType = $this->faker->randomElement([Customer::class, Supplier::class]);
        $contactable = $contactableType::factory()->create();

        return [
            'contactable_id' => $contactable->id,
            'contactable_type' => $contactableType,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'title' => $this->faker->jobTitle(),
            'created_by_user_id' => CrmUser::inRandomOrder()->first()->user_id ?? CrmUser::factory(),
        ];
    }
}