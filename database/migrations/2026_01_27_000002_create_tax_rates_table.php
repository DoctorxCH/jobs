<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2);
            $table->foreignId('tax_class_id')->constrained('tax_classes');
            $table->decimal('rate_percent', 5, 2);
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->timestamps();

            $table->index(['country_code', 'tax_class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
