<?php

namespace Database\Factories;

use App\Models\CrmUser;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['Person', 'Company']);

        $data = [
            'type' => $type,
            'legal_id' => $this->faker->unique()->bothify('??-#######-#'), // e.g., AB-1234567-8
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Lead', 'Prospect']),
            'created_by_user_id' => CrmUser::inRandomOrder()->first()->user_id ?? CrmUser::factory(),
            'first_name' => $type === 'Person' ? $this->faker->firstName : null,
            'last_name' => $type === 'Person' ? $this->faker->lastName : null,
            'company_name' => $type === 'Company' ? $this->faker->company : null,
        ];

        return $data;
    }
}