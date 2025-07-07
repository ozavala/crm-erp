<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Product;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Check for products with low stock and send alerts';

    public function handle(NotificationService $notificationService)
    {
        $this->info('Checking for low stock products...');

        $lowStockProducts = Product::whereHas('warehouses', function ($query) {
            $query->whereRaw('quantity <= reorder_point');
        })->with(['warehouses'])->get();

        $count = $lowStockProducts->count();
        $this->info("Found {$count} products with low stock");

        foreach ($lowStockProducts as $product) {
            $this->line("- {$product->name} (SKU: {$product->sku})");
            
            foreach ($product->warehouses as $warehouse) {
                if ($warehouse->pivot->quantity <= ($product->reorder_point ?? 10)) {
                    $this->line("  Warehouse: {$warehouse->name} - Stock: {$warehouse->pivot->quantity}");
                }
            }

            // Enviar alerta
            $notificationService->sendLowStockAlert($product);
        }

        $this->info('Low stock alerts sent successfully!');
    }
} 