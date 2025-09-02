<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Carbon\Carbon;

class TaxPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Generando pagos por compras con impuestos...');

        // Obtener bills existentes que tengan impuestos
        $bills = Bill::where('tax_amount', '>', 0)
            ->where('status', '!=', 'Paid')
            ->with(['purchaseOrder', 'supplier'])
            ->get();

        if ($bills->isEmpty()) {
            $this->command->warn('No hay bills con impuestos para generar pagos.');
            return;
        }

        $paymentCount = 0;

        foreach ($bills as $bill) {
            // Generar 1-3 pagos por bill
            $numPayments = rand(1, 3);
            $remainingAmount = $bill->total_amount;
            
            for ($i = 0; $i < $numPayments && $remainingAmount > 0; $i++) {
                // Calcular monto del pago (puede ser parcial o total)
                $paymentAmount = $i === $numPayments - 1 
                    ? $remainingAmount 
                    : min($remainingAmount * 0.6, $remainingAmount); // 60% del restante o el total
                
                $paymentAmount = round($paymentAmount, 2);
                
                if ($paymentAmount <= 0) break;

                // Crear el pago
                $payment = Payment::create([
                    'payable_type' => Bill::class,
                    'payable_id' => $bill->bill_id,
                    'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                    'amount' => $paymentAmount,
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'reference_number' => 'PAY-' . str_pad($paymentCount + 1, 6, '0', STR_PAD_LEFT),
                    'notes' => "Pago por factura {$bill->bill_number}",
                    'created_by_user_id' => 1,
                ]);

                $remainingAmount -= $paymentAmount;
                $paymentCount++;

                $this->command->info("✓ Pago creado: {$payment->reference_number} - \${$paymentAmount} para Bill {$bill->bill_number}");
            }

            // Actualizar el estado del bill después de los pagos
            $totalPaid = $bill->payments()->sum('amount');
            $bill->amount_paid = $totalPaid;
            
            if (bccomp($totalPaid, $bill->total_amount, 2) >= 0) {
                $bill->status = 'Paid';
            } elseif ($totalPaid > 0) {
                $bill->status = 'Partially Paid';
            }
            
            $bill->save();
        }

        // Generar algunos pagos adicionales para bills sin impuestos (para variedad)
        $billsWithoutTax = Bill::where('tax_amount', 0)
            ->where('status', '!=', 'Paid')
            ->take(5)
            ->get();

        foreach ($billsWithoutTax as $bill) {
            $paymentAmount = $bill->total_amount;
            
            Payment::create([
                'payable_type' => Bill::class,
                'payable_id' => $bill->bill_id,
                'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                'amount' => $paymentAmount,
                'payment_method' => $this->getRandomPaymentMethod(),
                'reference_number' => 'PAY-' . str_pad($paymentCount + 1, 6, '0', STR_PAD_LEFT),
                'notes' => "Pago completo por factura {$bill->bill_number}",
                'created_by_user_id' => 1,
            ]);

            $bill->update([
                'amount_paid' => $paymentAmount,
                'status' => 'Paid'
            ]);

            $paymentCount++;
            $this->command->info("✓ Pago sin impuestos: \${$paymentAmount} para Bill {$bill->bill_number}");
        }

        // Generar algunos pagos para facturas de venta (invoices) para balance
        $this->generateInvoicePayments();

        $this->command->info("✅ Se generaron {$paymentCount} pagos exitosamente.");
        $this->command->info("📊 Resumen de pagos:");
        $this->command->info("   - Bills con impuestos pagados: " . Bill::where('tax_amount', '>', 0)->where('status', 'Paid')->count());
        $this->command->info("   - Bills parcialmente pagados: " . Bill::where('tax_amount', '>', 0)->where('status', 'Partially Paid')->count());
        $this->command->info("   - Total de pagos generados: " . $paymentCount);
    }

    /**
     * Generar pagos para facturas de venta
     */
    private function generateInvoicePayments()
    {
        $invoices = \App\Models\Invoice::where('tax_amount', '>', 0)
            ->where('status', '!=', 'Paid')
            ->take(10)
            ->get();

        foreach ($invoices as $invoice) {
            $paymentAmount = $invoice->total_amount;
            
            Payment::create([
                'payable_type' => \App\Models\Invoice::class,
                'payable_id' => $invoice->invoice_id,
                'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                'amount' => $paymentAmount,
                'payment_method' => $this->getRandomPaymentMethod(),
                'reference_number' => 'INV-PAY-' . str_pad($invoice->invoice_id, 6, '0', STR_PAD_LEFT),
                'notes' => "Pago por factura {$invoice->invoice_number}",
                'created_by_user_id' => 1,
            ]);

            $invoice->update([
                'amount_paid' => $paymentAmount,
                'status' => 'Paid'
            ]);

            $this->command->info("✓ Pago de venta: \${$paymentAmount} para Invoice {$invoice->invoice_number}");
        }
    }

    /**
     * Obtener método de pago aleatorio
     */
    private function getRandomPaymentMethod(): string
    {
        $methods = ['Bank Transfer', 'Credit Card', 'Cash', 'Check', 'Wire Transfer'];
        return $methods[array_rand($methods)];
    }
} 