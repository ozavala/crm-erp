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
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses', 'warehouse_id');
            $table->string('receipt_number')->nullable();
            $table->string('status')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id', 'receipt_number', 'status']);
        });
    }
};
