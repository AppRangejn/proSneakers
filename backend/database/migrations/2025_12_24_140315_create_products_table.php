<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Основне
            $table->string('name');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('colors')->nullable();
            $table->string('sizes')->nullable();

            // Характеристики
            $table->string('gender')->nullable();
            $table->string('brand')->nullable();
            $table->string('purpose')->nullable();
            $table->string('season')->nullable();
            $table->string('lining_material')->nullable();
            $table->string('upper_material')->nullable();
            $table->string('sole_material')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};