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
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('user_id')->constrained('users');
            $table->char('currency', 3);
            $table->string('status');
            $table->string('tax_rule_applied');
            $table->boolean('reverse_charge')->default(false);
            $table->decimal('tax_rate_percent_snapshot', 5, 2);
            $table->integer('subtotal_net_minor');
            $table->integer('discount_minor');
            $table->integer('tax_minor');
            $table->integer('total_gross_minor');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons');
            $table->string('coupon_code_snapshot')->nullable();
            $table->integer('coupon_discount_minor')->nullable();
            $table->integer('company_discount_minor')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
