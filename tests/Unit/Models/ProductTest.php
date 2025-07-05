<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category()
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->category_id]);
        $this->assertEquals($category->category_id, $product->category->category_id);
    }

    public function test_product_can_be_in_multiple_warehouses()
    {
        $product = Product::factory()->create();
        $warehouses = Warehouse::factory()->count(2)->create();
        $product->warehouses()->attach($warehouses->pluck('warehouse_id'));
        $this->assertCount(2, $product->warehouses);
    }
} 