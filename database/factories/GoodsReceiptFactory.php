<?php

namespace Database\Factories;

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\CrmUser;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GoodsReceiptFactory extends Factory
{
    protected $model = GoodsReceipt::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'received_by_user_id' => CrmUser::factory(),
            'warehouse_id' => Warehouse::factory(),
            'receipt_number' => 'GR-' . strtoupper(Str::random(8)),
            'receipt_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => 'draft',
            'notes' => $this->faker->optional()->paragraph,
        ];
    }

    /**
     * Create a received goods receipt.
     */
    public function received()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'received',
            ];
        });
    }

    /**
     * Create a cancelled goods receipt.
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
} 