<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->string('status');
            $table->char('currency', 3);
            $table->dateTime('issued_at');
            $table->dateTime('due_at')->nullable();
            $table->string('payment_reference')->unique();
            $table->string('customer_name_snapshot');
            $table->text('customer_address_snapshot');
            $table->char('customer_country_snapshot', 2);
            $table->string('customer_vat_id_snapshot')->nullable();
            $table->string('tax_rule_applied');
            $table->boolean('reverse_charge')->default(false);
            $table->decimal('tax_rate_percent_snapshot', 5, 2);
            $table->integer('subtotal_net_minor');
            $table->integer('discount_minor');
            $table->integer('tax_minor');
            $table->integer('total_gross_minor');
            $table->string('pdf_path')->nullable();
            $table->string('pdf_hash', 64)->nullable();
            $table->foreignId('cancelled_invoice_id')->nullable()->constrained('invoices');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
