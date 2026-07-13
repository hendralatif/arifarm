<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->string('qty_type_1')->default('fixed'); // fixed, per_goat
            $table->string('qty_type_2')->default('fixed'); // fixed, per_goat
        });
    }

    public function down(): void
    {
        Schema::table('feeding_schedules', function (Blueprint $table) {
            $table->dropColumn(['qty_type_1', 'qty_type_2']);
        });
    }
};
