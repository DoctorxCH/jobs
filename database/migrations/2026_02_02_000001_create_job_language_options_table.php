<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_language_options', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('label', 100);
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_language_options');
    }
};
