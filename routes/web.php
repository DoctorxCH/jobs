<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


use App\Http\Controllers\AdminGateController;
use App\Http\Controllers\CompanyInvitationController;

use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\DashboardController as FrontendDashboardController;
use App\Http\Controllers\Frontend\Billing\InvoiceController as FrontendBillingInvoiceController;
use App\Http\Controllers\Frontend\Billing\OrderController as FrontendBillingOrderController;
use App\Http\Controllers\Frontend\Billing\PaymentController as FrontendBillingPaymentController;
use App\Http\Controllers\Frontend\Billing\ProductController as FrontendBillingProductController;
use App\Http\Controllers\Frontend\ProfileController as FrontendProfileController;
use App\Http\Controllers\Frontend\JobController as FrontendJobController;
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

        Route::prefix('/dashboard/jobs')->group(function () {
            Route::get('/', [FrontendJobController::class, 'index'])
                ->name('frontend.jobs.index');
            Route::get('/create', [FrontendJobController::class, 'create'])
                ->name('frontend.jobs.create');
            Route::post('/', [FrontendJobController::class, 'store'])
                ->name('frontend.jobs.store');
            Route::get('/{job}/edit', [FrontendJobController::class, 'edit'])
                ->name('frontend.jobs.edit');
            Route::put('/{job}', [FrontendJobController::class, 'update'])
                ->name('frontend.jobs.update');
            Route::post('/{job}/post', [FrontendJobController::class, 'post'])
                ->name('frontend.jobs.post');
            Route::post('/{job}/archive', [FrontendJobController::class, 'archive'])
                ->name('frontend.jobs.archive');
        });

        Route::prefix('/dashboard/billing')->group(function () {
            Route::get('/products', [FrontendBillingProductController::class, 'index'])
                ->name('frontend.billing.products.index');
            Route::get('/products/{product}', [FrontendBillingProductController::class, 'show'])
                ->name('frontend.billing.products.show');
            Route::get('/products/{product}/checkout', [FrontendBillingProductController::class, 'checkout'])
                ->name('frontend.billing.products.checkout');
            Route::post('/products/{product}/checkout', [FrontendBillingProductController::class, 'placeOrder'])
                ->name('frontend.billing.products.checkout.store');

            Route::get('/orders', [FrontendBillingOrderController::class, 'index'])
                ->name('frontend.billing.orders.index');
            Route::get('/orders/{order}', [FrontendBillingOrderController::class, 'show'])
                ->name('frontend.billing.orders.show');

            Route::get('/invoices', [FrontendBillingInvoiceController::class, 'index'])
                ->name('frontend.billing.invoices.index');
            Route::get('/invoices/{invoice}', [FrontendBillingInvoiceController::class, 'show'])
                ->name('frontend.billing.invoices.show');
            Route::get('/invoices/{invoice}/download', [FrontendBillingInvoiceController::class, 'download'])
                ->name('frontend.billing.invoices.download');

            Route::get('/payments', [FrontendBillingPaymentController::class, 'index'])
                ->name('frontend.billing.payments.index');
            Route::get('/payments/{payment}', [FrontendBillingPaymentController::class, 'show'])
                ->name('frontend.billing.payments.show');
        });
    });

    // Cookie consent
    Route::post('/cookies/consent', [CookieConsentController::class, 'store'])
    ->name('cookies.consent');

    /*
    |--------------------------------------------------------------------------
    | Jobs demo pages (placeholder)
    |--------------------------------------------------------------------------
    */
    Route::get('/jobs', [FrontendJobController::class, 'publicIndex'])
        ->name('jobs.index');

    Route::get('/jobs/{job}', [FrontendJobController::class, 'show'])
        ->name('jobs.show');

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
