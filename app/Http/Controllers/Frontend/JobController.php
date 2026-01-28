<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobPostRequest;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Benefit;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\DrivingLicenseCategory;
use App\Models\EducationField;
use App\Models\EducationLevel;
use App\Models\Job;
use App\Models\Region;
use App\Models\Skill;
use App\Models\SknicePosition;
use App\Services\Billing\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);

        if (! $company) {
            abort(404);
        }

        $jobs = Job::query()
            ->where('company_id', $company->id)
            ->latest('id')
            ->get();

        return view('dashboard.jobs.index', [
            'company' => $company,
            'jobs' => $jobs,
        ]);
    }

    public function create(Request $request, CreditService $creditService)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);

        if (! $company) {
            abort(404);
        }

        return view('dashboard.jobs.create', $this->formData($company, $creditService));
    }

    public function store(JobStoreRequest $request, CreditService $creditService)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);

        if (! $company) {
            abort(404);
        }

        $data = $request->validated();

        $job = new Job();
        $job->company_id = $company->id;
        $job->status = 'draft';
        $job->fill($this->jobFillData($data));
        $this->applyHrSnapshot($job, $data, $company);
        $job->save();

        $this->syncRelations($job, $data);

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('status', 'Job saved as draft.');
    }

    public function edit(Request $request, Job $job, CreditService $creditService)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);
        $this->ensureOwnership($company, $job);

        return view('dashboard.jobs.edit', array_merge(
            $this->formData($company, $creditService),
            ['job' => $job]
        ));
    }

    public function update(JobUpdateRequest $request, Job $job, CreditService $creditService)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);
        $this->ensureOwnership($company, $job);

        $data = $request->validated();

        $job->fill($this->jobFillData($data));
        $this->applyHrSnapshot($job, $data, $company);
        $job->save();

        $this->syncRelations($job, $data);

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('status', 'Job updated successfully.');
    }

    public function post(JobPostRequest $request, Job $job, CreditService $creditService)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);
        $this->ensureOwnership($company, $job);

        $days = (int) $request->validated()['days'];
        $available = $creditService->availableCredits($company->id);

        if ($available < $days) {
            throw ValidationException::withMessages([
                'days' => 'Not enough credits available for this posting.',
            ]);
        }

        DB::transaction(function () use ($creditService, $company, $job, $days): void {
            $reservation = $creditService->reserveForJob($company, $job->id, $days);
            $creditService->consumeReservation($reservation, $job->id, $days);

            $job->status = 'published';
            $job->published_at = now();
            $job->expires_at = now()->addDays($days);
            $job->save();
        });

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('status', 'Job published successfully.');
    }

    public function archive(Request $request, Job $job)
    {
        $company = $this->resolveCompany($request);
        $this->authorizeCompanyManage($request);
        $this->ensureOwnership($company, $job);

        $job->status = 'archived';
        $job->save();

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('status', 'Job archived.');
    }

    private function resolveCompany(Request $request): ?Company
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $companyId = method_exists($user, 'effectiveCompanyId')
            ? $user->effectiveCompanyId()
            : $user->company_id;

        if (! $companyId) {
            return null;
        }

        return Company::query()->find($companyId);
    }

    private function authorizeCompanyManage(Request $request): void
    {
        $user = $request->user();

        abort_unless($user && method_exists($user, 'canCompanyManageJobs') && $user->canCompanyManageJobs(), 403);
    }

    private function ensureOwnership(?Company $company, Job $job): void
    {
        if (! $company || (int) $job->company_id !== (int) $company->id) {
            abort(404);
        }
    }

    private function jobFillData(array $data): array
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'sknice_position_id' => $data['sknice_position_id'],
            'employment_type' => $data['employment_type'],
            'workload_min' => $data['workload_min'],
            'workload_max' => $data['workload_max'],
            'country_id' => $data['country_id'],
            'region_id' => $data['region_id'],
            'city_id' => $data['city_id'],
            'is_remote' => (bool) ($data['is_remote'] ?? false),
            'is_hybrid' => (bool) ($data['is_hybrid'] ?? false),
            'travel_required' => (bool) ($data['travel_required'] ?? false),
            'available_from' => $data['available_from'] ?? null,
            'application_deadline' => $data['application_deadline'] ?? null,
            'salary_min_gross_month' => $data['salary_min_gross_month'] ?? null,
            'salary_max_gross_month' => $data['salary_max_gross_month'] ?? null,
            'salary_currency' => $data['salary_currency'] ?? 'EUR',
            'salary_note' => $data['salary_note'] ?? null,
            'salary_months' => $data['salary_months'] ?? null,
            'education_level_id' => $data['education_level_id'] ?? null,
            'education_field_id' => $data['education_field_id'] ?? null,
            'min_years_experience' => $data['min_years_experience'] ?? null,
            'is_for_graduates' => (bool) ($data['is_for_graduates'] ?? false),
            'is_for_disabled' => (bool) ($data['is_for_disabled'] ?? false),
            'open_positions' => $data['open_positions'] ?? 1,
            'candidate_note' => $data['candidate_note'] ?? null,
            'employer_reference' => $data['employer_reference'] ?? null,
            'has_company_car' => (bool) ($data['has_company_car'] ?? false),
        ];
    }

    private function applyHrSnapshot(Job $job, array $data, Company $company): void
    {
        $job->hr_team_member_id = $data['hr_team_member_id'] ?? null;

        $member = null;
        if (! empty($data['hr_team_member_id'])) {
            $member = CompanyUser::query()
                ->with('user')
                ->where('company_id', $company->id)
                ->where('id', $data['hr_team_member_id'])
                ->first();
        }

        $job->hr_email = $data['hr_email']
            ?? $member?->user?->email
            ?? null;
        $job->hr_phone = $data['hr_phone'] ?? null;
    }

    private function syncRelations(Job $job, array $data): void
    {
        $job->benefits()->sync($data['benefits'] ?? []);
        $job->drivingLicenseCategories()->sync($data['driving_license_categories'] ?? []);

        $job->jobLanguages()->delete();
        foreach ($data['job_languages'] ?? [] as $language) {
            if (empty($language['language_code']) || empty($language['level'])) {
                continue;
            }

            $job->jobLanguages()->create([
                'language_code' => $language['language_code'],
                'level' => $language['level'],
            ]);
        }

        $job->jobSkills()->delete();
        foreach ($data['job_skills'] ?? [] as $skill) {
            if (empty($skill['skill_id']) || empty($skill['level'])) {
                continue;
            }

            $job->jobSkills()->create([
                'skill_id' => $skill['skill_id'],
                'level' => $skill['level'],
            ]);
        }
    }

    private function formData(Company $company, CreditService $creditService): array
    {
        $languageOptions = [
            'en' => 'English',
            'de' => 'German',
            'fr' => 'French',
            'it' => 'Italian',
            'es' => 'Spanish',
        ];

        return [
            'benefits' => Benefit::query()->orderBy('sort')->get(),
            'skills' => Skill::query()->where('is_active', true)->orderBy('name')->get(),
            'sknicePositions' => SknicePosition::query()->orderBy('sort')->get(),
            'countries' => Country::query()->where('is_active', true)->orderBy('sort')->get(),
            'regions' => Region::query()->orderBy('sort')->get(),
            'cities' => City::query()->orderBy('sort')->get(),
            'educationLevels' => EducationLevel::query()->orderBy('sort')->get(),
            'educationFields' => EducationField::query()->orderBy('sort')->get(),
            'drivingLicenseCategories' => DrivingLicenseCategory::query()->orderBy('code')->get(),
            'teamMembers' => CompanyUser::query()
                ->with('user')
                ->where('company_id', $company->id)
                ->where('status', 'active')
                ->get(),
            'languageOptions' => $languageOptions,
            'languageLevels' => ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'native'],
            'skillLevels' => ['basic', 'intermediate', 'advanced', 'expert'],
            'availableCredits' => $creditService->availableCredits($company->id),
        ];
    }
}
