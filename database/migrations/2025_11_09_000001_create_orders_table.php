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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Order info
            $table->string('order_number')->unique();
            $table->string('status')->default('pending'); // pending, confirmed, processing, shipped, delivered, cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, pending, paid, failed
            
            // Pricing
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            
            // Shipping info
            $table->json('shipping_address')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('shipping_service_type')->nullable();
            $table->text('notes')->nullable();
            
            // Payment info
            $table->string('payment_method')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('xendit_invoice_id')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('order_number');
            $table->index('payment_status');
            $table->index('status');
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