<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminGateController;
use App\Http\Controllers\CompanyInvitationController;

use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\DashboardController as FrontendDashboardController;

use App\Http\Middleware\FrontendAuthenticate;

/*
|--------------------------------------------------------------------------
| Admin Gate (hidden Filament access)
|--------------------------------------------------------------------------
| - We keep the named route "login" pointing to /365gate
|   so Laravel's AuthenticationException redirectTo() never crashes.
*/
Route::middleware('web')->group(function () {
    Route::get('/365gate', [AdminGateController::class, 'show'])->name('login');
    Route::post('/365gate', [AdminGateController::class, 'authenticate'])->name('365gate.authenticate');
    Route::post('/365gate/logout', [AdminGateController::class, 'logout'])->name('365gate.logout');
});

/*
|--------------------------------------------------------------------------
| Kill Filament's default login URL
|--------------------------------------------------------------------------
| If someone tries /admin/login, we hard-disable it.
*/
Route::any('/admin/login', function () {
    abort(410);
});

/*
|--------------------------------------------------------------------------
| Frontend (Pixel)
|--------------------------------------------------------------------------
| Public pages + auth pages + protected dashboard area.
*/
Route::middleware('web')->group(function () {

    // Home
    Route::get('/', fn () => view('home'))->name('frontend.home');

    // Auth (Pixel)
    Route::get('/login', [FrontendAuthController::class, 'showLogin'])->name('frontend.login');
    Route::post('/login', [FrontendAuthController::class, 'login'])->name('frontend.login.submit');

    Route::get('/register', [FrontendAuthController::class, 'showRegister'])->name('frontend.register');
    Route::post('/register', [FrontendAuthController::class, 'register'])->name('frontend.register.submit');

    Route::post('/logout', [FrontendAuthController::class, 'logout'])->name('frontend.logout');

    // Protected Frontend Area
    Route::middleware(FrontendAuthenticate::class)->group(function () {
        Route::get('/dashboard', [FrontendDashboardController::class, 'index'])
            ->name('frontend.dashboard');

        // Profile (will contain company fields later)
        Route::get('/dashboard/profile', fn () => view('dashboard.profile'))
            ->name('frontend.profile');
    });

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
            'salary' => 'CHF 85k–110k',
            'work_mode' => 'Remote-first',
            'team_size' => '8 people',
            'deadline' => 'Oct 30, 2024',
            'description' => 'You will shape the visual guidelines of our job portal, optimize the user journey, and bring pixel-perfect details into a modern context.',
            'responsibilities' => [
                'Evolve the pixel UI design system',
                'Structure landing pages and job detail pages',
                'Collaborate with Engineering & Marketing',
            ],
            'requirements' => [
                '3+ years UI/UX experience',
                'Confident with Figma (or similar)',
                'Strong sense for minimalism and clear typography',
            ],
        ];

        return view('jobs.show', ['job' => $job]);
    })->name('jobs.show');

    /*
    |--------------------------------------------------------------------------
    | Company invite accept
    |--------------------------------------------------------------------------
    */
    Route::get('/company-invite/{token}', [CompanyInvitationController::class, 'accept'])
        ->name('company.invite.accept');

    /*
    |--------------------------------------------------------------------------
    | Old / demo company dashboard (optional)
    |--------------------------------------------------------------------------
    | Keep it if you still want it. Otherwise delete this block.
    */
    Route::get('/company/dashboard', function () {
        $company = [
            'name' => 'Axiom Tools',
            'stats' => [
                'active_jobs' => 5,
                'applications' => 48,
                'response_time' => '24h',
            ],
            'postings' => [
                [
                    'department' => 'Design',
                    'title' => 'UI Designer (Pixel UI)',
                    'status' => 'Active',
                    'location' => 'Remote · CH',
                    'candidates' => 12,
                    'stage' => 'Interview',
                ],
                [
                    'department' => 'Engineering',
                    'title' => 'Full-Stack Laravel Engineer',
                    'status' => 'Active',
                    'location' => 'Zurich',
                    'candidates' => 20,
                    'stage' => 'Review',
                ],
                [
                    'department' => 'Growth',
                    'title' => 'Community Manager',
                    'status' => 'Paused',
                    'location' => 'Remote',
                    'candidates' => 6,
                    'stage' => 'Screening',
                ],
            ],
        ];

        return view('company.dashboard', ['company' => $company]);
    })->name('company.dashboard');
});
