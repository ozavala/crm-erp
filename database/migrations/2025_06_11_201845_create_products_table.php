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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku', 100)->unique()->nullable(); // Stock Keeping Unit
            $table->decimal('price', 15, 2)->default(0.00);
            $table->decimal('cost', 15, 2)->nullable(); // Cost of the product/service
            $table->integer('quantity_on_hand')->default(0);
            $table->boolean('is_service')->default(false); // True if this is a service, false if a physical product
            $table->boolean('is_active')->default(true);   // To enable/disable product/service
            $table->foreignId('created_by_user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};