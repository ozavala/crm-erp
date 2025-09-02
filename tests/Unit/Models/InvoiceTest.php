<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $invoice = Invoice::factory()->for($customer)->create();
        $this->assertEquals($customer->customer_id, $invoice->customer->customer_id);
    }

    public function test_invoice_has_many_items()
    {
        $invoice = Invoice::factory()->create();
        InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->invoice_id]);
        $this->assertCount(2, $invoice->items);
    }
} 