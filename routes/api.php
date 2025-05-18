<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/applicant/dashboard', [ApplicationController::class, 'getApplicantDashboardData']);
Route::get('/chairperson/dashboard', [ApplicationController::class, 'getChairpersonDashboardData']);
Route::get('/interviewer/dashboard', [ApplicationController::class, 'getInterviewerDashboardData']);
Route::get('/admin/dashboard', [ApplicationController::class, 'getAdminDashboardData']);
