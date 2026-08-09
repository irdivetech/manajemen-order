<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_flow_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id')->nullable()->constrained('master_tracking_statuses')->nullOnDelete();
            $table->foreignId('to_status_id')->constrained('master_tracking_statuses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_flow_rules');
    }
};
