<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\OwnerCompany;
use Carbon\Carbon;

class TaxBalanceReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get owner companies or create a default one if none exist
        $ownerCompanies = OwnerCompany::all();
        if ($ownerCompanies->isEmpty()) {
            $ownerCompanies = collect([OwnerCompany::factory()->create()]);
        }
        
        // Crear tasas de impuesto si no existen
        $taxRates = [
            ['name' => 'IVA 12%', 'rate' => 12.00, 'country_code' => 'EC', 'is_default' => true],
            ['name' => 'IVA 0%', 'rate' => 0.00, 'country_code' => 'EC', 'is_default' => false],
            ['name' => 'IVA 14%', 'rate' => 14.00, 'country_code' => 'EC', 'is_default' => false],
        ];

        foreach ($taxRates as $taxRateData) {
            TaxRate::firstOrCreate(
                ['name' => $taxRateData['name']],
                $taxRateData
            );
        }

        // Obtener clientes y proveedores existentes
        $customers = Customer::take(5)->get();
        $suppliers = Supplier::take(5)->get();
        $products = Product::take(10)->get();

        if ($customers->isEmpty() || $suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No hay suficientes clientes, proveedores o productos para generar datos de prueba.');
            return;
        }

        // Generar facturas (ventas) con impuestos
        $this->generateInvoices($customers, $products);

        // Generar facturas de proveedores (compras) con impuestos
        $this->generateBills($suppliers, $products);

        $this->command->info('Datos de prueba para el reporte de balance de impuestos generados exitosamente.');
    }

    private function generateInvoices($customers, $products)
    {
        $taxRates = TaxRate::all();
        $ownerCompanyId = OwnerCompany::first()->id;
        
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $taxRate = $taxRates->random();
            
            $subtotal = rand(1000, 50000) / 100; // Entre $10.00 y $500.00
            $taxAmount = $subtotal * ($taxRate->rate / 100);
            $totalAmount = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'customer_id' => $customer->customer_id,
                'invoice_number' => 'INV-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'invoice_date' => Carbon::now()->subDays(rand(1, 30)),
                'due_date' => Carbon::now()->addDays(30),
                'status' => 'Sent',
                'subtotal' => $subtotal,
                'tax_percentage' => $taxRate->rate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'created_by_user_id' => 1,
                'tax_rate_id' => $taxRate->tax_rate_id,
                'owner_company_id' => $ownerCompanyId,
            ]);

            // Crear items de factura
            $product = $products->random();
            $quantity = rand(1, 5);
            $unitPrice = $subtotal / $quantity;

            $invoice->items()->create([
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'item_total' => $subtotal,
            ]);
        }
    }

    private function generateBills($suppliers, $products)
    {
        $ownerCompanyId = OwnerCompany::first()->id;
        
        for ($i = 0; $i < 15; $i++) {
            $supplier = $suppliers->random();
            $taxPercentage = rand(0, 14); // 0%, 12%, o 14%
            
            $subtotal = rand(500, 30000) / 100; // Entre $5.00 y $300.00
            $taxAmount = $subtotal * ($taxPercentage / 100);
            $totalAmount = $subtotal + $taxAmount;

            // Crear orden de compra primero
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $supplier->supplier_id,
                'purchase_order_number' => 'PO-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'order_date' => Carbon::now()->subDays(rand(1, 30)),
                'expected_delivery_date' => Carbon::now()->addDays(15),
                'type' => 'Standard',
                'status' => 'confirmed',
                'subtotal' => $subtotal,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'created_by_user_id' => 1,
                'owner_company_id' => $ownerCompanyId,
            ]);

            // Crear items de orden de compra
            $product = $products->random();
            $quantity = rand(1, 10);
            $unitPrice = $subtotal / $quantity;

            $purchaseOrder->items()->create([
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'item_total' => $subtotal,
            ]);

            // Crear factura de proveedor
            $bill = Bill::create([
                'purchase_order_id' => $purchaseOrder->purchase_order_id,
                'supplier_id' => $supplier->supplier_id,
                'bill_number' => 'BILL-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'bill_date' => Carbon::now()->subDays(rand(1, 30)),
                'due_date' => Carbon::now()->addDays(30),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'status' => 'Awaiting Payment',
                'created_by_user_id' => 1,
                'owner_company_id' => $ownerCompanyId,
            ]);

            // Crear items de factura de proveedor
            $bill->items()->create([
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'item_total' => $subtotal,
            ]);
        }
    }
} 