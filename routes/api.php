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
Route::get('applicants', [ApplicantStatusController::class, 'getAllApplicants']);
Route::get('applicants/filter', [ApplicantStatusController::class, 'filterApplicants']);
Route::get('applicants/{id}', [ApplicantStatusController::class, 'getApplicantById']);

// Status Update Routes
Route::patch('applicants/{id}/status/pending', [ApplicantStatusController::class, 'updateToPending']);
Route::patch('applicants/{id}/status/documents-submitted', [ApplicantStatusController::class, 'updateToDocumentsSubmitted']);
Route::patch('applicants/{id}/status/documents-verified', [ApplicantStatusController::class, 'updateToDocumentsVerified']);
Route::patch('applicants/{id}/status/interview-scheduled', [ApplicantStatusController::class, 'updateToInterviewScheduled']);
Route::patch('applicants/{id}/status/interview-completed', [ApplicantStatusController::class, 'updateToInterviewCompleted']);
Route::patch('applicants/{id}/status/test-scheduled', [ApplicantStatusController::class, 'updateToTestScheduled']);
Route::patch('applicants/{id}/status/test-completed', [ApplicantStatusController::class, 'updateToTestCompleted']);
Route::patch('applicants/{id}/status/approved', [ApplicantStatusController::class, 'updateToApproved']);
Route::patch('applicants/{id}/status/rejected', [ApplicantStatusController::class, 'updateToRejected']);
Route::patch('applicants/{id}/status/waitlisted', [ApplicantStatusController::class, 'updateToWaitlisted']);

Route::get('applicants/notifications/{applicantId}', [NotificationController::class, 'viewApplicantNotifications']); // Get owned  notifications for a specific applicant

Route::get('admin/notifications', [NotificationController::class, 'viewAdminNotifications']); // Get all notifications for admin

Route::apiResource('notifications', NotificationController::class)->only(['show', 'destroy']); // CRUD operations for notifications

/**
 * -------------------------------------------------
 */