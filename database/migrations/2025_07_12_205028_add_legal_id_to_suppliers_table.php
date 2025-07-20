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
        // Verificar si la columna legal_id ya existe
        if (!Schema::hasColumn('suppliers', 'legal_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('legal_id', 100)->nullable()->after('name');
            });
        }
        
        // Actualizar suppliers existentes con legal_id temporal si están vacíos
        \App\Models\Supplier::whereNull('legal_id')->update([
            'legal_id' => \Illuminate\Support\Str::random(10)
        ]);
        
        // Hacer la columna NOT NULL y agregar la restricción única
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('legal_id', 100)->nullable(false)->change();
        });
        
        // Agregar la restricción única si no existe
        if (!Schema::hasIndex('suppliers', 'suppliers_legal_id_unique')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->unique('legal_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('legal_id');
        });
    }
};
