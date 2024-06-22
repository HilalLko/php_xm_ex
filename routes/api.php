<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

Route::post('/get-company',[CompanyController::class, 'getHistoricalData']);
