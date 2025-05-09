<?php

namespace App\Http\Controllers;

use App\Mail\StatusMail;
use App\Models\Applicant;
use App\Models\ApplicantStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Mail;

class ApplicantStatusController extends Controller
{
    // Get all applicant statuses
    public function index()
    {
        $status = ApplicantStatus::all();
        return response()->json($status, 200);
    }

    // Get applicant status by applicant id
    public function show(string $id)
    {
        $status = ApplicantStatus::findOrFail($id);
        $transformedStatus = [
            'applicant_id' => $status->applicant_id,
            'applicant_name' => $status->applicant->name,
            'status' => $status->status,
            'remarks' => $status->remarks,
        ];
        return response()->json($transformedStatus, 200);
    }

    // Update applicant status
    public function update(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $validatedData = $request->validate([
            'status' => 'required|string|max:255', // pending, documents submitted, documents verified, interview scheduled, interview completed, test scheduled, test completed, approved, rejected, waitlisted
        ]);
        $now = Carbon::now();
        $remarksMap = [
            'Pending' => 'Your application has been received and is pending initial review.',
            'Documents Submitted' => 'Your required documents have been submitted. Please wait while we verify them.',
            'Documents Verified' => 'Your documents have been successfully verified.',
            'Interview Scheduled' => 'Your interview is scheduled on ' . $now->copy()->addDays(3)->format('F d, Y') . ' at 10:00 AM via Zoom. Meeting link: https://zoom.us/j/xxxxxxx',
            'Interview Completed' => 'You have completed your interview. Please wait for further updates.',
            'Test Scheduled' => 'Your entrance test is scheduled on ' . $now->copy()->addDays(7)->format('F d, Y') . ' at 9:00 AM in Room 101, Main Building.',
            'Test Completed' => 'You have completed your entrance test. Please wait for the results.',
            'Approved' => 'Congratulations! Your application has been approved.',
            'Rejected' => 'We regret to inform you that your application was not successful.',
            'Waitlisted' => 'Your application is on the waitlist. We will notify you if a slot becomes available.',
        ];


        // Get the related status model
        $status = $applicant->status()->firstOrFail();

        // Update the model instance directly
        $status->fill($validatedData);
        $status->save();

        // For applicant
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'Application Status Update',
            'message' => "Your application status has been updated to {$validatedData['status']}. {$remarksMap[$validatedData['status']]}",
        ]);

        // For admin
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'Application Status Updated',
            'message' => "The status of the application for {$applicant->name} has been updated to {$validatedData['status']}.",
            'for_admin' => true,
        ]);

        Mail::to($applicant->email)->send(new StatusMail(
            [
                'applicant_id' => $applicant->id,
                'applicant_name' => $applicant->name,
                'status' => $validatedData['status'],
                'remarks' => $remarksMap[$validatedData['status']],
            ]
        ));

        return response()->json($status, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = ApplicantStatus::findOrFail($id);
        $status->delete();

        return response()->json(['message' => 'Applicant status deleted'], 200);
    }
}