<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_size_details', function (Blueprint $table) {
            $table->string('color')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('master_genders')->nullOnDelete();
            $table->foreignId('size_category_id')->nullable()->constrained('master_size_categories')->nullOnDelete();
            $table->string('size_type', 20)->nullable()->comment('standard atau big');
            $table->foreignId('size_id')->nullable()->constrained('master_sizes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_size_details', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropForeign(['size_category_id']);
            $table->dropForeign(['size_id']);
            $table->dropColumn([
                'color',
                'gender_id',
                'size_category_id',
                'size_type',
                'size_id',
            ]);
        });
    }
};
