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
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses', 'warehouse_id')->onDelete('cascade');
            $table->integer('quantity')->default(0); // Quantity of this product in this warehouse
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']); // A product should only have one entry per warehouse
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse');
    }
};