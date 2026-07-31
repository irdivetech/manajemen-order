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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('customer_category', ['b2b', 'retail'])->default('retail')->after('customer_phone');
            $table->decimal('total_cost', 15, 2)->default(0)->after('total_price')->comment('Total Harga Pokok Produksi (HPP)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_category', 'total_cost']);
        });
    }
};
