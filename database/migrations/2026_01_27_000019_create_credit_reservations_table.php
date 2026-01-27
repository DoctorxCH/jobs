<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('amount');
            $table->string('purpose')->default('job_post');
            $table->string('reference_type')->default('job');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('status');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_reservations');
    }
};
