<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_job', function (Blueprint $table) {
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('benefit_id')->constrained('benefits')->cascadeOnDelete();

            $table->unique(['job_id', 'benefit_id'], 'benefit_job_unique');
        });

        Schema::create('job_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->char('language_code', 2);
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'native']);
            $table->timestamps();

            $table->unique(['job_id', 'language_code'], 'job_languages_unique');
        });

        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->enum('level', ['basic', 'intermediate', 'advanced', 'expert']);
            $table->timestamps();

            $table->unique(['job_id', 'skill_id'], 'job_skills_unique');
        });

        Schema::create('job_driving_license', function (Blueprint $table) {
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('driving_license_category_id')
                ->constrained('driving_license_categories')
                ->cascadeOnDelete();

            $table->unique(['job_id', 'driving_license_category_id'], 'job_driving_license_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_driving_license');
        Schema::dropIfExists('job_skills');
        Schema::dropIfExists('job_languages');
        Schema::dropIfExists('benefit_job');
    }
};
