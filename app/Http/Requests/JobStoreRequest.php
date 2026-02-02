<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sknace_position_id' => ['required', 'integer', 'exists:sknace_positions,id'],
            'description' => ['nullable', 'string'],

            // Work
            'employment_type' => ['required', 'string', 'in:full_time,part_time,contract,freelance,internship'],
            'workload_min' => ['required', 'integer', 'min:0', 'max:100'],
            'workload_max' => ['required', 'integer', 'min:0', 'max:100'],
            'is_remote' => ['nullable', 'boolean'],
            'is_hybrid' => ['nullable', 'boolean'],
            'travel_required' => ['nullable', 'boolean'],
            'has_company_car' => ['nullable', 'boolean'],

            // Location
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],

            // Dates
            'available_from' => ['nullable', 'date'],
            'application_deadline' => ['nullable', 'date'],
            'open_positions' => ['required', 'integer', 'min:1'],

            // Salary
            'salary_min_gross_month' => ['nullable', 'numeric', 'min:0'],
            'salary_max_gross_month' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'max:3'],
            'salary_months' => ['nullable', 'string', 'in:12,13'],
            'salary_note' => ['nullable', 'string', 'max:255'],

            // Requirements
            'education_level_id' => ['nullable', 'integer', 'exists:education_levels,id'],
            'education_field_id' => ['nullable', 'integer', 'exists:education_fields,id'],
            'min_years_experience' => ['nullable', 'integer', 'min:0'],
            'is_for_graduates' => ['nullable', 'boolean'],
            'is_for_disabled' => ['nullable', 'boolean'],
            'candidate_note' => ['nullable', 'string'],

            // HR / Contact
            'hr_team_member_id' => ['nullable', 'integer', 'exists:company_user,id'],
            'employer_reference' => ['nullable', 'string', 'max:80'],
            'hr_email' => ['nullable', 'email', 'max:255'],
            'hr_phone' => ['nullable', 'string', 'max:50'],

            // Benefits & Licenses
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['integer', 'exists:benefits,id'],
            'driving_license_categories' => ['nullable', 'array'],
            'driving_license_categories.*' => ['integer', 'exists:driving_license_categories,id'],

            // Languages
            'job_languages' => ['nullable', 'array'],
            'job_languages.*.language_code' => ['nullable', 'string', 'max:10', 'exists:job_language_options,code'],
            'job_languages.*.level' => ['nullable', 'string', 'max:20', 'exists:job_language_levels,code'],

            // Skills
            'job_skills' => ['nullable', 'array'],
            'job_skills.*.skill_id' => ['nullable', 'integer', 'exists:skills,id'],
            'job_skills.*.level' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_remote' => $this->boolean('is_remote'),
            'is_hybrid' => $this->boolean('is_hybrid'),
            'travel_required' => $this->boolean('travel_required'),
            'has_company_car' => $this->boolean('has_company_car'),
            'is_for_graduates' => $this->boolean('is_for_graduates'),
            'is_for_disabled' => $this->boolean('is_for_disabled'),
        ]);
    }
}
