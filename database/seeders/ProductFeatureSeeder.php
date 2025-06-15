<?php

namespace Database\Seeders;

use App\Models\ProductFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductFeature::create([
            'name' => 'Color',
            'description' => 'Product color',
        ]);
        ProductFeature::create([
            'name' => 'Size',
            'description' => 'Product dimensions or apparel size',
        ]);
        ProductFeature::create([
            'name' => 'Material',
            'description' => 'Main material of the product',
        ]);
        ProductFeature::create([
            'name' => 'Storage Capacity',
            'description' => 'Storage capacity for electronic devices',
        ]);
        ProductFeature::create([
            'name' => 'RAM', // Example: For electronics
            'description' => 'Random Access Memory for electronic devices',
        ]);
        ProductFeature::create([
            'name' => 'Battery Life',
            'description' => 'Expected battery life for electronic devices',
        ]);
        ProductFeature::create([
            'name' => 'Warranty Period',
            'description' => 'Warranty period for the product',
        ]);
        ProductFeature::create([
            'name' => 'Weight',
            'description' => 'Weight of the product',
        ]);
        ProductFeature::create([
            'name' => 'Height',
            'description' => 'Height of the product',
        ]);
    }
}
