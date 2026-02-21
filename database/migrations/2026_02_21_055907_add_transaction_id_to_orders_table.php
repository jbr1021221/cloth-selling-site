<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds transaction_id for storing the SSLCommerz transaction reference.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // SSLCommerz bank transaction ID (returned in callback)
            $table->string('transaction_id')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
};
