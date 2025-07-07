<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ReportDataSeeder;

class SeedReportData extends Command
{
    protected $signature = 'seed:reports {--fresh : Run fresh migrations first}';
    protected $description = 'Seed the database with comprehensive report data';

    public function handle(): int
    {
        $this->info('🌱 Seeding report data...');

        if ($this->option('fresh')) {
            $this->info('Running fresh migrations...');
            $this->call('migrate:fresh');
        }

        // Ejecutar el seeder de reportes
        $this->call('db:seed', ['--class' => ReportDataSeeder::class]);

        $this->info('✅ Report data seeded successfully!');
        $this->info('');
        $this->info('📊 Generated data includes:');
        $this->info('   • 8 Product Categories');
        $this->info('   • 25 Products with realistic pricing');
        $this->info('   • 10 Customers with contact information');
        $this->info('   • 8 Suppliers');
        $this->info('   • 5 Sales Users');
        $this->info('   • 15 Quotations with various statuses');
        $this->info('   • 20 Purchase Orders with realistic workflows');
        $this->info('   • 50 Orders with different statuses');
        $this->info('   • Multiple Invoices with payment scenarios');
        $this->info('   • Realistic payment data');
        $this->info('   • Inventory stock across 3 warehouses');
        $this->info('');
        $this->info('🎯 You can now test all 5 report types:');
        $this->info('   • Sales Report');
        $this->info('   • Sales by Category');
        $this->info('   • Sales by Customer');
        $this->info('   • Sales by Employee');
        $this->info('   • Sales by Product');

        return Command::SUCCESS;
    }
} 