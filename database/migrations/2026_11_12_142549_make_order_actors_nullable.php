<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->unsignedBigInteger('changed_by')->nullable()->change();
            $table->foreign('changed_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
            $table->foreign('created_by')->references('id')->on('admins')->restrictOnDelete();
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->unsignedBigInteger('changed_by')->nullable(false)->change();
            $table->foreign('changed_by')->references('id')->on('admins')->restrictOnDelete();
        });
    }
};
