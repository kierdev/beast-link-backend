<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicantStatusController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ApplicationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * -------------------------------------------------
 * |    Applicant Tracking and Status Update       |
 * -------------------------------------------------
 */
Route::post('applicants', [ApplicantStatusController::class, 'create']);

Route::get('applicants', [ApplicantStatusController::class, 'filterApplicants']);
Route::get('applicants/{id}', [ApplicantStatusController::class, 'getApplicantById']);

// Status Update Routes
Route::put('applicants/{id}/status/missing', [ApplicantStatusController::class, 'updateToMissing']);
Route::put('applicants/{id}/status/submitted', [ApplicantStatusController::class, 'updateToSubmitted']);
Route::put('applicants/{id}/status/pending', [ApplicantStatusController::class, 'updateToPending']);
Route::put('applicants/{id}/status/under-review', [ApplicantStatusController::class, 'updateToUnderReview']);
Route::put('applicants/{id}/status/approved', [ApplicantStatusController::class, 'updateToApproved']);
Route::put('applicants/{id}/status/rejected', [ApplicantStatusController::class, 'updateToRejected']);

Route::get('applicants/notifications/{applicantId}', [ApplicantStatusController::class, 'getAllApplicantNotifications']); // Get owned  notifications for a specific applicant

Route::get('admin/notifications', [ApplicantStatusController::class, 'getAllAdminNotifications']); // Get all notifications for admin

Route::get('notifications/{notificationId}', [ApplicantStatusController::class, 'getNotificationById']); // Get a notification by notification_id

Route::delete('notifications/{notificationId}', [ApplicantStatusController::class, 'deleteNotification']); // Delete a notification by notification_id

Route::get('notifications/send', [ApplicantStatusController::class, 'sendStatusNotifications']); // Send a status notification

Route::put('notifications/{notificationId}/read', [ApplicantStatusController::class, 'markNotificationAsRead']); // Mark a notification as read by notification_id

// For applicant notifications
Route::put('notifications/mark-all-read/{applicantId}', [ApplicantStatusController::class, 'markAllNotificationsAsRead']);

// For admin notifications
Route::put('notifications/mark-all-admin-read', [ApplicantStatusController::class, 'markAllAdminNotificationsAsRead']);
/**
 * -------------------------------------------------
 */


Route::get('/test', [TestController::class, 'index']);

Route::get('/applicant/dashboard', [ApplicationController::class, 'index']);
Route::get('/program/statistics', [ApplicationController::class, 'programStatistics']);
Route::get('/interviewer/dashboard', [ApplicationController::class, 'interviewerDashboard']);
Route::get('/admin/dashboard', [ApplicationController::class, 'adminDashboard']);