<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pakan (Feeding Records)
        Schema::create('goat_feedings', function (Blueprint $table) {
            $table->id();
            $table->date('feeding_date');
            $table->time('feeding_time')->nullable();
            $table->string('feed_type'); // Jenis pakan: Rumput, Konsentrat, Dedak, dll
            $table->decimal('quantity_kg', 8, 2); // Jumlah pakan (kg)
            $table->integer('goat_count'); // Jumlah kambing yang diberi pakan
            $table->string('session')->default('pagi'); // pagi, siang, sore
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Kesehatan (Health Records)
        Schema::create('goat_health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->constrained('goats')->onDelete('cascade');
            $table->date('check_date');
            $table->enum('record_type', ['checkup', 'vaccination', 'treatment', 'observation']);
            $table->string('diagnosis')->nullable();
            $table->string('treatment')->nullable();
            $table->string('medicine')->nullable();
            $table->decimal('medicine_dose', 8, 2)->nullable();
            $table->string('vet_name')->nullable();
            $table->enum('health_status', ['healthy', 'sick', 'recovering', 'critical']);
            $table->date('next_checkup')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Kelahiran (Birth Records)
        Schema::create('goat_births', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('goats')->onDelete('cascade');
            $table->foreignId('father_id')->nullable()->constrained('goats')->onDelete('set null');
            $table->date('birth_date');
            $table->integer('total_kids'); // Jumlah anak lahir
            $table->integer('male_count')->default(0);
            $table->integer('female_count')->default(0);
            $table->integer('stillborn_count')->default(0);
            $table->enum('birth_condition', ['normal', 'assisted', 'cesarean'])->default('normal');
            $table->string('mother_condition')->default('healthy'); // kondisi induk setelah melahirkan
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goat_births');
        Schema::dropIfExists('goat_health_records');
        Schema::dropIfExists('goat_feedings');
    }
};
