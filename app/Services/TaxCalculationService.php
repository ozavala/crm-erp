<?php

namespace App\Services;

use App\Models\TaxRate;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\PurchaseOrder;

class TaxCalculationService
{
    /**
     * Calculate tax for a product based on its tax rate.
     */
    public function calculateProductTax(Product $product, float $quantity = 1): array
    {
        $subtotal = $product->price * $quantity;
        $taxRate = $product->taxRate;
        
        if (!$taxRate) {
            return [
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total' => $subtotal,
                'tax_rate' => null,
            ];
        }

        $taxAmount = $taxRate->calculateTaxAmount($subtotal);
        $total = $taxRate->calculateTotalWithTax($subtotal);

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate tax for an invoice based on its items.
     */
    public function calculateInvoiceTax(Invoice $invoice): array
    {
        $subtotal = 0;
        $taxAmount = 0;
        $taxRate = $invoice->taxRate;

        // Calculate subtotal from items
        foreach ($invoice->items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
        }

        // Apply discount if any
        if ($invoice->discount_amount > 0) {
            $subtotal -= $invoice->discount_amount;
        }

        // Calculate tax
        if ($taxRate) {
            $taxAmount = $taxRate->calculateTaxAmount($subtotal);
        }

        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate tax for a quotation based on its items.
     */
    public function calculateQuotationTax(Quotation $quotation): array
    {
        $subtotal = 0;
        $taxAmount = 0;
        $taxRate = $quotation->taxRate;

        // Calculate subtotal from items
        foreach ($quotation->items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
        }

        // Apply discount if any
        if ($quotation->discount_amount > 0) {
            $subtotal -= $quotation->discount_amount;
        }

        // Calculate tax
        if ($taxRate) {
            $taxAmount = $taxRate->calculateTaxAmount($subtotal);
        }

        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate tax for a purchase order based on its items.
     */
    public function calculatePurchaseOrderTax(PurchaseOrder $purchaseOrder): array
    {
        $subtotal = 0;
        $taxAmount = 0;
        $taxRate = $purchaseOrder->taxRate;

        // Calculate subtotal from items
        foreach ($purchaseOrder->items as $item) {
            $subtotal += $item->quantity * $item->unit_cost;
        }

        // Apply discount if any
        if ($purchaseOrder->discount_amount > 0) {
            $subtotal -= $purchaseOrder->discount_amount;
        }

        // Calculate tax
        if ($taxRate) {
            $taxAmount = $taxRate->calculateTaxAmount($subtotal);
        }

        $total = $subtotal + $taxAmount + $purchaseOrder->shipping_cost + $purchaseOrder->other_charges;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Get default tax rate for a country.
     */
    public function getDefaultTaxRate(string $countryCode = 'ES'): ?TaxRate
    {
        return TaxRate::getDefaultForCountry($countryCode);
    }

    /**
     * Get all active tax rates for a country.
     */
    public function getActiveTaxRates(string $countryCode = 'ES'): \Illuminate\Database\Eloquent\Collection
    {
        return TaxRate::getActiveForCountry($countryCode);
    }

    /**
     * Auto-assign tax rate to a transaction based on products.
     */
    public function autoAssignTaxRate($transaction): void
    {
        if ($transaction->tax_rate_id) {
            return; // Already assigned
        }

        // Get the most common tax rate from items
        $taxRates = collect();
        
        if (method_exists($transaction, 'items')) {
            foreach ($transaction->items as $item) {
                if ($item->product && $item->product->taxRate) {
                    $taxRates->push($item->product->taxRate);
                }
            }
        }

        if ($taxRates->isNotEmpty()) {
            $mostCommonTaxRate = $taxRates->countBy('tax_rate_id')->sortDesc()->keys()->first();
            $transaction->tax_rate_id = $mostCommonTaxRate;
            $transaction->save();
        } else {
            // Assign default tax rate
            $defaultTaxRate = $this->getDefaultTaxRate();
            if ($defaultTaxRate) {
                $transaction->tax_rate_id = $defaultTaxRate->tax_rate_id;
                $transaction->save();
            }
        }
    }

    /**
     * Calculate tax for additional costs (transport, insurance, storage, etc.)
     */
    public function calculateAdditionalCostsTax(array $costs, string $countryCode = 'EC'): array
    {
        $totalTax = 0;
        $taxableCosts = [];
        
        foreach ($costs as $cost) {
            $category = $cost['category'] ?? 'services';
            $amount = $cost['amount'] ?? 0;
            
            // Determinar si el costo es imponible según la categoría
            $isTaxable = $this->isCostCategoryTaxable($category, $countryCode);
            $taxRate = $this->getTaxRateForCategory($category, $countryCode);
            
            if ($isTaxable && $taxRate > 0) {
                $taxAmount = $amount * ($taxRate / 100);
                $totalTax += $taxAmount;
                
                $taxableCosts[] = [
                    'category' => $category,
                    'amount' => $amount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total_with_tax' => $amount + $taxAmount,
                ];
            } else {
                $taxableCosts[] = [
                    'category' => $category,
                    'amount' => $amount,
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'total_with_tax' => $amount,
                ];
            }
        }
        
        return [
            'costs' => $taxableCosts,
            'total_amount' => array_sum(array_column($costs, 'amount')),
            'total_tax' => $totalTax,
            'total_with_tax' => array_sum(array_column($costs, 'amount')) + $totalTax,
        ];
    }
    
    /**
     * Check if a cost category is taxable in the given country
     */
    private function isCostCategoryTaxable(string $category, string $countryCode): bool
    {
        $nonTaxableCategories = ['transport_public'];
        
        if (in_array($category, $nonTaxableCategories)) {
            return false;
        }
        
        // Verificar configuración específica del país
        $setting = \App\Models\Setting::where('key', "tax_includes_{$category}")->first();
        if ($setting) {
            return (bool) $setting->value;
        }
        
        return true; // Por defecto, los costos son imponibles
    }
    
    /**
     * Get tax rate for a specific cost category
     */
    private function getTaxRateForCategory(string $category, string $countryCode): float
    {
        // Tasas específicas por categoría y país
        $categoryRates = [
            'EC' => [
                'transport' => 15.0,
                'insurance' => 22.0,
                'storage' => 22.0,
                'services' => 15.0,
                'goods' => 15.0,
            ],
            'ES' => [
                'transport' => 21.0,
                'insurance' => 21.0,
                'storage' => 21.0,
                'services' => 21.0,
                'goods' => 21.0,
            ],
        ];
        
        return $categoryRates[$countryCode][$category] ?? 15.0; // Default 15%
    }
} 