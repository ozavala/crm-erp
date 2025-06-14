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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('purchase_order_id');
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id')->onDelete('cascade');
            // Consider using polymorphic relationship for addresses if they are stored in the 'addresses' table
            $table->unsignedBigInteger('shipping_address_id')->nullable(); // Your company's shipping address
            $table->string('purchase_order_number')->unique()->nullable(); // Auto-generate or manual
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('type')->nullable(); // e.g., Standard, Rush, Drop Ship
            $table->string('status')->default('Draft'); // e.g., Draft, Sent, Partially Received, Received, Cancelled
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('shipping_cost', 15, 2)->default(0.00);
            $table->decimal('other_charges', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
};
