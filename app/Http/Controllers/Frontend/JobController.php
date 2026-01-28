<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobPostRequest;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Job;
use App\Services\Billing\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobController extends Controller
{
    protected function companyForUser(): ?Company
    {
        $user = Auth::user();

        return Company::query()
            ->where('owner_user_id', $user->id)
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
        $sknicePositions = DB::table('sknice_positions')->orderBy('title')->get();
        $benefits = DB::table('benefits')->orderBy('label')->get();
        $drivingLicenseCategories = DB::table('driving_license_categories')->orderBy('label')->get();
        $skills = DB::table('skills')->orderBy('name')->get();

        $educationLevels = DB::table('education_levels')->orderBy('label')->get();
        $educationFields = DB::table('education_fields')->orderBy('label')->get();

        $countries = DB::table('countries')->orderBy('name')->get();

        // Languages + levels (falls im Blade verwendet)
        $languageOptions = [
            'sk' => 'Slovak',
            'cs' => 'Czech',
            'de' => 'German',
            'en' => 'English',
            'hu' => 'Hungarian',
            'pl' => 'Polish',
            'uk' => 'Ukrainian',
            'ru' => 'Russian',
        ];
        $languageLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'native'];

        // Skills levels (falls im Blade verwendet)
        $skillLevels = ['basic', 'intermediate', 'advanced', 'expert'];

        $regions = collect();
        if (!empty($countryId)) {
            $regions = DB::table('regions')
                ->where('country_id', $countryId)
                ->orderBy('name')
                ->get();
        }

        $cities = collect();
        if (!empty($regionId)) {
            $cities = DB::table('cities')
                ->where('region_id', $regionId)
                ->orderBy('name')
                ->get();
        }

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
            'sknicePositions',
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
            ->with('success', 'Job created.');
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
            ->with('success', 'Job updated.');
    }

    public function archive(Request $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        $job->update(['status' => 'archived']);

        return redirect()
            ->route('frontend.jobs.index')
            ->with('success', 'Job archived.');
    }

    public function post(JobPostRequest $request, Job $job): RedirectResponse
    {
        $company = $this->companyForUser();
        abort_unless($company, 403);

        $this->assertJobBelongsToCompany($job, $company);

        $days = (int) ($request->validated()['days'] ?? 0);
        if ($days < 1) {
            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->with('error', 'Invalid days.');
        }

        $creditService = new CreditService();
        $available = $creditService->availableCredits((int) $company->id);

        $now = now()->startOfDay();
        $currentExpiry = $job->expires_at ? $job->expires_at->copy()->startOfDay() : null;

        // User input bedeutet: "bis heute + days"
        $desiredExpiry = $now->copy()->addDays($days);

        // Nie ein bestehendes späteres expires_at verkürzen
        $finalExpiry = ($currentExpiry && $currentExpiry->greaterThan($desiredExpiry))
            ? $currentExpiry
            : $desiredExpiry;

        // Credits nur für Verlängerung über das spätere von (now, currentExpiry)
        $base = ($currentExpiry && $currentExpiry->greaterThan($now)) ? $currentExpiry : $now;
        $required = $finalExpiry->greaterThan($base) ? $base->diffInDays($finalExpiry) : 0;

        if ($required > 0 && $available < $required) {
            return redirect()
                ->route('frontend.jobs.edit', $job)
                ->withErrors(['days' => 'Not enough credits available for this extension.'])
                ->withInput();
        }

        DB::transaction(function () use ($company, $job, $required, $finalExpiry): void {
            if ($required > 0) {
                DB::table('credit_ledger')->insert([
                    'company_id' => (int) $company->id,
                    'change' => -1 * (int) $required,
                    'reason' => 'job_post',
                    'reference_type' => 'job',
                    'reference_id' => (int) $job->id,
                    'created_by_admin_id' => null,
                    'created_at' => now(),
                ]);
            }

            $job->update([
                'status' => 'published',
                'published_at' => $job->published_at ?? now(),
                'expires_at' => $finalExpiry->copy()->endOfDay(),
            ]);
        });

        return redirect()
            ->route('frontend.jobs.edit', $job)
            ->with('success', $required > 0 ? 'Job published (credits consumed).' : 'Job published (no extra credits needed).');
    }
}
