<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(Company $company): View
    {
        if ($company->deleted_at) {
            abort(404);
        }

        if (Schema::hasColumn('companies', 'active') && ! $company->active) {
            abort(404);
        }

        $relations = [];
        if (method_exists($company, 'category')) {
            $relations[] = 'category';
        }

        if (! empty($relations)) {
            $company->load($relations);
        }

        $logoPath = $company->logo_path ?: $company->top_partner_logo_path;
        $logoUrl = $logoPath ? asset('storage/' . ltrim($logoPath, '/')) : null;

        $locationLine = collect([
            $company->city,
            $company->region,
            $company->country_code,
        ])->filter()->implode(', ');

        $socialLinks = [];
        if (is_array($company->social_links)) {
            foreach ($company->social_links as $label => $url) {
                if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $socialLinks[$label] = $url;
                }
            }
        }

        $openJobs = collect();
        $openJobsCount = 0;

        if (method_exists($company, 'jobs')) {
            $openJobsQuery = $company->jobs()
                ->where('status', 'published')
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                });

            $openJobsCount = (clone $openJobsQuery)->count();

            $openJobs = $openJobsQuery
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(10)
                ->get();
        }

        return view('company.show', [
            'company' => $company,
            'logoUrl' => $logoUrl,
            'locationLine' => $locationLine,
            'socialLinks' => $socialLinks,
            'openJobs' => $openJobs,
            'openJobsCount' => $openJobsCount,
        ]);
    }
}
