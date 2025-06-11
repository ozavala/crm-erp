<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_product_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('product_features', 'feature_id')->onDelete('cascade');
            $table->string('value'); // The specific value of the feature for this product, e.g., "Red", "XL"
            $table->timestamps();

            $table->unique(['product_id', 'feature_id']); // A product should not have the same feature twice
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_product_feature');
    }
};