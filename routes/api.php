<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicantController;

Route::post(
    'applicants/{applicant}/generate-letter',
    [ApplicantController::class, 'generateLetter']
);

Route::post(
    'applicants/{applicant}/send-letter',
    [ApplicantController::class, 'sendLetter']
);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');