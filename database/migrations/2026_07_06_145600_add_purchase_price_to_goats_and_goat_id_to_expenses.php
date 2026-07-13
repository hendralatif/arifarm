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
        // 1. Add purchase_price to goats table
        Schema::table('goats', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->nullable()->after('price');
        });

        // 2. Add goat_id to expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('goat_id')->nullable()->after('id')->constrained('goats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['goat_id']);
            $table->dropColumn('goat_id');
        });

        Schema::table('goats', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
