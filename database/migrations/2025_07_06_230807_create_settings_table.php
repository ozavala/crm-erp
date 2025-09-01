<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['core', 'custom'])->default('custom');
            $table->boolean('is_editable')->default(true);
            $table->timestamps();
        });

        // Agregar el setting company_legal_id
        Setting::create([
            'key' => 'company_legal_id',
            'value' => '0992793747-001',
            'type' => 'core',
            'is_editable' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};