<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicantController;

Route::post('/generate-letter/{applicant}', [ApplicantController::class, 'generateLetter']);
Route::post('/send-letter/{applicant}', [ApplicantController::class, 'sendLetter']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');