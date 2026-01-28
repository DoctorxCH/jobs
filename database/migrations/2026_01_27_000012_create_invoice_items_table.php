<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('name_snapshot');
            $table->integer('qty');
            $table->integer('unit_net_minor');
            $table->decimal('tax_rate_percent', 5, 2);
            $table->integer('tax_minor');
            $table->integer('total_gross_minor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
