<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('owner_company_id')->nullable()->after('payment_id');
        // Si quieres la relación:
        // $table->foreign('owner_company_id')->references('id')->on('owner_companies');
    });
}
public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('owner_company_id');
    });
    }
};
