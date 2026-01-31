<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'jobs_postings';

    protected $fillable = [
        'company_id',
        'sknice_position_id',
        'title',
        'description',

        // Work
        'employment_type',
        'workload_min',
        'workload_max',
        'is_remote',
        'is_hybrid',
        'travel_required',
        'has_company_car',

        // Location
        'country_id',
        'region_id',
        'city_id',

        // Dates
        'available_from',
        'application_deadline',
        'open_positions',

        // Salary
        'salary_min_gross_month',
        'salary_max_gross_month',
        'salary_currency',
        'salary_months',
        'salary_note',

        // Requirements
        'education_level_id',
        'education_field_id',
        'min_years_experience',
        'is_for_graduates',
        'is_for_disabled',
        'candidate_note',

        // HR / Contact
        'hr_team_member_id',
        'employer_reference',
        'hr_email',
        'hr_phone',

        // Status & Publishing
        'status',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'is_hybrid' => 'boolean',
        'travel_required' => 'boolean',
        'has_company_car' => 'boolean',
        'is_for_graduates' => 'boolean',
        'is_for_disabled' => 'boolean',

        'workload_min' => 'integer',
        'workload_max' => 'integer',
        'open_positions' => 'integer',
        'min_years_experience' => 'integer',

        'salary_min_gross_month' => 'decimal:2',
        'salary_max_gross_month' => 'decimal:2',

        'available_from' => 'date',
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /* -----------------
     | Relations
     |-----------------*/

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sknicePosition(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'sknice_position_id', 'id', 'sknice_positions');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'country_id', 'id', 'countries');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'region_id', 'id', 'regions');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'city_id', 'id', 'cities');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'education_level_id', 'id', 'education_levels');
    }

    public function educationField(): BelongsTo
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'education_field_id', 'id', 'education_fields');
    }

    public function hrTeamMember(): BelongsTo
    {
        return $this->belongsTo(CompanyUser::class, 'hr_team_member_id');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(\Illuminate\Database\Eloquent\Model::class, 'job_benefit', 'job_id', 'benefit_id', null, null, 'benefits');
    }

    public function drivingLicenseCategories(): BelongsToMany
    {
        return $this->belongsToMany(\Illuminate\Database\Eloquent\Model::class, 'job_driving_license_category', 'job_id', 'driving_license_category_id', null, null, 'drivingLicenseCategories');
    }

    public function jobLanguages(): HasMany
    {
        return $this->hasMany(JobLanguage::class);
    }

    public function jobSkills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }
}
