<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyLookupController;

Route::get('/company-lookup', CompanyLookupController::class);
