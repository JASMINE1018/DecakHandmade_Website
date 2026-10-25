<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->string('sku', 50)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('base_price')->default(0);
            $table->integer('stock')->default(0)->index();
            $table->unsignedInteger('min_stock')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->json('gallery')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
