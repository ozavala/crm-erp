<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can seed the product_categories table with some initial data here.
        // For example, you might want to create a few categories.

        \App\Models\ProductCategory::create([
            'name' => 'Electronics',
            'description' => 'Devices and gadgets',
        ]);

        \App\Models\ProductCategory::create([
            'name' => 'Furniture',
            'description' => 'Home and office furniture',
        ]);

        \App\Models\ProductCategory::create([
            'name' => 'Clothing',
            'description' => 'Apparel and accessories',
        ]);
        
        // Add more categories as needed
        \App\Models\ProductCategory::create([
            'name' => 'TShirts',
            'description' => 'Printed and digital books',
            'parent_category_id' => 3, // Assuming this is a top-level category
        ]);
    }
}
