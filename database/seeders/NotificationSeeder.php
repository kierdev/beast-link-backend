<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Applicant::all() as $applicant) {
            $status = $applicant->status; // Get related status

            Notification::create([
                'applicant_id' => $applicant->id,
                'title' => 'Application Status Update',
                'message' => $status
                    ? "Your application status has been updated to {$status->status}. {$status->remarks}"
                    : "Your application status is not available.",
                'for_admin' => false,
            ]);

            Notification::create([
                'applicant_id' => $applicant->id,
                'title' => 'Application Status Updated',
                'message' => $status
                    ? "The status of the application for {$applicant->name} has been updated to {$status->status}."
                    : "No status found for applicant {$applicant->name}.",
                'for_admin' => true,
            ]);
        }
    }
}