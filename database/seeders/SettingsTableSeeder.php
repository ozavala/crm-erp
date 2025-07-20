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
        // Core settings
        $coreSettings = [
            ['key' => 'company_name', 'value' => 'Ingeconsersa SA', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_legal_id', 'value' => '0992793747-001', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_address_line_1', 'value' => '123 Innovation Drive', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_address_line_2', 'value' => 'Suite 456, Tech Park, CA 90210', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_email', 'value' => 'contact@crm-erp.example.com', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_phone', 'value' => '+1 (555) 123-4567', 'type' => 'core', 'is_editable' => false],
            ['key' => 'company_logo', 'value' => null, 'type' => 'core', 'is_editable' => false],
            ['key' => 'default_locale', 'value' => 'es', 'type' => 'core', 'is_editable' => false],
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'core', 'is_editable' => false],
            ['key' => 'tax_includes_services', 'value' => 'true', 'type' => 'core', 'is_editable' => false],
            ['key' => 'tax_includes_transport', 'value' => 'false', 'type' => 'core', 'is_editable' => false],
        ];
        foreach ($coreSettings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Custom settings de ejemplo
        \App\Models\Setting::updateOrCreate(
            ['key' => 'custom_message'],
            [
                'key' => 'custom_message',
                'value' => 'Bienvenido al sistema',
                'type' => 'custom',
                'is_editable' => true,
            ]
        );
    }
}