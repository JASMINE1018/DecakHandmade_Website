<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('label', 60)->default('Rumah'); // contoh: Rumah, Kantor
            $table->string('recipient_name', 120);
            $table->string('phone', 25);
            $table->text('full_address');
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('subdistrict', 100)->nullable(); // Kecamatan
            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->index(['customer_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
