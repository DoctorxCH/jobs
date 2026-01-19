<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending_created')->index();
            $table->string('occupation')->nullable();
            $table->string('category')->nullable();
            $table->string('title');
            $table->string('short_description', 30)->nullable();
            $table->longText('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->string('workload')->nullable();
            $table->json('languages')->nullable();
            $table->string('education_level')->nullable();
            $table->json('requirements')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_top')->default(false);
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
