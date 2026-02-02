<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);

            $table->index(['country_id', 'is_active'], 'regions_country_active_idx');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);

            $table->index(['region_id', 'is_active'], 'cities_region_active_idx');
        });

        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
        });

        Schema::create('education_fields', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
        });

        Schema::create('sknace_positions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
        });

        Schema::create('driving_license_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driving_license_categories');
        Schema::dropIfExists('sknace_positions');
        Schema::dropIfExists('education_fields');
        Schema::dropIfExists('education_levels');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('benefits');
    }
};
