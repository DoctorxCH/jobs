<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyCategory;
use App\Models\CompanyUser;
use App\Models\Country;
use App\Models\Job;
use App\Models\Region;
use App\Models\SknicePosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_job_with_credits(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompanyForOwner($user);
        $job = $this->createJob($company);

        DB::table('credit_ledger')->insert([
            'company_id' => $company->id,
            'change' => 5,
            'reason' => 'purchase',
            'reference_type' => 'invoice_item',
            'reference_id' => 1,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('frontend.jobs.post', $job), ['days' => 3]);

        $response->assertRedirect(route('frontend.jobs.edit', $job));

        $job->refresh();
        $this->assertSame('published', $job->status);
        $this->assertNotNull($job->published_at);
        $this->assertNotNull($job->expires_at);
        $this->assertSame(3, $job->published_at->diffInDays($job->expires_at));

        $ledgerChange = DB::table('credit_ledger')
            ->where('company_id', $company->id)
            ->where('reason', 'job_post')
            ->sum('change');

        $this->assertSame(-3, (int) $ledgerChange);
    }

    public function test_publish_job_fails_with_insufficient_credits(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompanyForOwner($user);
        $job = $this->createJob($company);

        DB::table('credit_ledger')->insert([
            'company_id' => $company->id,
            'change' => 1,
            'reason' => 'purchase',
            'reference_type' => 'invoice_item',
            'reference_id' => 1,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('frontend.jobs.post', $job), ['days' => 2]);

        $response->assertStatus(422);
    }

    public function test_viewer_cannot_manage_jobs(): void
    {
        $owner = User::factory()->create();
        $company = $this->createCompanyForOwner($owner);
        $viewer = User::factory()->create();

        CompanyUser::query()->create([
            'company_id' => $company->id,
            'user_id' => $viewer->id,
            'role' => 'viewer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($viewer)->get(route('frontend.jobs.index'));

        $response->assertStatus(403);
    }

    public function test_workload_max_validation(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompanyForOwner($user);
        $payload = $this->jobPayload();
        $payload['workload_min'] = 80;
        $payload['workload_max'] = 40;

        $response = $this->actingAs($user)
            ->postJson(route('frontend.jobs.store'), $payload);

        $response->assertStatus(422);
    }

    private function createCompanyForOwner(User $user): Company
    {
        $category = CompanyCategory::query()->create([
            'name' => 'Testing',
            'slug' => 'testing',
            'is_active' => true,
        ]);

        $company = Company::query()->create([
            'owner_user_id' => $user->id,
            'category_id' => $category->id,
            'legal_name' => 'Test Company',
            'slug' => 'test-company-' . $user->id,
            'ico' => str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            'country_code' => 'CH',
        ]);

        $user->company_id = $company->id;
        $user->save();

        return $company;
    }

    private function jobPayload(): array
    {
        $country = Country::query()->create([
            'code' => 'CH',
            'name' => 'Switzerland',
            'is_active' => true,
            'sort' => 1,
        ]);

        $region = Region::query()->create([
            'country_id' => $country->id,
            'name' => 'Zurich',
            'is_active' => true,
            'sort' => 1,
        ]);

        $city = City::query()->create([
            'region_id' => $region->id,
            'name' => 'Zurich',
            'is_active' => true,
            'sort' => 1,
        ]);

        $position = SknicePosition::query()->create([
            'code' => '1111',
            'title' => 'Developer',
            'is_active' => true,
            'sort' => 1,
        ]);

        return [
            'title' => 'Backend Engineer',
            'description' => 'Build backend services.',
            'sknice_position_id' => $position->id,
            'employment_type' => 'full_time',
            'workload_min' => 60,
            'workload_max' => 100,
            'country_id' => $country->id,
            'region_id' => $region->id,
            'city_id' => $city->id,
            'open_positions' => 1,
        ];
    }

    private function createJob(Company $company): Job
    {
        $payload = $this->jobPayload();

        return Job::query()->create(array_merge($payload, [
            'company_id' => $company->id,
            'status' => 'draft',
        ]));
    }
}
