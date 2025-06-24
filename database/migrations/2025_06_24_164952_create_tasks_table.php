<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Pending'); // e.g., Pending, In Progress, Completed
            $table->string('priority')->default('Normal'); // e.g., Low, Normal, High
            $table->morphs('taskable'); // Creates taskable_id and taskable_type
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('crm_users', 'user_id')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('crm_users', 'user_id')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
