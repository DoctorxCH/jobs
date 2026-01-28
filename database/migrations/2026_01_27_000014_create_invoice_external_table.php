<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_external', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->unique()->constrained('invoices');
            $table->string('provider')->default('superfaktura');
            $table->string('external_invoice_id')->nullable();
            $table->string('external_invoice_number')->nullable();
            $table->string('external_pdf_url')->nullable();
            $table->string('sync_status')->default('pending');
            $table->dateTime('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_external');
    }
};
