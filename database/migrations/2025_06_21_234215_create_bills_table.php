<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id('bill_id');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders', 'purchase_order_id')->onDelete('set null');
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id')->onDelete('cascade');
            $table->string('bill_number')->comment("Supplier's invoice/bill number");
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->string('status'); // e.g., Draft, Awaiting Payment, Paid
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->constrained('crm_users', 'user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};

