<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->string('method')->default('bank_transfer');
            $table->string('status');
            $table->integer('amount_minor');
            $table->char('currency', 3);
            $table->string('bank_reference')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
