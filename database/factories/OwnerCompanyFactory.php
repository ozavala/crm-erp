<?php

namespace Database\Factories;

use App\Models\OwnerCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class OwnerCompanyFactory extends Factory
{
    protected $model = OwnerCompany::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'legal_id' => $this->faker->unique()->bothify('##-########-001'),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'industry' => $this->faker->word,
        ];
    }
}
