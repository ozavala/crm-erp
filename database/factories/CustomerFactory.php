<?php
// This file is part of a Laravel application.
// database/factories/CustomerFactory.php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\{Customer, User,Address};
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstname(),
            'last_name' => $this->faker->lastname(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'company_name' => $this->faker->company(),
            'address_street' => $this->faker->streetAddress(),
            'address_city' => $this->faker->city(),
            'address_state' => $this->faker->state(),
            'address_postal_code' => $this->faker->postcode(),
            'status' => $this->faker->randomElement(['lead', 'customer', 'vip']),
            'notes' => $this->faker->paragraph(),
            'created_by_user_id' => User::factory(),

            Address::create([
                'addressable_id' => Customer::factory(),
                'addressable_type' => Customer::class,
                'address_type' => $this->faker->randomElement(['Primary', 'Billing', 'Shipping']),
                'street_address_line_1' => $this->faker->streetAddress(),
                'street_address_line_2' => $this->faker->optional()->secondaryAddress(),
                'city' => $this->faker->city(),
                'state_province' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country_code' => $this->faker->countryCode(),
                'is_primary' => $this->faker->boolean(80), // 80% chance of being primary
            ]),
           
            
        ];
    }
}

