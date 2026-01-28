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
            'description' => ['required', 'string'],
            'sknice_position_id' => ['required', 'exists:sknice_positions,id'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,freelance,internship'],
            'workload_min' => ['required', 'integer', 'min:0', 'max:100'],
            'workload_max' => ['required', 'integer', 'min:0', 'max:100', 'gte:workload_min'],

            'country_id' => ['required', 'exists:countries,id'],
            'region_id' => ['required', 'exists:regions,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'is_remote' => ['nullable', 'boolean'],
            'is_hybrid' => ['nullable', 'boolean'],
            'travel_required' => ['nullable', 'boolean'],

            'available_from' => ['nullable', 'date'],
            'application_deadline' => ['nullable', 'date'],

            'salary_min_gross_month' => ['nullable', 'integer', 'min:0'],
            'salary_max_gross_month' => ['nullable', 'integer', 'min:0', 'gte:salary_min_gross_month'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'salary_note' => ['nullable', 'string', 'max:255'],
            'salary_months' => ['nullable', 'in:12,13'],

            'education_level_id' => ['nullable', 'exists:education_levels,id'],
            'education_field_id' => ['nullable', 'exists:education_fields,id'],
            'min_years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'is_for_graduates' => ['nullable', 'boolean'],
            'is_for_disabled' => ['nullable', 'boolean'],

            'open_positions' => ['required', 'integer', 'min:1', 'max:1000'],
            'candidate_note' => ['nullable', 'string'],
            'employer_reference' => ['nullable', 'string', 'max:80'],

            'hr_team_member_id' => ['nullable', 'exists:company_user,id'],
            'hr_email' => ['nullable', 'email', 'max:255'],
            'hr_phone' => ['nullable', 'string', 'max:50'],

            'has_company_car' => ['nullable', 'boolean'],

            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['integer', 'exists:benefits,id'],

            'job_languages' => ['nullable', 'array'],
            'job_languages.*.language_code' => ['required_with:job_languages.*.level', 'string', 'size:2'],
            'job_languages.*.level' => ['required_with:job_languages.*.language_code', 'in:A1,A2,B1,B2,C1,C2,native'],

            'job_skills' => ['nullable', 'array'],
            'job_skills.*.skill_id' => ['required_with:job_skills.*.level', 'exists:skills,id'],
            'job_skills.*.level' => ['required_with:job_skills.*.skill_id', 'in:basic,intermediate,advanced,expert'],

            'driving_license_categories' => ['nullable', 'array'],
            'driving_license_categories.*' => ['integer', 'exists:driving_license_categories,id'],
        ];
    }
}
