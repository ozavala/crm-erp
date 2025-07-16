<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OwnerCompany;

class OwnerCompanySeeder extends Seeder
{
    public function run()
    {
        // Crea 3 empresas propietarias de ejemplo
        OwnerCompany::factory()->count(3)->create();
    }
}
