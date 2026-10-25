<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->nullable()->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('status', 30)->default('processing')->index();
            $table->string('source', 20)->default('manual')->index();
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->string('customer_note', 500)->nullable();
            $table->string('admin_note', 500)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('admins')->restrictOnDelete();
            $table->timestamps();

            $table->index(['status', 'completed_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
