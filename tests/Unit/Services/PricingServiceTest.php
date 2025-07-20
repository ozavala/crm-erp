<?php

namespace Tests\Unit\Services;

use App\Services\PricingService;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    private PricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PricingService();
    }

    public function test_calculate_total_price_with_tax_and_discount()
    {
        $result = $this->service->calculateTotalPrice(1000.00, 10.0, 5.0);

        $this->assertEquals(1000.00, $result['subtotal']);
        $this->assertEquals(5.0, $result['discount_rate']);
        $this->assertEquals(50.00, $result['discount_amount']);
        $this->assertEquals(10.0, $result['tax_rate']);
        $this->assertEquals(95.00, $result['tax_amount']); // (1000 - 50) * 0.10
        $this->assertEquals(1045.00, $result['total']); // 950 + 95
    }

    public function test_calculate_total_price_without_tax_or_discount()
    {
        $result = $this->service->calculateTotalPrice(1000.00);

        $this->assertEquals(1000.00, $result['subtotal']);
        $this->assertEquals(0.0, $result['discount_rate']);
        $this->assertEquals(0.00, $result['discount_amount']);
        $this->assertEquals(0.0, $result['tax_rate']);
        $this->assertEquals(0.00, $result['tax_amount']);
        $this->assertEquals(1000.00, $result['total']);
    }

    public function test_calculate_unit_price_with_markup()
    {
        $unitPrice = $this->service->calculateUnitPrice(50.00, 30.0);

        $this->assertEquals(65.00, $unitPrice); // 50 * (1 + 0.30)
    }

    public function test_calculate_profit_margin()
    {
        $margin = $this->service->calculateProfitMargin(100.00, 60.00);

        $this->assertEquals(40.0, $margin); // (100 - 60) / 100 * 100
    }

    public function test_calculate_profit_margin_with_zero_selling_price()
    {
        $margin = $this->service->calculateProfitMargin(0.00, 60.00);

        $this->assertEquals(0.0, $margin);
    }

    public function test_calculate_break_even_quantity()
    {
        $quantity = $this->service->calculateBreakEvenQuantity(1000.00, 100.00, 60.00);

        $this->assertEquals(25, $quantity); // 1000 / (100 - 60) = 25
    }

    public function test_calculate_break_even_quantity_with_negative_contribution()
    {
        $quantity = $this->service->calculateBreakEvenQuantity(1000.00, 50.00, 60.00);

        $this->assertEquals(0, $quantity); // Cannot break even if variable cost > selling price
    }

    public function test_apply_bulk_discount()
    {
        $discountTiers = [
            ['min_quantity' => 10, 'discount_rate' => 5.0],
            ['min_quantity' => 50, 'discount_rate' => 10.0],
            ['min_quantity' => 100, 'discount_rate' => 15.0],
        ];

        // Test quantity 5 (no discount)
        $price1 = $this->service->applyBulkDiscount(100.00, 5, $discountTiers);
        $this->assertEquals(100.00, $price1);

        // Test quantity 25 (5% discount)
        $price2 = $this->service->applyBulkDiscount(100.00, 25, $discountTiers);
        $this->assertEquals(95.00, $price2);

        // Test quantity 75 (10% discount)
        $price3 = $this->service->applyBulkDiscount(100.00, 75, $discountTiers);
        $this->assertEquals(90.00, $price3);

        // Test quantity 150 (15% discount)
        $price4 = $this->service->applyBulkDiscount(100.00, 150, $discountTiers);
        $this->assertEquals(85.00, $price4);
    }

    public function test_edge_cases()
    {
        // Test with zero values
        $result = $this->service->calculateTotalPrice(0.00, 10.0, 5.0);
        $this->assertEquals(0.00, $result['total']);

        // Test with very small values
        $result = $this->service->calculateTotalPrice(0.01, 10.0, 5.0);
        $this->assertEquals(0.01045, $result['total'], '', 0.0001);

        // Test with very large values
        $result = $this->service->calculateTotalPrice(1000000.00, 10.0, 5.0);
        $this->assertEquals(1045000.00, $result['total']);
    }
} 