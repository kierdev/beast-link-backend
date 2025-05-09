<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;
use App\Models\ApplicantStatus;
use Illuminate\Support\Carbon;

class ApplicantStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'pending',
            'documents submitted',
            'documents verified',
            'interview scheduled',
            'interview completed',
            'test scheduled',
            'test completed',
            'approved',
            'rejected',
            'waitlisted',
        ];
    
        $now = Carbon::now();
    
        $remarksMap = [
            'pending' => 'Your application has been received and is pending initial review.',
            'documents submitted' => 'Your required documents have been submitted. Please wait while we verify them.',
            'documents verified' => 'Your documents have been successfully verified.',
            'interview scheduled' => 'Your interview is scheduled on ' . $now->copy()->addDays(3)->format('F d, Y') . ' at 10:00 AM via Zoom. Meeting link: https://zoom.us/j/xxxxxxx',
            'interview completed' => 'You have completed your interview. Please wait for further updates.',
            'test scheduled' => 'Your entrance test is scheduled on ' . $now->copy()->addDays(7)->format('F d, Y') . ' at 9:00 AM in Room 101, Main Building.',
            'test completed' => 'You have completed your entrance test. Please wait for the results.',
            'approved' => 'Congratulations! Your application has been approved.',
            'rejected' => 'We regret to inform you that your application was not successful.',
            'waitlisted' => 'Your application is on the waitlist. We will notify you if a slot becomes available.',
        ];
    
        foreach (Applicant::all() as $index => $applicant) {
            $status = $statuses[$index % count($statuses)];
            ApplicantStatus::create([
                'applicant_id' => $applicant->id,
                'status' => $status,
                'remarks' => $remarksMap[$status],
            ]);
        }
    }
}