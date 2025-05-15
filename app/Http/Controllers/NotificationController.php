<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function getAllApplicantNotifications($applicantId)
    {
        // Fetch notifications for the specific applicant
        $notifications = Notification::where('applicant_id', $applicantId)
            ->where('for_admin', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function getAllAdminNotifications()
    {
        // Fetch all notifications for admin
        $notifications = Notification::where('for_admin', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the notification by notification_id
        $notification = Notification::where('notification_id', $id)->firstOrFail();

        // Return the notification as a JSON response
        return response()->json($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the notification by notification_id
        $notification = Notification::where('notification_id', $id)->firstOrFail();

        // Delete the notification
        $notification->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Notification deleted successfully.'], 200);
    }
}