<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
    }
}