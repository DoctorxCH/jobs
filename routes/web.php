<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/jobs', function () {
    $jobs = [
        [
            'slug' => 'product-designer-pixel',
            'company' => 'Nebula Labs',
            'title' => 'Product Designer (Pixel UI)',
            'summary' => 'Gestalte klare Interfaces für moderne Job-Experiences im minimalistischen Look.',
            'location' => 'Remote · CH',
            'type' => 'Vollzeit',
        ],
        [
            'slug' => 'backend-laravel-engineer',
            'company' => 'Nordic Stack',
            'title' => 'Backend Engineer (Laravel)',
            'summary' => 'Baue robuste Services für Matching, Suchlogik und skalierbare APIs.',
            'location' => 'Zürich',
            'type' => 'Hybrid',
        ],
        [
            'slug' => 'growth-ops-lead',
            'company' => 'Cloudform',
            'title' => 'Growth Ops Lead',
            'summary' => 'Optimiere Funnels und Employer Branding mit datengetriebener Klarheit.',
            'location' => 'Berlin · Remote',
            'type' => 'Teilzeit',
        ],
    ];

    return view('jobs.index', ['jobs' => $jobs]);
});

Route::get('/jobs/{slug}', function (string $slug) {
    $job = [
        'slug' => $slug,
        'company' => 'Nebula Labs',
        'title' => 'Product Designer (Pixel UI)',
        'summary' => 'Design für klare Job-Interfaces, die minimalistisch und einladend wirken.',
        'location' => 'Remote · CH',
        'type' => 'Vollzeit',
        'salary' => 'CHF 85k–110k',
        'work_mode' => 'Remote-first',
        'team_size' => '8 Personen',
        'deadline' => '30. Oktober 2024',
        'description' => 'Du gestaltest die visuellen Leitlinien unseres Jobportals, optimierst die User Journey und bringst Pixel-Details in einen modernen Kontext.',
        'responsibilities' => [
            'Design System für das Pixel-UI weiterentwickeln',
            'Landingpages und Jobdetailseiten strukturieren',
            'Kollaboration mit Engineering & Marketing',
        ],
        'requirements' => [
            '3+ Jahre UI/UX Erfahrung',
            'Sicher im Umgang mit Figma oder ähnlichen Tools',
            'Gefühl für Minimalismus und klare Typografie',
        ],
    ];

    return view('jobs.show', ['job' => $job]);
});

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
                'status' => 'Aktiv',
                'location' => 'Remote · CH',
                'candidates' => 12,
                'stage' => 'Interview',
            ],
            [
                'department' => 'Engineering',
                'title' => 'Full-Stack Laravel Engineer',
                'status' => 'Aktiv',
                'location' => 'Zürich',
                'candidates' => 20,
                'stage' => 'Review',
            ],
            [
                'department' => 'Growth',
                'title' => 'Community Manager',
                'status' => 'Pausiert',
                'location' => 'Remote',
                'candidates' => 6,
                'stage' => 'Screening',
            ],
        ],
    ];

    return view('company.dashboard', ['company' => $company]);
});

Route::get('/company-invite/{token}', [\App\Http\Controllers\CompanyInvitationController::class, 'accept'])
    ->name('company.invite.accept');
