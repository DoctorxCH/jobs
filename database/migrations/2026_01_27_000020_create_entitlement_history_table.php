<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entitlement_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitlement_id')->constrained('entitlements');
            $table->integer('change');
            $table->string('reason');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users');
            $table->json('meta')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlement_history');
    }
};
