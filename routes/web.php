<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/company-invite/{token}', [\App\Http\Controllers\CompanyInvitationController::class, 'accept'])
    ->name('company.invite.accept');
