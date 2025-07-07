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
        Schema::create('notes', function (Blueprint $table) {
            $table->id('note_id');
            $table->text('content');
            $table->string('notable_type');
            $table->unsignedBigInteger('notable_id');
            $table->foreignId('created_by_user_id')->constrained('crm_users', 'user_id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['notable_type', 'notable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
