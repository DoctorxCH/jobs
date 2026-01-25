<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Company::where('owner_user_id', Auth::id())->first();

        return view('dashboard.index', [
            'user' => Auth::user(),
            'company' => $company,
        ]);
    }
}
