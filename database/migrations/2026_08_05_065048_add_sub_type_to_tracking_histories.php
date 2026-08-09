<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->string('sub_type', 100)->nullable()->comment('e.g. penjahitan, bordir, penjahitan_dan_bordir');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->dropColumn('sub_type');
        });
    }
};
