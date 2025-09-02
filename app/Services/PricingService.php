<?php

namespace App\Services;

class PricingService
{
    /**
     * Calculate the total price including tax and discounts
     */
    public function calculateTotalPrice(float $subtotal, float $taxRate = 0, float $discountRate = 0): array
    {
        $discountAmount = $subtotal * ($discountRate / 100);
        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $taxableAmount * ($taxRate / 100);
        $total = $taxableAmount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'discount_rate' => $discountRate,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }

    /**
     * Calculate the unit price with markup
     */
    public function calculateUnitPrice(float $cost, float $markupPercentage): float
    {
        return $cost * (1 + ($markupPercentage / 100));
    }

    /**
     * Calculate profit margin
     */
    public function calculateProfitMargin(float $sellingPrice, float $cost): float
    {
        if ($sellingPrice <= 0) {
            return 0;
        }

        return (($sellingPrice - $cost) / $sellingPrice) * 100;
    }

    /**
     * Calculate break-even quantity
     */
    public function calculateBreakEvenQuantity(float $fixedCosts, float $sellingPrice, float $variableCostPerUnit): int
    {
        $contributionMargin = $sellingPrice - $variableCostPerUnit;
        
        if ($contributionMargin <= 0) {
            return 0; // Cannot break even if contribution margin is negative
        }

        return (int) ceil($fixedCosts / $contributionMargin);
    }

    /**
     * Apply bulk discount based on quantity
     */
    public function applyBulkDiscount(float $unitPrice, int $quantity, array $discountTiers): float
    {
        $discountRate = 0;
        
        foreach ($discountTiers as $tier) {
            if ($quantity >= $tier['min_quantity']) {
                $discountRate = $tier['discount_rate'];
            }
        }

        return $unitPrice * (1 - ($discountRate / 100));
    }
} 