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
    // Get all applicants
    public function getAllApplicants()
    {
        $applicants = Applicant::with('applications')->get()->map(function ($applicant) {
            $data = $applicant->toArray();
            unset($data['applications']); // Remove the nested applications relationship

            // Get the first application's status and remarks
            $application = $applicant->applications->first();
            $data['status'] = $application ? $application->status : null;
            $data['remarks'] = $application ? $application->remarks : null;

            return $data;
        });

        return response()->json($applicants, 200);
    }

    // Filter applicants by name, email, course1, course2, academic_year, status    
    public function filterApplicants(Request $request)
    {
        $query = Applicant::with('applications');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('first_choice')) {
            $query->where('first_choice', $request->first_choice);
        }

        if ($request->filled('course2')) {
            $query->where('course2', $request->course2);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $applicants = $query->get()->map(function ($applicant) {
            $data = $applicant->toArray();
            unset($data['status']);
            $data['status'] = $applicant->status ? $applicant->status->status : null;
            $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;
            return $data;
        });

        return response()->json($applicants, 200);
    }

    // Get applicant by id
    public function getApplicantById(string $id)
    {
        $applicant = Applicant::with('applications')->findOrFail($id);

        $data = $applicant->toArray();
        unset($data['status']); // Remove the nested status relationship
        $data['status'] = $applicant->status ? $applicant->status->status : null;
        $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;

        return response()->json($data);
    }

    // Create a new applicant
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:applicants',
    //         'academic_year' => 'required|string|max:255',
    //         'course1' => 'required|string|max:255',
    //         'course2' => 'nullable|string|max:255',
    //     ]);

    //     $applicant = Applicant::create($validatedData);
    //     // For applicant
    //     $applicant->notifications()->create([
    //         'applicant_id' => $applicant->id,
    //         'title' => 'Application Received',
    //         'message' => "Your application for the $applicant->course1 program has been successfully submitted. You will receive updates on your document verification and interview schedule soon.",
    //     ]);
    //     // For admin
    //     $applicant->notifications()->create([
    //         'applicant_id' => $applicant->id,
    //         'title' => 'New Applicant Submission',
    //         'message' => "A new applicant $applicant->name, has submitted an application for the $applicant->course1 program. Please review the submitted documents and verify the application status.",
    //         'for_admin' => true,
    //     ]);
    //     $applicant->status()->create([
    //         'applicant_id' => $applicant->id,
    //         'status' => 'Pending',
    //         'remarks' => 'Your application is under review.',
    //     ]);

    //     Mail::to($applicant->email)->send(new StatusMail(
    //         [
    //             'applicant_id' => $applicant->id,
    //             'applicant_name' => $applicant->name,
    //             'status' => 'Pending',
    //             'remarks' => 'Your application is under review.',
    //         ]
    //     ));

    //     return response()->json($applicant, 201);
    // }

    private function createStatusNotifications($applicant, $status, $remarks)
    {
        // For applicant
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'Application Status Update',
            'message' => "Your application status has been updated to {$status}. {$remarks}",
        ]);

        // For admin
        $applicant->notifications()->create([
            'applicant_id' => $applicant->id,
            'title' => 'Application Status Updated',
            'message' => "The status of the application for {$applicant->name} has been updated to {$status}.",
            'for_admin' => true,
        ]);

        Mail::to($applicant->email)->send(new StatusMail([
            'applicant_id' => $applicant->id,
            'applicant_name' => $applicant->name,
            'status' => $status,
            'remarks' => $remarks,
        ]));
    }

    public function updateToPending(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Pending']);
        $status->save();

        $remarks = 'Your application has been received and is pending initial review.';
        $this->createStatusNotifications($applicant, 'Pending', $remarks);

        return response()->json($status, 201);
    }

    public function updateToDocumentsSubmitted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Documents Submitted']);
        $status->save();

        $remarks = 'Your required documents have been submitted. Please wait while we verify them.';
        $this->createStatusNotifications($applicant, 'Documents Submitted', $remarks);

        return response()->json($status, 201);
    }

    public function updateToDocumentsVerified(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Documents Verified']);
        $status->save();

        $remarks = 'Your documents have been successfully verified.';
        $this->createStatusNotifications($applicant, 'Documents Verified', $remarks);

        return response()->json($status, 201);
    }

    public function updateToInterviewScheduled(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();
        $now = Carbon::now();

        $status->fill(['status' => 'Interview Scheduled']);
        $status->save();

        $remarks = 'Your interview is scheduled on ' . $now->copy()->addDays(3)->format('F d, Y') . ' at 10:00 AM via Zoom. Meeting link: https://zoom.us/j/xxxxxxx';
        $this->createStatusNotifications($applicant, 'Interview Scheduled', $remarks);

        return response()->json($status, 201);
    }

    public function updateToInterviewCompleted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Interview Completed']);
        $status->save();

        $remarks = 'You have completed your interview. Please wait for further updates.';
        $this->createStatusNotifications($applicant, 'Interview Completed', $remarks);

        return response()->json($status, 201);
    }

    public function updateToTestScheduled(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();
        $now = Carbon::now();

        $status->fill(['status' => 'Test Scheduled']);
        $status->save();

        $remarks = 'Your entrance test is scheduled on ' . $now->copy()->addDays(7)->format('F d, Y') . ' at 9:00 AM in Room 101, Main Building.';
        $this->createStatusNotifications($applicant, 'Test Scheduled', $remarks);

        return response()->json($status, 201);
    }

    public function updateToTestCompleted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Test Completed']);
        $status->save();

        $remarks = 'You have completed your entrance test. Please wait for the results.';
        $this->createStatusNotifications($applicant, 'Test Completed', $remarks);

        return response()->json($status, 201);
    }

    public function updateToApproved(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Approved']);
        $status->save();

        $remarks = 'Congratulations! Your application has been approved.';
        $this->createStatusNotifications($applicant, 'Approved', $remarks);

        return response()->json($status, 201);
    }

    public function updateToRejected(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Rejected']);
        $status->save();

        $remarks = 'We regret to inform you that your application was not successful.';
        $this->createStatusNotifications($applicant, 'Rejected', $remarks);

        return response()->json($status, 201);
    }

    public function updateToWaitlisted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Waitlisted']);
        $status->save();

        $remarks = 'Your application is on the waitlist. We will notify you if a slot becomes available.';
        $this->createStatusNotifications($applicant, 'Waitlisted', $remarks);

        return response()->json($status, 201);
    }
}