<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('sknice_position_id')->nullable()->constrained('sknice_positions');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'freelance', 'internship'])
                ->nullable();
            $table->unsignedTinyInteger('workload_min')->nullable();
            $table->unsignedTinyInteger('workload_max')->nullable();

            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_hybrid')->default(false);
            $table->boolean('travel_required')->default(false);

            $table->date('available_from')->nullable();
            $table->date('application_deadline')->nullable();

            $table->unsignedInteger('salary_min_gross_month')->nullable();
            $table->unsignedInteger('salary_max_gross_month')->nullable();
            $table->char('salary_currency', 3)->default('EUR');
            $table->string('salary_note', 255)->nullable();
            $table->enum('salary_months', ['12', '13'])->nullable();

            $table->foreignId('education_level_id')->nullable()->constrained('education_levels');
            $table->foreignId('education_field_id')->nullable()->constrained('education_fields');
            $table->unsignedTinyInteger('min_years_experience')->nullable();
            $table->boolean('is_for_graduates')->default(false);
            $table->boolean('is_for_disabled')->default(false);

            $table->unsignedSmallInteger('open_positions')->default(1);
            $table->text('candidate_note')->nullable();
            $table->string('employer_reference', 80)->nullable();

            $table->foreignId('hr_team_member_id')->nullable()->constrained('company_user');
            $table->string('hr_email')->nullable();
            $table->string('hr_phone', 50)->nullable();

            $table->boolean('has_company_car')->default(false);

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->index(['company_id', 'status'], 'jobs_company_status_idx');
            $table->index(['expires_at'], 'jobs_expires_at_idx');
            $table->index(['country_id', 'region_id', 'city_id'], 'jobs_location_idx');
            $table->index(['sknice_position_id'], 'jobs_sknice_position_idx');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('jobs_company_status_idx');
            $table->dropIndex('jobs_expires_at_idx');
            $table->dropIndex('jobs_location_idx');
            $table->dropIndex('jobs_sknice_position_idx');

            $table->dropForeign(['company_id']);
            $table->dropForeign(['sknice_position_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['education_level_id']);
            $table->dropForeign(['education_field_id']);
            $table->dropForeign(['hr_team_member_id']);

            $table->dropColumn([
                'company_id',
                'title',
                'description',
                'sknice_position_id',
                'employment_type',
                'workload_min',
                'workload_max',
                'country_id',
                'region_id',
                'city_id',
                'is_remote',
                'is_hybrid',
                'travel_required',
                'available_from',
                'application_deadline',
                'salary_min_gross_month',
                'salary_max_gross_month',
                'salary_currency',
                'salary_note',
                'salary_months',
                'education_level_id',
                'education_field_id',
                'min_years_experience',
                'is_for_graduates',
                'is_for_disabled',
                'open_positions',
                'candidate_note',
                'employer_reference',
                'hr_team_member_id',
                'hr_email',
                'hr_phone',
                'has_company_car',
                'status',
                'published_at',
                'expires_at',
            ]);
        });
    }
};
