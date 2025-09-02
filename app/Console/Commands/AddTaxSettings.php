<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class AddTaxSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:tax-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add tax settings for different countries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📋 Agregando configuraciones de IVA...');
        
        $taxSettings = [
            'tax_rates_ecuador' => [
                ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
                ['name' => 'IVA 15%', 'rate' => 15.00, 'description' => 'Tasa general de IVA'],
                ['name' => 'IVA 22%', 'rate' => 22.00, 'description' => 'Tasa especial de IVA'],
            ],
            'tax_rates_spain' => [
                ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
                ['name' => 'IVA 4%', 'rate' => 4.00, 'description' => 'Tasa superreducida'],
                ['name' => 'IVA 10%', 'rate' => 10.00, 'description' => 'Tasa reducida'],
                ['name' => 'IVA 21%', 'rate' => 21.00, 'description' => 'Tasa general'],
            ],
            'tax_rates_mexico' => [
                ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
                ['name' => 'IVA 16%', 'rate' => 16.00, 'description' => 'Tasa general de IVA'],
            ],
            'default_country_tax' => 'ecuador',
            'tax_includes_services' => 'true',
            'tax_includes_transport' => 'false',
        ];
        
        foreach ($taxSettings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                Setting::create([
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'type' => 'custom',
                ]);
                $this->info("  ✅ Creado: {$key}");
            } else {
                $this->warn("  ⚠️  Ya existe: {$key}");
            }
        }
        
        $this->info('✅ Configuraciones de IVA agregadas correctamente');
    }
}
