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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products'); // termék
            $table->integer('quantity')->default(1); // darabszám
            $table->integer('price_gross', unsigned: true)->default(0); // bruttó ár
            $table->integer('price_net', unsigned: true)->default(0); // nettó ár
            $table->foreignId('user_id')->constrained('users'); // felhasználó
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
