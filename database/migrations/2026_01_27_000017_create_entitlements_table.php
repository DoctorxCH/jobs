<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('type');
            $table->integer('quantity_total');
            $table->integer('quantity_remaining');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->foreignId('source_invoice_item_id')->nullable()->constrained('invoice_items');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlements');
    }
};
