<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('clothing_category_id')->nullable()->constrained('master_clothing_categories')->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('master_materials')->nullOnDelete();
            $table->string('customer_city')->nullable();
            $table->string('customer_district')->nullable();
            $table->boolean('has_embroidery')->default(false);
            $table->string('model_product')->nullable();
            $table->decimal('material_price_snapshot', 15, 2)->nullable()->comment('Harga bahan saat order dibuat (frozen)');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['clothing_category_id']);
            $table->dropForeign(['material_id']);
            $table->dropColumn([
                'clothing_category_id',
                'material_id',
                'customer_city',
                'customer_district',
                'has_embroidery',
                'model_product',
                'material_price_snapshot',
            ]);
        });
    }
};
