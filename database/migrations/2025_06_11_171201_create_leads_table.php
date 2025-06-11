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
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('lead_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2)->nullable(); // Potential deal amount
            $table->string('status', 50)->default('New'); // e.g., New, Contacted, Qualified, Proposal, Won, Lost
            $table->string('source', 100)->nullable(); // e.g., Web, Referral, Cold Call
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'customer_id')->onDelete('set null');
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null');
            $table->foreignId('created_by_user_id')->nullable()->constrained('crm_users', 'user_id')->onDelete('set null');
            $table->date('expected_close_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};