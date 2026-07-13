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
        Schema::create('goats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(1);
            $table->decimal('weight_kg', 8, 2);
            $table->integer('age_months');
            $table->enum('gender', ['male', 'female']);
            $table->string('breed');
            $table->enum('health_status', ['healthy', 'vaccine_completed', 'under_observation'])->default('healthy');
            $table->boolean('vaccine_status')->default(false);
            $table->json('images')->nullable();
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goats');
    }
};
