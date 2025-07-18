<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventoryService();
    }

    public function test_has_sufficient_stock_with_warehouse()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 50]);

        $result = $this->service->hasSufficientStock($product->product_id, 30, $warehouse->warehouse_id);

        $this->assertTrue($result);
    }

    public function test_has_insufficient_stock_with_warehouse()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 20]);

        $result = $this->service->hasSufficientStock($product->product_id, 30, $warehouse->warehouse_id);

        $this->assertFalse($result);
    }

    public function test_has_sufficient_stock_across_all_warehouses()
    {
        $product = Product::factory()->create();
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse1->warehouse_id, ['quantity' => 30]);
        $product->warehouses()->attach($warehouse2->warehouse_id, ['quantity' => 40]);

        $result = $this->service->hasSufficientStock($product->product_id, 50);

        $this->assertTrue($result);
    }

    public function test_has_insufficient_stock_across_all_warehouses()
    {
        $product = Product::factory()->create();
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse1->warehouse_id, ['quantity' => 20]);
        $product->warehouses()->attach($warehouse2->warehouse_id, ['quantity' => 25]);

        $result = $this->service->hasSufficientStock($product->product_id, 50);

        $this->assertFalse($result);
    }

    public function test_reserve_stock_successfully()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 100]);

        $result = $this->service->reserveStock($product->product_id, 30, $warehouse->warehouse_id);

        $this->assertTrue($result);
        
        $stock = DB::table('inventories')
            ->where('product_id', $product->product_id)
            ->where('warehouse_id', $warehouse->warehouse_id)
            ->value('quantity');
        $this->assertEquals(70, $stock);
    }

    public function test_reserve_stock_fails_insufficient_quantity()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        
        $product->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 20]);

        $result = $this->service->reserveStock($product->product_id, 30, $warehouse->warehouse_id);

        $this->assertFalse($result);
        
        $stock = DB::table('inventories')
            ->where('product_id', $product->product_id)
            ->where('warehouse_id', $warehouse->warehouse_id)
            ->value('quantity');
        $this->assertEquals(20, $stock); // Stock unchanged
    }

    public function test_get_low_stock_alerts()
    {
        $product1 = Product::factory()->create(['is_active' => true]);
        $product2 = Product::factory()->create(['is_active' => true]);
        $product3 = Product::factory()->create(['is_active' => false]);
        
        $warehouse = Warehouse::factory()->create();
        
        $product1->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 5]); // Low stock
        $product2->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 15]); // Above threshold
        $product3->warehouses()->attach($warehouse->warehouse_id, ['quantity' => 3]); // Inactive product

        $alerts = $this->service->getLowStockAlerts(10);

        $this->assertCount(1, $alerts);
        $this->assertEquals($product1->product_id, $alerts[0]['product_id']);
        $this->assertEquals(5, $alerts[0]['current_stock']);
        $this->assertEquals(10, $alerts[0]['threshold']);
    }

    public function test_calculate_reorder_point()
    {
        $product = Product::factory()->create();
        
        $reorderPoint = $this->service->calculateReorderPoint($product->product_id);

        // Based on the mock calculation: (5 * 7) + 10 = 45
        $this->assertEquals(45, $reorderPoint);
    }

    public function test_calculate_reorder_point_nonexistent_product()
    {
        $reorderPoint = $this->service->calculateReorderPoint(999);

        $this->assertEquals(0, $reorderPoint);
    }

    public function test_get_stock_movement_summary()
    {
        $product = Product::factory()->create();
        
        $summary = $this->service->getStockMovementSummary($product->product_id, 30);

        $this->assertEquals($product->product_id, $summary['product_id']);
        $this->assertEquals(30, $summary['period_days']);
        $this->assertEquals(0, $summary['total_in']);
        $this->assertEquals(0, $summary['total_out']);
        $this->assertEquals(0, $summary['net_movement']);
        $this->assertEquals(0, $summary['average_daily_movement']);
    }

    public function test_update_product_cost()
    {
        $product = Product::factory()->create([
            'cost' => 50.00,
            'quantity_on_hand' => 100,
        ]);

        $this->service->updateProductCost($product->product_id, 60.00, 50);

        $product->refresh();
        
        // Weighted average: (100 * 50 + 50 * 60) / 150 = 53.33
        $this->assertEquals(53.33, $product->cost, '', 0.01);
        $this->assertEquals(150, $product->quantity_on_hand);
    }

    public function test_update_product_cost_with_zero_quantity()
    {
        $product = Product::factory()->create([
            'cost' => 50.00,
            'quantity_on_hand' => 0,
        ]);

        $this->service->updateProductCost($product->product_id, 60.00, 0);

        $product->refresh();
        
        $this->assertEquals(60.00, $product->cost);
        $this->assertEquals(0, $product->quantity_on_hand);
    }

    public function test_update_product_cost_nonexistent_product()
    {
        // Should not throw exception
        $this->service->updateProductCost(999, 60.00, 50);
        
        $this->assertTrue(true); // Method completed without error
    }
} 