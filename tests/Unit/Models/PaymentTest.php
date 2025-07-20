<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_belongs_to_invoice()
    {
        $invoice = Invoice::factory()->create();
        $payment = Payment::factory()->create([
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->invoice_id,
        ]);
        $this->assertEquals($invoice->invoice_id, $payment->payable->invoice_id);
    }
} 