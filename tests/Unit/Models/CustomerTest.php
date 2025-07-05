<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_have_multiple_contacts()
    {
        $customer = Customer::factory()->hasContacts(3)->create();
        $this->assertCount(3, $customer->contacts);
    }

    public function test_customer_has_orders_relationship()
    {
        $customer = Customer::factory()->hasOrders(2)->create();
        $this->assertCount(2, $customer->orders);
    }
} 