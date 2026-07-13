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
        Schema::create('goat_weighings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->constrained()->onDelete('cascade');
            $table->decimal('weight_kg', 8, 2);
            $table->date('weighed_at');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goat_weighings');
    }
};
