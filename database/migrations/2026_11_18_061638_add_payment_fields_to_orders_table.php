<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('bank')->after('tracking_number');
            $table->string('payment_status', 30)->default('pending')->after('payment_method');
            $table->string('payment_bank', 50)->nullable()->after('payment_status');
            $table->string('payment_va_number', 50)->nullable()->after('payment_bank');
            $table->text('payment_qris_url')->nullable()->after('payment_va_number');
            $table->text('payment_raw_response')->nullable()->after('payment_qris_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_bank',
                'payment_va_number',
                'payment_qris_url',
                'payment_raw_response',
            ]);
        });
    }
};
