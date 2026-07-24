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
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('product_name');
            $table->string('product_type');
            $table->string('color');
            $table->string('size');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->date('order_date');
            $table->date('deadline');
            $table->enum('current_status', [
                'order_received',
                'fabric_cutting',
                'sewing',
                'embroidery',
                'button_installation',
                'shipping',
            ])->default('order_received');
            $table->text('notes')->nullable();
            $table->timestamp('archived_at')->nullable();
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
