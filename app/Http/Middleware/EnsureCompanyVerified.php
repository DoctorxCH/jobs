<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if (! method_exists($user, 'canCompanyManageJobs') || ! $user->canCompanyManageJobs()) {
            abort(403);
        }

        $companyId = method_exists($user, 'effectiveCompanyId')
            ? $user->effectiveCompanyId()
            : $user->company_id;

        if (! $companyId) {
            return $this->redirectToVerification();
        }

        $company = Company::query()
            ->where('id', $companyId)
            ->whereNull('deleted_at')
            ->first();

        if (! $company || ! $company->verified_at) {
            return $this->redirectToVerification();
        }

        return $next($request);
    }

    private function redirectToVerification(): RedirectResponse
    {
        return redirect()
            ->route('frontend.company.verification.index')
            ->with('error', 'Please verify your company before posting a job.');
    }
}
