<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->for($customer)->create();
        $this->assertEquals($customer->customer_id, $order->customer->customer_id);
    }

    public function test_order_has_many_items()
    {
        $order = Order::factory()->create();
        OrderItem::factory()->count(3)->create(['order_id' => $order->order_id]);
        $this->assertCount(3, $order->items);
    }
} 