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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->text('shipping_address')->nullable();
            $table->string('phone_number');
            $table->text('notes')->nullable();
            $table->enum('shipping_method', ['diambil', 'diantar'])->default('diantar');
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->enum('status', [
                'pending_approval',
                'pending_payment',
                'pending_verification',
                'processing',
                'shipped',
                'completed',
                'cancelled'
            ])->default('pending_approval');
            $table->string('payment_method')->default('bank_transfer');
            $table->string('payment_receipt')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
