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
        Schema::create('customer_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Foreign key to customers table
        $table->foreignId('crm_user_id')->constrained('crm_users', 'user_id')->onDelete('cascade'); // Author
        $table->text('note');
        $table->timestamps();
        $table->index(['customer_id', 'crm_user_id']); // Index for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_notes');
    }
};
