<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('inbox_email', 150)->nullable();
            $table->string('outbox_email', 150)->nullable();
            $table->json('status_options')->nullable();
            $table->unsignedBigInteger('default_form_id')->nullable();
            $table->timestamps();

            $table->index(['default_form_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
