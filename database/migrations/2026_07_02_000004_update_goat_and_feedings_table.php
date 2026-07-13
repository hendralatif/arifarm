<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add acquisition_type to goats
        Schema::table('goats', function (Blueprint $table) {
            $table->string('acquisition_type')->default('beli'); // beli, kelahiran, lainnya
        });

        // 2. Adjust goat_feedings to support 2 feed types
        Schema::table('goat_feedings', function (Blueprint $table) {
            // Drop constraint and column from previous migration
            $table->dropForeign(['feed_stock_id']);
            $table->dropColumn('feed_stock_id');
            $table->dropColumn('feed_type');
            $table->dropColumn('quantity_kg');
        });

        Schema::table('goat_feedings', function (Blueprint $table) {
            $table->foreignId('feed_stock_1_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
            $table->foreignId('feed_stock_2_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
            $table->string('feed_type_1')->nullable();
            $table->string('feed_type_2')->nullable();
            $table->decimal('quantity_1_kg', 8, 2)->default(0);
            $table->decimal('quantity_2_kg', 8, 2)->default(0);
        });

        // 3. Create weekly feeding schedules table
        Schema::create('feeding_schedules', function (Blueprint $table) {
            $table->id();
            $table->enum('day_of_week', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->enum('session', ['pagi', 'sore']);
            $table->foreignId('feed_stock_1_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
            $table->foreignId('feed_stock_2_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
            $table->decimal('quantity_1_kg', 8, 2)->default(0);
            $table->decimal('quantity_2_kg', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeding_schedules');

        Schema::table('goat_feedings', function (Blueprint $table) {
            $table->dropForeign(['feed_stock_1_id']);
            $table->dropForeign(['feed_stock_2_id']);
            $table->dropColumn(['feed_stock_1_id', 'feed_stock_2_id', 'feed_type_1', 'feed_type_2', 'quantity_1_kg', 'quantity_2_kg']);
            $table->string('feed_type')->nullable();
            $table->decimal('quantity_kg', 8, 2)->default(0);
            $table->foreignId('feed_stock_id')->nullable()->constrained('feed_stocks')->onDelete('set null');
        });

        Schema::table('goats', function (Blueprint $table) {
            $table->dropColumn('acquisition_type');
        });
    }
};
