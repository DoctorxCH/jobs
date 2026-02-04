<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


use App\Http\Controllers\AdminGateController;
use App\Http\Controllers\CompanyInvitationController;

use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;
use App\Http\Controllers\Frontend\DashboardController as FrontendDashboardController;
use App\Http\Controllers\Frontend\ContactController as FrontendContactController;
use App\Http\Controllers\Frontend\CompanyVerificationController as FrontendCompanyVerificationController;
use App\Http\Controllers\Frontend\Billing\InvoiceController as FrontendBillingInvoiceController;
use App\Http\Controllers\Frontend\Billing\OrderController as FrontendBillingOrderController;
use App\Http\Controllers\Frontend\Billing\PaymentController as FrontendBillingPaymentController;
use App\Http\Controllers\Frontend\Billing\ProductController as FrontendBillingProductController;
use App\Http\Controllers\Frontend\Billing\CouponController as FrontendBillingCouponController;
use App\Http\Controllers\Frontend\ProfileController as FrontendProfileController;
use App\Http\Controllers\Frontend\CompanyController as FrontendCompanyController;
use App\Http\Controllers\Frontend\LegalPageController as FrontendLegalPageController;
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

    // Registration (multi-step)
    Route::get('/register', [FrontendAuthController::class, 'showRegister'])->name('frontend.register');
    Route::get('/register/step-1', [FrontendAuthController::class, 'showRegisterStep1'])->name('frontend.register.step1');
    Route::post('/register/step-1', [FrontendAuthController::class, 'postRegisterStep1'])->name('frontend.register.step1.post');
    Route::get('/register/step-2', [FrontendAuthController::class, 'showRegisterStep2'])->name('frontend.register.step2');
    Route::post('/register/step-2', [FrontendAuthController::class, 'postRegisterStep2'])->name('frontend.register.step2.post');
    Route::get('/register/step-3', [FrontendAuthController::class, 'showRegisterStep3'])->name('frontend.register.step3');
    Route::post('/register/step-3', [FrontendAuthController::class, 'postRegisterStep3'])->name('frontend.register.step3.post');

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

        Route::get('/dashboard/contact', [FrontendContactController::class, 'create'])
            ->name('frontend.contact');
        Route::post('/dashboard/contact', [FrontendContactController::class, 'store'])
            ->name('frontend.contact.store');

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
                ->middleware('company.verified')
                ->name('frontend.jobs.post');
            Route::post('/{job}/archive', [FrontendJobController::class, 'archive'])
                ->name('frontend.jobs.archive');
            Route::post('/{job}/unarchive', [FrontendJobController::class, 'unarchive'])
                ->name('frontend.jobs.unarchive');
        });

        Route::prefix('/dashboard/company/verification')->group(function () {
            Route::get('/', [FrontendCompanyVerificationController::class, 'index'])
                ->name('frontend.company.verification.index');
            Route::post('/code/start', [FrontendCompanyVerificationController::class, 'startCode'])
                ->name('frontend.company.verification.code.start');
            Route::post('/code/confirm', [FrontendCompanyVerificationController::class, 'confirmCode'])
                ->name('frontend.company.verification.code.confirm');
            Route::post('/code/resend', [FrontendCompanyVerificationController::class, 'resendCode'])
                ->name('frontend.company.verification.code.resend');
            Route::post('/invoice/start', [FrontendCompanyVerificationController::class, 'startInvoice'])
                ->name('frontend.company.verification.invoice.start');
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

            Route::post('/coupons/apply', [FrontendBillingCouponController::class, 'apply'])
                ->name('frontend.billing.coupons.apply');
            Route::post('/coupons/remove', [FrontendBillingCouponController::class, 'remove'])
                ->name('frontend.billing.coupons.remove');

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

    Route::get('/favorites', [FrontendJobController::class, 'favorites'])
        ->name('frontend.favorites');

    Route::get('/jobs/{job}', [FrontendJobController::class, 'show'])
        ->name('jobs.show');

    Route::get('/company/{company}', [FrontendCompanyController::class, 'show'])
        ->name('company.show');

    Route::get('/agb', [FrontendLegalPageController::class, 'agb'])
        ->name('legal.agb');

    Route::get('/privacy', [FrontendLegalPageController::class, 'privacy'])
        ->name('legal.privacy');
        
    Route::get('/jobs', [FrontendJobController::class, 'publicIndex'])
        ->name('jobs.index');

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
