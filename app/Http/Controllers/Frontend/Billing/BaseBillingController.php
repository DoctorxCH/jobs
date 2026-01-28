<?php

namespace App\Http\Controllers\Frontend\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BaseBillingController extends Controller
{
    protected function resolveCompany(Request $request): ?Company
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

        return Company::find($companyId);
    }

    protected function companyRequiredView(string $message = 'Billing is available only for users linked to a company.'): View
    {
        return view('dashboard.billing.empty', ['message' => $message]);
    }
}
