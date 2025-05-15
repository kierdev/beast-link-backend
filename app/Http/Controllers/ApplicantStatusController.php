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
    // public function getAllApplicants()
    // {
    //     $applicants = Applicant::with('applications')->get()->map(function ($applicant) {
    //         $data = $applicant->toArray();
    //         unset($data['applications']); // Remove the nested applications relationship

    //         // Get the first application's status and remarks
    //         $application = $applicant->applications->first();
    //         $data['status'] = $application ? $application->status : null;
    //         $data['remarks'] = $application ? $application->remarks : null;

    //         return $data;
    //     });

    //     return response()->json($applicants, 200);
    // }

    // Filter applicants  
    public function filterApplicants(Request $request)
    {
        $query = Applicant::with('applications');

        // Filter conditions
        if ($request->filled('name')) {
            $query->where('Name', 'like', '%' . $request->Name . '%');
        }

        if ($request->filled('email')) {
            $query->where('Email', 'like', '%' . $request->Email . '%');
        }

        if ($request->filled('first_choice')) {
            $query->where('First_Choice', $request->First_Choice);
        }

        if ($request->filled('second_choice')) {
            $query->where('Second_Choice', $request->Second_Choice);
        }

        if ($request->filled('academic_year')) {
            $query->where('Academic_Year', $request->Academic_Year);
        }

        if ($request->filled('status')) {
            $query->whereHas('applications', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Sorting options
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $query->orderBy('Timestamp', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('Timestamp', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('Last_Name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('Last_Name', 'desc');
                    break;
                default:
                    // Default sorting by newest if invalid sort option
                    $query->orderBy('Timestamp', 'desc');
            }
        } else {
            // Default sorting by newest if no sort option provided
            $query->orderBy('Timestamp', 'desc');
        }

        // Use Laravel's default pagination
        $perPage = max(1, min(100, intval($request->input('per_page', 10))));

        return $query->paginate($perPage)->through(function ($applicant) {
            $data = $applicant->toArray();
            unset($data['applications']); // Remove the nested applications relationship

            // Get the first application's status and remarks
            $application = $applicant->applications->first();
            $data['status'] = $application ? $application->status : null;
            $data['remarks'] = $application ? $application->remarks : null;

            return $data;
        });
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

    public function sendStatusNotifications(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $validatedData = $request->validate([
            'status' => 'required|string|in:Missing,Submitted,Pending,Under Review,Approved,Rejected',
        ]);

        // Map of status to remarks
        $remarksMap = [
            'Missing' => 'Some required documents are missing from your application. Please submit the complete set of documents to proceed.',
            'Submitted' => 'Your application has been successfully submitted. We will review your documents and get back to you soon.',
            'Pending' => 'Your application is in the queue and pending initial review.',
            'Under Review' => 'Your application is currently under review by our admissions committee. We will notify you once the review is complete.',
            'Approved' => 'Congratulations! Your application has been approved. Welcome to our university!',
            'Rejected' => 'We regret to inform you that your application was not successful at this time.'
        ];

        // Get the application status
        $status = $applicant->applications()->firstOrFail();

        // Update the status
        $status->fill(['status' => $validatedData['status']]);
        $status->save();

        // Get the remarks for the status
        $remarks = $remarksMap[$validatedData['status']] ?? 'Status has been updated.';

        // Create notifications and send email
        $this->createStatusNotifications($applicant, $validatedData['status'], $remarks);

        return response()->json([
            'message' => 'Status updated successfully',
            'status' => $status,
            'remarks' => $remarks
        ], 201);
    }

    public function createStatusNotifications($applicant, $status, $remarks)
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

        Mail::to($applicant->Email)->send(new StatusMail([
            'applicant_id' => $applicant->id,
            'applicant_name' => $applicant->name,
            'status' => $status,
            'remarks' => $remarks,
        ]));
    }

    public function updateToMissing(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Missing']);
        $status->save();

        $remarks = 'Some required documents are missing from your application. Please submit the complete set of documents to proceed.';
        $this->createStatusNotifications($applicant, 'Missing', $remarks);

        return response()->json($status, 201);
    }

    public function updateToSubmitted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Submitted']);
        $status->save();

        $remarks = 'Your application has been successfully submitted. We will review your documents and get back to you soon.';
        $this->createStatusNotifications($applicant, 'Submitted', $remarks);

        return response()->json($status, 201);
    }

    public function updateToPending(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Pending']);
        $status->save();

        $remarks = 'Your application is in the queue and pending initial review.';
        $this->createStatusNotifications($applicant, 'Pending', $remarks);

        return response()->json($status, 201);
    }

    public function updateToUnderReview(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Under Review']);
        $status->save();

        $remarks = 'Your application is currently under review by our admissions committee. We will notify you once the review is complete.';
        $this->createStatusNotifications($applicant, 'Under Review', $remarks);

        return response()->json($status, 201);
    }

    public function updateToApproved(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Approved']);
        $status->save();

        $remarks = 'Congratulations! Your application has been approved. Welcome to our university!';
        $this->createStatusNotifications($applicant, 'Approved', $remarks);

        return response()->json($status, 201);
    }

    public function updateToRejected(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'Rejected']);
        $status->save();

        $remarks = 'We regret to inform you that your application was not successful at this time.';
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