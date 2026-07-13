<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table for feed stocks
        Schema::create('feed_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('stock_kg', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add foreign key to goat_feedings
        Schema::table('goat_feedings', function (Blueprint $table) {
            $table->foreignId('feed_stock_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('goat_feedings', function (Blueprint $table) {
            $table->dropForeign(['feed_stock_id']);
            $table->dropColumn('feed_stock_id');
        });
        Schema::dropIfExists('feed_stocks');
    }
};
