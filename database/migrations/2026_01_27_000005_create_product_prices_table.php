<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->char('currency', 3);
            $table->integer('unit_net_amount_minor');
            $table->foreignId('tax_class_id')->constrained('tax_classes');
            $table->dateTime('valid_from');
            $table->dateTime('valid_to')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
