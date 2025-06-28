<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id('goods_receipt_id');
            $table->foreignId('purchase_order_id')->constrained('purchase_orders', 'purchase_order_id')->cascadeOnDelete();
            $table->foreignId('received_by_user_id')->constrained('crm_users', 'user_id');
            $table->date('receipt_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};

