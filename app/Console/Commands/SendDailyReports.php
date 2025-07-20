<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Services\ReportingService;
use Carbon\Carbon;

class SendDailyReports extends Command
{
    protected $signature = 'reports:daily {--email=}';
    protected $description = 'Send daily reports to administrators';

    public function handle(NotificationService $notificationService, ReportingService $reportingService)
    {
        $this->info('Generating daily reports...');

        $yesterday = Carbon::yesterday();
        $today = Carbon::today();

        // Generar reportes
        $salesReport = $reportingService->getSalesReport($yesterday, $today);
        $inventoryReport = $reportingService->getInventoryReport();
        $cashFlowReport = $reportingService->getCashFlowReport($yesterday, $today);

        $this->info('Reports generated successfully');
        $this->info("Sales: {$salesReport['summary']['total_sales']}");
        $this->info("Orders: {$salesReport['summary']['total_orders']}");
        $this->info("Low stock products: {$inventoryReport['low_stock_products']}");

        // Enviar reporte diario
        $notificationService->sendDailyReport();

        $this->info('Daily reports sent successfully!');
    }
} 