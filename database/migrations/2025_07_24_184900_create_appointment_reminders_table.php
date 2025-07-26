<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments','appointment_id')->onDelete('cascade');
            $table->string('reminder_type'); // e.g., 'email', 'sms', 'push'
            $table->integer('minutes_before');
            $table->timestamp('sent_at');
            $table->timestamps();
            
            // Ensure we don't send duplicate reminders for the same interval
            $table->unique(['appointment_id', 'minutes_before']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_reminders');
    }
};