<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            // Тобі не вистачало цих 4 рядків нижче:
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Ці вже у тебе були:
            $table->string('payment_method');
            $table->string('delivery_type');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('post_type')->nullable();
            $table->string('post_number')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'email', 'phone', 'payment_method', 'delivery_type', 'city', 'address', 'post_type', 'post_number', 'fee']);
        });
    }
};
