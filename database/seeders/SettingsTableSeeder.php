<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            ['key' => 'company_name', 'value' => 'CRM-ERP Inc.'],
            ['key' => 'company_address_line_1', 'value' => '123 Innovation Drive'],
            ['key' => 'company_address_line_2', 'value' => 'Suite 456, Tech Park, CA 90210'],
            ['key' => 'company_email', 'value' => 'contact@crm-erp.example.com'],
            ['key' => 'company_phone', 'value' => '+1 (555) 123-4567'],
            ['key' => 'company_logo', 'value' => null],
        ]);

        // Configuraciones de IVA por país
        Setting::create(['key' => 'tax_rates_ecuador', 'value' => json_encode([
            ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
            ['name' => 'IVA 15%', 'rate' => 15.00, 'description' => 'Tasa general de IVA'],
            ['name' => 'IVA 22%', 'rate' => 22.00, 'description' => 'Tasa especial de IVA'],
        ])]);
        
        Setting::create(['key' => 'tax_rates_spain', 'value' => json_encode([
            ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
            ['name' => 'IVA 4%', 'rate' => 4.00, 'description' => 'Tasa superreducida'],
            ['name' => 'IVA 10%', 'rate' => 10.00, 'description' => 'Tasa reducida'],
            ['name' => 'IVA 21%', 'rate' => 21.00, 'description' => 'Tasa general'],
        ])]);
        
        Setting::create(['key' => 'tax_rates_mexico', 'value' => json_encode([
            ['name' => 'IVA 0%', 'rate' => 0.00, 'description' => 'Productos exentos de IVA'],
            ['name' => 'IVA 16%', 'rate' => 16.00, 'description' => 'Tasa general de IVA'],
        ])]);
        
        Setting::create(['key' => 'default_country_tax', 'value' => 'ecuador']);
        Setting::create(['key' => 'tax_includes_services', 'value' => 'true']);
        Setting::create(['key' => 'tax_includes_transport', 'value' => 'false']);
    }
}