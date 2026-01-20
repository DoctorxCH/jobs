<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Owner / relation to user
            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Category (separate table)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('company_categories')
                ->nullOnDelete();

            // Core
            $table->string('legal_name');
            $table->string('slug')->unique();

            // Slovakia identification
            $table->string('ico', 8)->unique();          // required + unique
            $table->string('dic', 10)->nullable()->unique();     // optional, but unique if set
            $table->string('ic_dph', 12)->nullable()->unique();  // optional, but unique if set (e.g. SK + 10 digits)

            // Public / profile
            $table->string('website_url')->nullable();
            $table->string('general_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('description_short', 280)->nullable();
            $table->longText('bio')->nullable();
            $table->json('social_links')->nullable(); // linkedin, facebook, instagram, x, youtube, github

            // Address (HQ)
            $table->char('country_code', 2)->default('SK');
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('street')->nullable();

            // Contact person (can differ from user)
            $table->string('contact_first_name')->nullable();
            $table->string('contact_last_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Company facts
            $table->unsignedSmallInteger('team_size')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();

            // System
            $table->string('status', 20)->default('pending'); // pending|active|suspended
            $table->timestamp('verified_at')->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes_internal')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // One owner -> one company (as per your current model)
            $table->unique('owner_user_id', 'companies_owner_unique');

            // Helpful indexes (short names)
            $table->index(['country_code', 'city'], 'companies_country_city_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
