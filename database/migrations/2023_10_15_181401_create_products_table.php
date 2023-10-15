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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50); // vonalkód
            $table->string('name', 250); // termék neve
            $table->foreignId('brand_id')->constrained('brands'); // márka
            $table->foreignId('manufacturer_id')->constrained('companies'); // gyártó
            $table->json('properties')->nullable(); // tul: original_name, sku, note
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
