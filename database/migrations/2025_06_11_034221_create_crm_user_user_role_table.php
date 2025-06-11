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
        Schema::create('crm_user_user_role', function (Blueprint $table) {
            $table->unsignedBigInteger('crm_user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps(); // Optional: if you want to track when a role was assigned

            $table->foreign('crm_user_id')->references('user_id')->on('crm_users')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('user_roles')->onDelete('cascade');

            $table->primary(['crm_user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_user_user_role');
    }
};