<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


use App\Http\Controllers\AdminGateController;
use App\Http\Controllers\CompanyInvitationController;

use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\DashboardController as FrontendDashboardController;
use App\Http\Controllers\Frontend\ProfileController as FrontendProfileController;
use App\Http\Controllers\Frontend\SecurityController as FrontendSecurityController;
use App\Http\Controllers\CookieConsentController;

use App\Http\Middleware\FrontendAuthenticate;

/*
|--------------------------------------------------------------------------
| Admin Gate (hidden Filament access)
|--------------------------------------------------------------------------
| Keep named route "login" -> /365gate so AuthenticationException redirect works.
*/
Route::middleware('web')->group(function () {
    Route::get('/365gate', [AdminGateController::class, 'show'])->name('login');
    Route::post('/365gate', [AdminGateController::class, 'authenticate'])->name('365gate.authenticate');
    Route::post('/365gate/logout', [AdminGateController::class, 'logout'])->name('365gate.logout');
});

/*
|--------------------------------------------------------------------------
| Kill Filament default login URL
|--------------------------------------------------------------------------
*/
Route::any('/admin/login', function () {
    abort(410);
});

/*
|--------------------------------------------------------------------------
| Frontend (Pixel)
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {

    // Home
    Route::get('/', fn () => view('home'))->name('frontend.home');

    // Auth
    Route::get('/login', [FrontendAuthController::class, 'showLogin'])->name('frontend.login');
    Route::post('/login', [FrontendAuthController::class, 'login'])->name('frontend.login.submit');

    Route::get('/register', [FrontendAuthController::class, 'showRegister'])->name('frontend.register');
    Route::post('/register', [FrontendAuthController::class, 'register'])->name('frontend.register.submit');

    Route::post('/logout', [FrontendAuthController::class, 'logout'])->name('frontend.logout');

    // Protected Frontend Area
    Route::middleware(FrontendAuthenticate::class)->group(function () {
        Route::get('/dashboard', [FrontendDashboardController::class, 'index'])
            ->name('frontend.dashboard');

        Route::get('/dashboard/profile', [FrontendProfileController::class, 'edit'])
            ->name('frontend.profile');

        Route::post('/dashboard/profile', [FrontendProfileController::class, 'update'])
            ->name('frontend.profile.update');

        Route::get('/dashboard/security', [FrontendSecurityController::class, 'edit'])
            ->name('frontend.security');

        Route::post('/dashboard/security', [FrontendSecurityController::class, 'update'])
            ->name('frontend.security.update');
    });

    // Cookie consent
    Route::post('/cookies/consent', [CookieConsentController::class, 'store'])
    ->name('cookies.consent');

    /*
    |--------------------------------------------------------------------------
    | Jobs demo pages (placeholder)
    |--------------------------------------------------------------------------
    */
    Route::get('/jobs', function () {
        $jobs = [
            [
                'slug' => 'product-designer-pixel',
                'company' => 'Nebula Labs',
                'title' => 'Product Designer (Pixel UI)',
                'summary' => 'Design crisp interfaces for modern job experiences in a minimal look.',
                'location' => 'Remote · CH',
                'type' => 'Full-time',
            ],
            [
                'slug' => 'backend-laravel-engineer',
                'company' => 'Nordic Stack',
                'title' => 'Backend Engineer (Laravel)',
                'summary' => 'Build robust services for matching, search logic and scalable APIs.',
                'location' => 'Zurich',
                'type' => 'Hybrid',
            ],
            [
                'slug' => 'growth-ops-lead',
                'company' => 'Cloudform',
                'title' => 'Growth Ops Lead',
                'summary' => 'Optimize funnels and employer branding with data-driven clarity.',
                'location' => 'Berlin · Remote',
                'type' => 'Part-time',
            ],
        ];

        return view('jobs.index', ['jobs' => $jobs]);
    })->name('jobs.index');

    Route::get('/jobs/{slug}', function (string $slug) {
        $job = [
            'slug' => $slug,
            'company' => 'Nebula Labs',
            'title' => 'Product Designer (Pixel UI)',
            'summary' => 'Design for crisp job interfaces that feel minimal and welcoming.',
            'location' => 'Remote · CH',
            'type' => 'Full-time',
        ];

        return view('jobs.show', ['job' => $job]);
    })->name('jobs.show');

    Route::get('/dashboard/team/invite', function () {
    $user = auth()->user();

    abort_unless($user && method_exists($user, 'canCompanyManageTeam') && $user->canCompanyManageTeam(), 403);

    $companyId = method_exists($user, 'effectiveCompanyId') ? $user->effectiveCompanyId() : null;

    $company = $companyId ? \App\Models\Company::find($companyId) : null;

    $invitations = $company
        ? \App\Models\CompanyInvitation::where('company_id', $company->id)->latest()->get()
        : collect();

    return view('dashboard.team', compact('company', 'invitations'));
})->middleware(\App\Http\Middleware\FrontendAuthenticate::class)->name('frontend.team');

    Route::post('/dashboard/team/invite', [CompanyInvitationController::class, 'send'])
        ->middleware(\App\Http\Middleware\FrontendAuthenticate::class)
        ->name('frontend.team.invite');

    // Old / demo company dashboard (optional)
    Route::get('/company/dashboard', fn () => view('company.dashboard'))
        ->name('company.dashboard');

    // Company invite accept + complete
    Route::get('/company-invite/{token}', [CompanyInvitationController::class, 'accept'])
        ->name('company.invite.accept');

    Route::post('/company-invite/{token}', [CompanyInvitationController::class, 'complete'])
        ->name('company.invite.complete');
        
});


