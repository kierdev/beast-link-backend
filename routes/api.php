<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
Route::get('applicants', [ApplicantStatusController::class, 'getAllApplicants']);
Route::get('applicants/filter', [ApplicantStatusController::class, 'filterApplicants']);
Route::get('applicants/{id}', [ApplicantStatusController::class, 'getApplicantById']);

// Status Update Routes
Route::put('applicants/{id}/status/missing', [ApplicantStatusController::class, 'updateToMissing']);
Route::put('applicants/{id}/status/submitted', [ApplicantStatusController::class, 'updateToSubmitted']);
Route::put('applicants/{id}/status/pending', [ApplicantStatusController::class, 'updateToPending']);
Route::put('applicants/{id}/status/under-review', [ApplicantStatusController::class, 'updateToUnderReview']);
Route::put('applicants/{id}/status/approved', [ApplicantStatusController::class, 'updateToApproved']);
Route::put('applicants/{id}/status/rejected', [ApplicantStatusController::class, 'updateToRejected']);

Route::get('applicants/notifications/{applicantId}', [NotificationController::class, 'getAllApplicantNotifications']); // Get owned  notifications for a specific applicant

Route::get('admin/notifications', [NotificationController::class, 'getAllAdminNotifications']); // Get all notifications for admin

Route::apiResource('notifications', NotificationController::class)->only(['show', 'destroy']); 

Route::get('notifications/send', [ApplicantStatusController::class, 'sendStatusNotifications']); // Send a status notification
/**
 * -------------------------------------------------
 */