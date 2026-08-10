<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('material_usage_estimates');

        Schema::create('material_usage_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('master_materials')->cascadeOnDelete();
            $table->foreignId('clothing_category_id')->constrained('master_clothing_categories')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('master_sizes')->cascadeOnDelete();
            $table->decimal('estimated_usage', 8, 4)->default(0);
            $table->timestamps();
            
            $table->unique(['material_id', 'clothing_category_id', 'size_id'], 'material_usage_estimates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_usage_estimates');

        Schema::create('material_usage_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('master_materials')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('master_sizes')->cascadeOnDelete();
            $table->decimal('estimated_usage', 8, 4)->default(0);
            $table->timestamps();
            
            $table->unique(['material_id', 'size_id']);
        });
    }
};
