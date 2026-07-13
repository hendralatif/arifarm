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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('shipping_distance')->nullable()->after('shipping_cost');
            $table->boolean('is_wonosobo')->default(false)->after('shipping_distance');
            $table->enum('payment_type', ['full', 'dp'])->default('full')->after('payment_method');
            $table->decimal('dp_amount', 15, 2)->default(0)->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_distance', 'is_wonosobo', 'payment_type', 'dp_amount']);
        });
    }
};
