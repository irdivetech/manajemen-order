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
            // Bahan / material kain
            $table->string('material')->nullable()->after('color');

            // Custom nama / jabatan pemesan — bisa berisi beberapa baris
            // cth: "Ketua OSIS SMA N 1 Semarang\natas nama seluruh anggota"
            $table->text('customer_title')->nullable()->after('customer_category');

            // Alamat lengkap pemesan (untuk pengiriman / surat jalan)
            $table->text('customer_address')->nullable()->after('customer_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['material', 'customer_title', 'customer_address']);
        });
    }
};
