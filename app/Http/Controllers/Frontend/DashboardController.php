<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyId = $user && method_exists($user, 'effectiveCompanyId')
            ? $user->effectiveCompanyId()
            : null;

        $company = $companyId ? Company::query()->find($companyId) : null;

        return view('dashboard.index', [
            'user' => Auth::user(),
            'company' => $company,
        ]);
    }
}
