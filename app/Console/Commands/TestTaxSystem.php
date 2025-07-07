<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Setting;
use App\Services\TaxCalculationService;

class TestTaxSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tax-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the tax system with different scenarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando Sistema de IVA...');
        
        // Test 1: Configuraciones por país
        $this->testCountrySettings();
        
        // Test 2: Productos con diferentes tasas
        $this->testProductTaxRates();
        
        // Test 3: Cálculo de costos adicionales
        $this->testAdditionalCosts();
        
        $this->info('✅ Pruebas completadas');
    }
    
    private function testCountrySettings()
    {
        $this->info('📋 Probando configuraciones por país...');
        
        $countries = ['EC', 'ES', 'MX'];
        foreach ($countries as $country) {
            $setting = Setting::where('key', "tax_rates_{$country}")->first();
            if ($setting) {
                $rates = json_decode($setting->value, true);
                $this->info("  {$country}: " . count($rates) . " tasas configuradas");
                foreach ($rates as $rate) {
                    $this->line("    - {$rate['name']}: {$rate['rate']}%");
                }
            } else {
                $this->warn("  {$country}: Sin configuraciones");
            }
        }
    }
    
    private function testProductTaxRates()
    {
        $this->info('📦 Probando productos con diferentes tasas...');
        
        $products = Product::with('taxRate')->take(5)->get();
        
        foreach ($products as $product) {
            $this->info("  Producto: {$product->name}");
            $this->line("    - Precio: $" . number_format($product->price, 2));
            $this->line("    - Paga IVA: " . ($product->is_taxable ? 'Sí' : 'No'));
            $this->line("    - Tasa específica: " . ($product->tax_rate_percentage ?? 'No definida') . "%");
            $this->line("    - Categoría: " . ($product->tax_category ?? 'No definida'));
            $this->line("    - País: " . ($product->tax_country_code ?? 'EC'));
            
            if ($product->taxRate) {
                $this->line("    - Tasa del modelo: {$product->taxRate->name} ({$product->taxRate->rate}%)");
            }
            
            $taxAmount = $product->tax_amount;
            $priceWithTax = $product->price_with_tax;
            $this->line("    - IVA calculado: $" . number_format($taxAmount, 2));
            $this->line("    - Precio con IVA: $" . number_format($priceWithTax, 2));
        }
    }
    
    private function testAdditionalCosts()
    {
        $this->info('💰 Probando cálculo de costos adicionales...');
        
        $taxService = new TaxCalculationService();
        
        $costs = [
            ['category' => 'transport', 'amount' => 100],
            ['category' => 'insurance', 'amount' => 50],
            ['category' => 'storage', 'amount' => 75],
            ['category' => 'transport_public', 'amount' => 25],
        ];
        
        $result = $taxService->calculateAdditionalCostsTax($costs, 'EC');
        
        $this->info("  Costos totales: $" . number_format($result['total_amount'], 2));
        $this->info("  IVA total: $" . number_format($result['total_tax'], 2));
        $this->info("  Total con IVA: $" . number_format($result['total_with_tax'], 2));
        
        foreach ($result['costs'] as $cost) {
            $this->line("    - {$cost['category']}: $" . number_format($cost['amount'], 2) . 
                       " (IVA: " . $cost['tax_rate'] . "%) = $" . number_format($cost['total_with_tax'], 2));
        }
    }
}
