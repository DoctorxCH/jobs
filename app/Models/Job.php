<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'sknace_position_id',
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
    ];

    protected $casts = [
        'company_id' => 'integer',
        'workload_min' => 'integer',
        'workload_max' => 'integer',
        'country_id' => 'integer',
        'region_id' => 'integer',
        'city_id' => 'integer',
        'education_level_id' => 'integer',
        'education_field_id' => 'integer',
        'min_years_experience' => 'integer',
        'open_positions' => 'integer',
        'is_remote' => 'boolean',
        'is_hybrid' => 'boolean',
        'travel_required' => 'boolean',
        'is_for_graduates' => 'boolean',
        'is_for_disabled' => 'boolean',
        'has_company_car' => 'boolean',
        'available_from' => 'date',
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sknacePosition(): BelongsTo
    {
        return $this->belongsTo(SknacePosition::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function educationField(): BelongsTo
    {
        return $this->belongsTo(EducationField::class);
    }

    public function hrTeamMember(): BelongsTo
    {
        return $this->belongsTo(CompanyUser::class, 'hr_team_member_id');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class);
    }

    public function jobLanguages(): HasMany
    {
        return $this->hasMany(JobLanguage::class);
    }

    public function jobSkills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    public function drivingLicenseCategories(): BelongsToMany
    {
        return $this->belongsToMany(DrivingLicenseCategory::class, 'job_driving_license');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('expires_at', '>=', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }
}
