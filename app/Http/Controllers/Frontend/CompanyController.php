<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(Request $request, string $company): View
    {
        $companyModel = Company::query()
            ->where(function ($query) use ($company) {
                if (is_numeric($company)) {
                    $query->where('id', (int) $company);
                }

                $query->orWhere('slug', $company);
            })
            ->firstOrFail();

        if (($companyModel->status ?? null) !== 'active') {
            abort(404);
        }

        $companyModel->load(['category']);

        $logoPath = $companyModel->logo_path ?: $companyModel->top_partner_logo_path;
        $logoUrl = $logoPath ? asset('storage/' . ltrim($logoPath, '/')) : null;

        $locationLine = collect([
            $companyModel->street,
            $companyModel->postal_code,
            $companyModel->city,
        ])->filter()->implode(', ');

        $socialLinks = collect($companyModel->social_links ?? [])
            ->filter(fn ($url) => is_string($url) && $url !== '')
            ->all();

        $openJobs = Job::query()
            ->where('company_id', $companyModel->id)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        $openJobsCount = (int) Job::query()
            ->where('company_id', $companyModel->id)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->count();

        return view('company.show', [
            'company' => $companyModel,
            'logoUrl' => $logoUrl,
            'locationLine' => $locationLine ?: null,
            'socialLinks' => $socialLinks,
            'openJobs' => $openJobs,
            'openJobsCount' => $openJobsCount,
        ]);
    }
}
