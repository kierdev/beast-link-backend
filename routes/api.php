<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicantStatusController;
use App\Http\Controllers\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * -------------------------------------------------
 * |    Applicant Tracking and Status Update       |
 * -------------------------------------------------
*/ 
Route::get('applicants/filter', [ApplicantController::class, 'filter']); // Filter applicants by name, email, course1, course2, academic_year, status

Route::apiResource('applicants', ApplicantController::class); // CRUD operations for applicants

Route::get('applicants/notifications/{applicantId}', [NotificationController::class, 'viewApplicantNotifications']); // Get owned  notifications for a specific applicant

Route::get('admin/notifications', [NotificationController::class, 'viewAdminNotifications']); // Get all notifications for admin

Route::apiResource('applicants-status', ApplicantStatusController::class)->except(['store']); // CRUD operations for applicant status

Route::apiResource('notifications', NotificationController::class)->only(['show', 'destroy']); // CRUD operations for notifications
/**
 * -------------------------------------------------
*/ 