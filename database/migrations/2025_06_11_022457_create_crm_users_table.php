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
        Schema::create('crm_users', function (Blueprint $table) {
            $table->bigIncrements('user_id'); // As per diagram: * user_id : BIGINT <<PK>>
            $table->string('username', 100)->unique(); // As per diagram, added unique constraint
            $table->string('full_name', 255);    // As per diagram
            $table->string('email')->unique();         // Added for authentication
            $table->timestamp('email_verified_at')->nullable(); // Standard Laravel
            $table->string('password');                // Added for authentication
            $table->rememberToken();                   // Standard Laravel
            $table->timestamps();                      // As per diagram: created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_users');
    }
};