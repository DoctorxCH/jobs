<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobPostRequest;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Models\Job;
use App\Services\Billing\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobController extends Controller
{
    protected function companyForUser(bool $requireManageJobs = true): ?Company
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        if ($requireManageJobs && ! $user->canCompanyManageJobs()) {
            abort(403);
        }

        $companyId = $user->effectiveCompanyId();
        if (! $companyId) {
            return null;
        }

        return Company::query()
            ->where('id', $companyId)
            ->whereNull('deleted_at')
            ->first();
    }

    protected function assertJobBelongsToCompany(Job $job, Company $company): void
    {
        abort_unless((int) $job->company_id === (int) $company->id, 403);
    }

    /**
     * Liefert alle Lookup-Listen, die dashboard/jobs/_form.blade.php braucht.
     */
    protected function formLookups(?int $countryId = null, ?int $regionId = null, ?int $companyId = null): array
    {
        $sknacePositions = DB::table('sknace_positions')->orderBy('id')->get();
        $benefits = DB::table('benefits')->orderBy('label')->get();
        $drivingLicenseCategories = DB::table('driving_license_categories')->orderBy('id')->get();
        $skills = DB::table('skills')->orderBy('name')->get();

        $educationLevels = DB::table('education_levels')->orderBy('label')->get();
        $educationFields = DB::table('education_fields')
            ->where('is_active', 1)
            ->orderBy('sort')
            ->orderBy('label')
            ->get();

        $countries = DB::table('countries')->orderBy('name')->get();

        // Languages + levels (aus DB)
        $languageOptions = DB::table('job_language_options')
            ->where('is_active', 1)
            ->orderBy('sort')
            ->orderBy('label')
            ->pluck('label', 'code')
            ->all();

        $languageLevels = DB::table('job_language_levels')
            ->where('is_active', 1)
            ->orderBy('sort')
            ->orderBy('label')
            ->pluck('label', 'code')
            ->all();

        // Skills levels (falls im Blade verwendet)
        $skillLevels = ['basic', 'intermediate', 'advanced', 'expert'];

        // Regionen immer laden (für alle Länder oder mindestens SK)
        $regions = DB::table('regions')
            ->when(!empty($countryId), fn ($q) => $q->where('country_id', $countryId))
            ->orderBy('name')
            ->get();

        $cities = DB::table('cities as ci')
            ->join('regions as r', 'r.id', '=', 'ci.region_id')
            ->when(!empty($countryId), fn ($q) => $q->where('r.country_id', $countryId))
            ->select('ci.*')
            ->orderBy('ci.name')
            ->get();

        // WICHTIG: teamMembers als CompanyUser-Model + user-Relation, damit $member->user->... funktioniert
        $teamMembers = collect();
        if (!empty($companyId)) {
            $teamMembers = CompanyUser::query()
                ->with('user')
                ->where('company_id', $companyId)
                ->orderBy('id')
                ->get();
        }

        return compact(
            'sknacePositions',
            'benefits',
            'drivingLicenseCategories',
            'skills',
            'educationLevels',
            'educationFields',
            'countries',
            'regions',
            'cities',
            'teamMembers',
            'languageOptions',
            'languageLevels',
            'skillLevels',
        );
    }

    public function publicIndex(Request $request): View
    {
        $search = trim((string) $request->get('q', ''));
        $regionId = $request->integer('region') ?: null;
        $cityId = $request->integer('city') ?: null;
        $countryCode = strtoupper((string) $request->get('country', 'SK'));

        $countryId = Country::query()
            ->where('code', $countryCode)
            ->value('id');

        $jobsQuery = Job::query()
            ->with(['company', 'region', 'city'])
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });

        if ($countryId) {
            $jobsQuery->where('country_id', $countryId);
        }

        if ($search !== '') {
            $jobsQuery->where('title', 'like', '%' . $search . '%');
        }

        if ($regionId) {
            $jobsQuery->where('region_id', $regionId);
        }

        if ($cityId) {
            $jobsQuery->where('city_id', $cityId);
        }

        $jobs = $jobsQuery
            ->orderByDesc('published_at')
            ->paginate(15)
            ->withQueryString();

        $regions = $countryId
            ? Region::query()->where('country_id', $countryId)->orderBy('name')->get()
            : collect();

        $cities = $regionId
            ? City::query()->where('region_id', $regionId)->orderBy('name')->get()
            : collect();

        return view('jobs.index', [
            'jobs' => $jobs,
            'search' => $search,
            'countryCode' => $countryCode,
            'regions' => $regions,
            'cities' => $cities,
            'selectedRegion' => $regionId,
            'selectedCity' => $cityId,
        ]);
    }

    public function show(Job $job): View
    {
        $job->load([
            'company',
            'country',
            'region',
            'city',
            'sknacePosition',
            'educationLevel',
            'educationField',
            'benefits',
            'jobLanguages',
            'jobSkills.skill',
            'hrTeamMember.user',
        ]);

        $now = now();
        $isVisible = $job->status === 'published'
            && (is_null($job->published_at) || $job->published_at->lte($now))
            && (is_null($job->expires_at) || $job->expires_at->gte($now));

        abort_unless($isVisible, 404);

        $company = $job->company;

        $periodStart = $job->published_at;
        $periodEnd = $job->expires_at;

        $companyContactName = $company
            ? trim(sprintf('%s %s', (string) $company->contact_first_name, (string) $company->contact_last_name))
            : '';
        $companyContactName = $companyContactName !== '' ? $companyContactName : null;

        $hrMemberName = $job->hrTeamMember?->user?->name;
        $contactName = $hrMemberName ?: $companyContactName;
        $contactEmail = $job->hr_email
            ?: $job->hrTeamMember?->user?->email
            ?: $company?->contact_email
            ?: $company?->general_email;
        $contactPhone = $job->hr_phone
            ?: $company?->contact_phone
            ?: $company?->phone;

        $employmentTypeLabels = [
            'full_time' => 'Full-time',
            'part_time' => 'Part-time',
            'contract' => 'Contract',
            'freelance' => 'Freelance',
            'internship' => 'Internship',
        ];

        $workload = null;
        if (! is_null($job->workload_min) && ! is_null($job->workload_max)) {
            $workload = sprintf('%s–%s%%', $job->workload_min, $job->workload_max);
        } elseif (! is_null($job->workload_min)) {
            $workload = sprintf('ab %s%%', $job->workload_min);
        } elseif (! is_null($job->workload_max)) {
            $workload = sprintf('bis %s%%', $job->workload_max);
        }

        $salary = null;
        if (! is_null($job->salary_min_gross_month) || ! is_null($job->salary_max_gross_month)) {
            $min = $job->salary_min_gross_month ? number_format((float) $job->salary_min_gross_month, 0, ',', ' ') : null;
            $max = $job->salary_max_gross_month ? number_format((float) $job->salary_max_gross_month, 0, ',', ' ') : null;
            $currency = $job->salary_currency ? strtoupper($job->salary_currency) : null;

            if ($min && $max) {
                $salary = trim(sprintf('%s–%s %s', $min, $max, $currency));
            } elseif ($min) {
                $salary = trim(sprintf('ab %s %s', $min, $currency));
            } elseif ($max) {
                $salary = trim(sprintf('bis %s %s', $max, $currency));
            }
        }

        $salaryNote = null;
        if ($salary && $job->salary_note) {
            $salaryNote = $job->salary_note;
        } elseif (! $salary && $job->salary_note) {
            $salary = $job->salary_note;
        }

        $locationCity = $job->city?->name ?: $company?->city;
        $locationRegion = $job->region?->name ?: $company?->region;
        $locationCountry = $job->country?->name ?: $company?->country_code;

        $locationLine = collect([$locationCity, $locationRegion, $locationCountry])
            ->filter()
            ->implode(', ');

        $postalLine = trim(collect([$company?->postal_code, $locationCity])->filter()->implode(' '));

        $languageOptions = DB::table('job_language_options')
            ->where('is_active', 1)
            ->pluck('label', 'code')
            ->all();

        $lat = $job->getAttribute('lat')
            ?? $job->getAttribute('latitude')
            ?? $company?->getAttribute('lat')
            ?? $company?->getAttribute('latitude');
        $lng = $job->getAttribute('lng')
            ?? $job->getAttribute('longitude')
            ?? $company?->getAttribute('lng')
            ?? $company?->getAttribute('longitude');

        $hasCoordinates = is_numeric($lat) && is_numeric($lng);

        return view('jobs.show', [
            'job' => $job,
            'company' => $company,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'contact' => [
                'name' => $contactName,
                'email' => $contactEmail,
                'phone' => $contactPhone,
            ],
            'employmentType' => $employmentTypeLabels[$job->employment_type] ?? $job->employment_type,
            'workload' => $workload,
            'salary' => $salary,
            'salaryNote' => $salaryNote,
            'location' => [
                'line' => $locationLine,
                'street' => null,
                'postal' => null,
            ],
            'languageOptions' => $languageOptions,
            'map' => [
                'hasCoordinates' => $hasCoordinates,
                'lat' => $hasCoordinates ? (float) $lat : null,
                'lng' => $hasCoordinates ? (float) $lng : null,
            ],
        ]);
    }

    public function index(Request $request): View
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $jobs = Job::query()
            ->where('company_id', $company->id)
            ->orderByDesc('id')
            ->paginate(20);

        return view('dashboard.jobs.index', [
            'company' => $company,
            'jobs' => $jobs,
        ]);
    }

    public function create(): View
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $creditService = new CreditService();
        $availableCredits = $creditService->availableCredits((int) $company->id);

        $lookups = $this->formLookups(null, null, (int) $company->id);

        return view('dashboard.jobs.create', array_merge([
            'company' => $company,
            'availableCredits' => $availableCredits,
        ], $lookups));
    }

    public function store(JobStoreRequest $request): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $data = $request->validated();
        $data['company_id'] = $company->id;

        $job = Job::create($data);

        if (array_key_exists('benefits', $data)) {
            $job->benefits()->sync($data['benefits'] ?? []);
        }

        if (array_key_exists('driving_license_categories', $data)) {
            $job->drivingLicenseCategories()->sync($data['driving_license_categories'] ?? []);
        }

        if (array_key_exists('job_languages', $data)) {
            $job->jobLanguages()->delete();
            foreach (($data['job_languages'] ?? []) as $row) {
                if (!empty($row['language_code']) && !empty($row['level'])) {
                    $job->jobLanguages()->create($row);
                }
            }
        }

        if (array_key_exists('job_skills', $data)) {
            $job->jobSkills()->delete();
            foreach (($data['job_skills'] ?? []) as $row) {
                if (!empty($row['skill_id']) && !empty($row['level'])) {
                    $job->jobSkills()->create($row);
                }
            }
        }

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('success', __('main.job_created'));
    }

    public function edit(Job $job): View
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        $creditService = new CreditService();
        $availableCredits = $creditService->availableCredits((int) $company->id);

        $lookups = $this->formLookups(
            $job->country_id ? (int) $job->country_id : null,
            $job->region_id ? (int) $job->region_id : null,
            (int) $company->id,
        );

        return view('dashboard.jobs.edit', array_merge([
            'company' => $company,
            'job' => $job,
            'availableCredits' => $availableCredits,
        ], $lookups));
    }

    public function update(JobUpdateRequest $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        \Log::info('JOB UPDATE incoming', [
            'job_id' => $job->id,
            'all' => $request->all(),
            'validated' => $request->validated(),
        ]);

        $data = $request->validated();
        $job->update($data);

        if (array_key_exists('benefits', $data)) {
            $job->benefits()->sync($data['benefits'] ?? []);
        }

        if (array_key_exists('driving_license_categories', $data)) {
            $job->drivingLicenseCategories()->sync($data['driving_license_categories'] ?? []);
        }

        if (array_key_exists('job_languages', $data)) {
            $job->jobLanguages()->delete();
            foreach (($data['job_languages'] ?? []) as $row) {
                if (!empty($row['language_code']) && !empty($row['level'])) {
                    $job->jobLanguages()->create($row);
                }
            }
        }

        if (array_key_exists('job_skills', $data)) {
            $job->jobSkills()->delete();
            foreach (($data['job_skills'] ?? []) as $row) {
                if (!empty($row['skill_id']) && !empty($row['level'])) {
                    $job->jobSkills()->create($row);
                }
            }
        }

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('success', __('main.job_updated'));
    }

    public function archive(Request $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        $job->update(['status' => 'archived']);

        return redirect()
            ->route('frontend.jobs.index')
            ->with('success', __('main.job_archived'));
    }

    public function unarchive(Request $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        $now = now();
        $isExpired = !empty($job->expires_at) && $job->expires_at->lt($now);

        if ($isExpired) {
            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('error', __('main.job_expired_unarchive'));
        }

        $job->forceFill([
            'status' => 'published',
            'published_at' => $job->published_at ?? $now,
        ])->save();

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('success', __('main.job_unarchived'));
    }

    public function post(Request $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);
        $this->assertJobBelongsToCompany($job, $company);

        if (($company->status ?? null) !== 'active') {
            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('error', __('main.company_must_be_active_to_publish'));
        }

        $data = $request->validate([
            'days' => ['required', 'integer', 'min:1'],
        ]);

        $days = (int) $data['days'];

        $now = now();

        $isPublished = !empty($job->published_at) && !empty($job->expires_at) && $job->expires_at->gt($now);
        $remainingDays = $isPublished ? max(0, (int) ceil($now->diffInSeconds($job->expires_at) / 86400)) : 0;

        $desiredExpiresAt = $now->copy()->addDays($days);

        // credits/day - adjust to your system
        $creditsPerDay = 1;

        $creditService = new CreditService();
        $available = $creditService->availableCredits((int) $company->id);

        if (!$isPublished) {
            // Initial publish: full credits
            $requiredCredits = $days * $creditsPerDay;

            if ($available < $requiredCredits) {
                return redirect()
                    ->route('frontend.jobs.edit', $job)
                    ->withErrors(['days' => __('main.not_enough_credits')])
                    ->withInput();
            }

            DB::transaction(function () use ($company, $job, $requiredCredits, $now, $desiredExpiresAt): void {
                if ($requiredCredits > 0) {
                    DB::table('credit_ledger')->insert([
                        'company_id' => (int) $company->id,
                        'change' => -1 * (int) $requiredCredits,
                        'reason' => 'job_post',
                        'reference_type' => 'job',
                        'reference_id' => (int) $job->id,
                        'created_by_admin_id' => null,
                        'created_at' => now(),
                    ]);
                }

                $job->forceFill([
                    'status' => 'published',
                    'published_at' => $now,
                    'expires_at' => $desiredExpiresAt->copy()->endOfDay(),
                ])->save();
            });

            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('success', __('main.job_published'));
        }

        // Already published: only delta counts
        $deltaDays = $days - $remainingDays;

        if ($deltaDays > 0) {
            $requiredCredits = $deltaDays * $creditsPerDay;

            if ($available < $requiredCredits) {
                return redirect()
                    ->route('frontend.jobs.edit', $job)
                    ->withErrors(['days' => __('main.not_enough_credits_extension')])
                    ->withInput();
            }

            DB::transaction(function () use ($company, $job, $requiredCredits, $desiredExpiresAt): void {
                if ($requiredCredits > 0) {
                    DB::table('credit_ledger')->insert([
                        'company_id' => (int) $company->id,
                        'change' => -1 * (int) $requiredCredits,
                        'reason' => 'job_extend',
                        'reference_type' => 'job',
                        'reference_id' => (int) $job->id,
                        'created_by_admin_id' => null,
                        'created_at' => now(),
                    ]);
                }

                $job->forceFill([
                    'expires_at' => $desiredExpiresAt->copy()->endOfDay(),
                ])->save();
            });

            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('success', __('main.duration_extended'));
        }

        if ($deltaDays < 0) {
            $refundDays = abs($deltaDays);
            $refundCredits = (int) ceil(($refundDays * $creditsPerDay) * 0.5);

            DB::transaction(function () use ($company, $job, $refundCredits, $desiredExpiresAt): void {
                if ($refundCredits > 0) {
                    DB::table('credit_ledger')->insert([
                        'company_id' => (int) $company->id,
                        'change' => (int) $refundCredits,
                        'reason' => 'job_reduce_refund',
                        'reference_type' => 'job',
                        'reference_id' => (int) $job->id,
                        'created_by_admin_id' => null,
                        'created_at' => now(),
                    ]);
                }

                $job->forceFill([
                    'expires_at' => $desiredExpiresAt->copy()->endOfDay(),
                ])->save();
            });

            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('success', __('main.duration_reduced'));
        }

        // deltaDays === 0 → nothing to do, but UX ok
        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('success', __('main.no_change'));
    }
}
