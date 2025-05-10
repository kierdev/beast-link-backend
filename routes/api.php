<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/test', [TestController::class, 'index']);

Route::get('/applicant/dashboard', [ApplicationController::class, 'index']);
Route::get('/program/statistics', [ApplicationController::class, 'programStatistics']);
Route::get('/interviewer/dashboard', [ApplicationController::class, 'interviewerDashboard']);
Route::get('/admin/dashboard', [ApplicationController::class, 'adminDashboard']);
