<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Track total amount paid so far (sum of all invoice_payments)
            $table->decimal('paid_amount', 15, 2)->default(0)->after('grand_total');

            // Remove tax — no longer needed
            $table->dropColumn('tax');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->decimal('tax', 15, 2)->default(0)->after('subtotal');
        });
    }
};
