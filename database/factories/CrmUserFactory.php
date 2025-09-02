<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrmUser>
 */
class CrmUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->email,
            'email_verified_at' => now(), // Set email as verified
            
            'password' => bcrypt('password'), // Default password, can be overridden  
            //
        ];
    }
}
