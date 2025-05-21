<?php

namespace App\Http\Controllers;

use App\Mail\StatusMail;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Exam;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mail;

class ApplicantStatusController extends Controller
{
    /**
     * Filter and retrieve applicants based on various criteria
     * Supports filtering by name, email, choices, academic year, and status
     * Includes sorting options and pagination
     * 
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function filterApplicants(Request $request)
    {
        $query = Applicant::with('applications');

        // Filter conditions
        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('First_Name', 'like', $searchTerm)
                    ->orWhere('Last_Name', 'like', $searchTerm);
            });
        } else {
            if ($request->filled('first_name')) {
                $query->where('First_Name', 'like', '%' . $request->first_name . '%');
            }

            if ($request->filled('last_name')) {
                $query->where('Last_Name', 'like', '%' . $request->last_name . '%');
            }
        }

        if ($request->filled('email')) {
            $query->where('Email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('first_choice')) {
            $query->where('First_Choice', $request->first_choice);
        }

        if ($request->filled('second_choice')) {
            $query->where('Second_Choice', $request->second_choice);
        }

        if ($request->filled('academic_year')) {
            $query->where('Academic_Year', $request->academic_year);
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

    /**
     * Retrieve a specific applicant by their ID
     * Returns applicant details along with their current application status and remarks
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicantById(string $id)
    {
        $applicant = Applicant::with('applications')->findOrFail($id);

        $data = $applicant->toArray();
        unset($data['status']); // Remove the nested status relationship
        $data['status'] = $applicant->status ? $applicant->status->status : null;
        $data['remarks'] = $applicant->status ? $applicant->status->remarks : null;

        return response()->json($data);
    }

    /**
     * Update an applicant's status and send notifications
     * Validates the new status, updates the database, and sends email notifications
     * Also creates notifications for both applicant and admin
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendStatusNotifications(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $validatedData = $request->validate([
            'status' => 'required|string|in:missing,submitted,pending,under review,approved,rejected',
        ]);

        // Map of status to remarks
        $remarksMap = [
            'missing' => 'Some required documents are missing from your application. Please submit the complete set of documents to proceed.',
            'submitted' => 'Your application has been successfully submitted. We will review your documents and get back to you soon.',
            'pending' => 'Your application is in the queue and pending initial review.',
            'under review' => 'Your application is currently under review by our admissions committee. We will notify you once the review is complete.',
            'approved' => 'Congratulations! Your application has been approved. Welcome to our university!',
            'rejected' => 'We regret to inform you that your application was not successful at this time.'
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

    /**
     * Create notifications and send email for status updates
     * Creates two notifications: one for the applicant and one for admin
     * Sends an email to the applicant about their status change
     * 
     * @param Applicant $applicant
     * @param string $status
     * @param string $remarks
     * @return void
     */
    public function createStatusNotifications($applicant, $status, $remarks)
    {
        // For applicant
        $applicant->notifications()->create([
            'applicant_id' => $applicant->applicant_id,
            'title' => 'Application Status Update',
            'message' => "Your application status has been updated to {$status}. {$remarks}",
        ]);

        // For admin
        $applicant->notifications()->create([
            'applicant_id' => $applicant->applicant_id,
            'title' => 'Application Status Updated',
            'message' => "The status of the application for {$applicant->First_Name} {$applicant->Last_Name} has been updated to {$status}.",
            'for_admin' => true,
        ]);

        Mail::to($applicant->Email)->send(new StatusMail([
            'applicant_id' => $applicant->applicant_id,
            'applicant_name' => $applicant->First_Name . ' ' . $applicant->Last_Name,
            'status' => $status,
            'remarks' => $remarks,
        ]));
    }

    /**
     * Update applicant status to 'Missing'
     * Used when required documents are not submitted
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToMissing(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'missing']);
        $status->save();

        $remarks = 'Some required documents are missing from your application. Please submit the complete set of documents to proceed.';
        $this->createStatusNotifications($applicant, 'missing', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Update applicant status to 'Submitted'
     * Used when application is successfully submitted
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToSubmitted(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'submitted']);
        $status->save();

        $remarks = 'Your application has been successfully submitted. We will review your documents and get back to you soon.';
        $this->createStatusNotifications($applicant, 'submitted', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Update applicant status to 'Pending'
     * Used when application is in queue for initial review
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToPending(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'pending']);
        $status->save();

        $remarks = 'Your application is in the queue and pending initial review.';
        $this->createStatusNotifications($applicant, 'pending', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Update applicant status to 'Under Review'
     * Used when application is being reviewed by admissions committee
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToUnderReview(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'under review']);
        $status->save();

        $remarks = 'Your application is currently under review by our admissions committee. We will notify you once the review is complete.';
        $this->createStatusNotifications($applicant, 'under review', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Update applicant status to 'Approved'
     * Used when application has been accepted
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToApproved(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'approved']);
        $status->save();

        $remarks = 'Congratulations! Your application has been approved. Welcome to our university!';
        $this->createStatusNotifications($applicant, 'approved', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Update applicant status to 'Rejected'
     * Used when application has been declined
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateToRejected(Request $request, string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $status = $applicant->applications()->firstOrFail();

        $status->fill(['status' => 'rejected']);
        $status->save();

        $remarks = 'We regret to inform you that your application was not successful at this time.';
        $this->createStatusNotifications($applicant, 'rejected', $remarks);

        return response()->json($status, 201);
    }

    /**
     * Retrieve all notifications for a specific applicant
     * Returns notifications ordered by creation date (newest first)
     * Only returns notifications that are not for admin
     * 
     * @param string|int $applicantId The ID of the applicant
     * @return \Illuminate\Http\JsonResponse List of applicant's notifications
     */
    public function getAllApplicantNotifications($applicantId)
    {
        // Fetch notifications for the specific applicant
        $notifications = Notification::where('applicant_id', $applicantId)
            ->where('for_admin', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Retrieve all notifications for admin users
     * Returns notifications ordered by creation date (newest first)
     * Only returns notifications marked as for_admin
     * 
     * @return \Illuminate\Http\JsonResponse List of admin notifications
     */
    public function getAllAdminNotifications()
    {
        // Fetch all notifications for admin
        $notifications = Notification::where('for_admin', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Retrieve a specific notification by ID and mark it as read
     * Updates both is_read flag and read_at timestamp when notification is viewed
     * Returns the notification details
     * 
     * @param string $id The notification ID
     * @return \Illuminate\Http\JsonResponse The notification details
     */
    public function getNotificationById(string $id)
    {
        // Find the notification by notification_id
        $notification = Notification::where('notification_id', $id)->firstOrFail();

        // Mark as read if not already read
        if (!$notification->is_read) {
            try {
                $notification->is_read = true;
                $notification->read_at = now();
                $notification->save();

                // Refresh the model to get the updated values
                $notification->refresh();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to update notification status',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // Return the updated notification as a JSON response
        return response()->json($notification);
    }

    /**
     * Delete a specific notification by ID
     * Permanently removes the notification from the database
     * 
     * @param string $id The notification ID
     * @return \Illuminate\Http\JsonResponse Success message
     */
    public function deleteNotification(string $id)
    {
        // Find the notification by notification_id
        $notification = Notification::where('notification_id', $id)->firstOrFail();

        // Delete the notification
        $notification->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Notification deleted successfully.'], 200);
    }

    /**
     * Mark a specific notification as read
     * Updates is_read flag and read_at timestamp
     * Used when explicitly marking a notification as read
     * 
     * @param string $id The notification ID
     * @return \Illuminate\Http\JsonResponse Success message
     */
    public function markNotificationAsRead(string $id)
    {
        $notification = Notification::where('notification_id', $id)->firstOrFail();

        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read successfully.'], 200);
    }

    /**
     * Mark all unread notifications as read
     * Updates is_read flag and read_at timestamp for all unread notifications
     * Can handle both applicant and admin notifications
     * 
     * @param string|int|null $applicantId The ID of the applicant (null for admin notifications)
     * @param bool $forAdmin Whether to mark admin notifications as read
     * @return \Illuminate\Http\JsonResponse Success message
     */
    public function markAllNotificationsAsRead($applicantId = null, bool $forAdmin = false)
    {
        $query = Notification::where('is_read', false);

        if ($forAdmin) {
            $query->where('for_admin', true);
        } else {
            if (!$applicantId) {
                return response()->json(['message' => 'Applicant ID is required for non-admin notifications'], 400);
            }
            $query->where('applicant_id', $applicantId)
                ->where('for_admin', false);
        }

        $query->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        $message = $forAdmin
            ? 'All admin notifications marked as read successfully.'
            : 'All applicant notifications marked as read successfully.';

        return response()->json(['message' => $message], 200);
    }

    public function markAllAdminNotificationsAsRead()
    {
        return $this->markAllNotificationsAsRead(null, true);
    }

    /**
     * Create a new applicant with their application and exam
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'gender' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'civil_status' => 'nullable|string|max:20',
            'place_of_birth' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
            'barangay' => 'nullable|string|max:100',
            'city_municipality' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:tbl_applicants,Email',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email',
            'shs_strand' => 'nullable|string|max:100',
            'shs_school' => 'nullable|string|max:255',
            'shs_address' => 'nullable|string',
            'gwa_12' => 'nullable|numeric',
            'gwa_11' => 'nullable|numeric',
            'college' => 'required|string|max:255',
            'first_choice' => 'required|string|max:50',
            'second_choice' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'exam_time' => 'required|date_format:H:i:s',
            'exam_title' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'exam_location' => 'required|string|max:255',
            'exam_type' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Get the next applicant_id
            $nextApplicantId = Applicant::max('applicant_id') + 1;

            // Create the applicant
            $applicant = Applicant::create([
                'applicant_id' => $nextApplicantId,
                'Last_Name' => $request->last_name,
                'First_Name' => $request->first_name,
                'Middle_Name' => $request->middle_name,
                'Name_Extension' => $request->name_extension,
                'Gender' => $request->gender,
                'Religion' => $request->religion,
                'Citizenship' => $request->citizenship,
                'Civil_Status' => $request->civil_status,
                'Place_of_Birth' => $request->place_of_birth,
                'Age' => $request->age,
                'Address' => $request->address,
                'Barangay' => $request->barangay,
                'City_Municipality' => $request->city_municipality,
                'District' => $request->district,
                'Zip_Code' => $request->zip_code,
                'Mobile_Number' => $request->mobile_number,
                'Email' => $request->email,
                'Guardian_Name' => $request->guardian_name,
                'Guardian_Email' => $request->guardian_email,
                'SHS_Strand' => $request->shs_strand,
                'SHS_School' => $request->shs_school,
                'SHS_Address' => $request->shs_address,
                'GWA_12' => $request->gwa_12,
                'GWA_11' => $request->gwa_11,
                'College' => $request->college,
                'First_Choice' => $request->first_choice,
                'Second_Choice' => $request->second_choice,
                'Academic_Year' => $request->academic_year,
                'Timestamp' => now(),
            ]);

            // Create the exam
            $exam = Exam::create([
                'exam_id' => $nextApplicantId,
                'exam_time' => $request->exam_time,
                'exam_title' => $request->exam_title,
                'exam_date' => $request->exam_date,
                'program_code' => $request->first_choice, // Using first choice as program code
                'exam_location' => $request->exam_location,
                'exam_type' => $request->exam_type,
            ]);

            // Create the application
            $application = Application::create([
                'application_id' => $nextApplicantId,
                'applicant_id' => $nextApplicantId,
                'program_code' => $request->first_choice,
                'exam_id' => $nextApplicantId,
                'application_date' => now(),
                'status' => 'pending',
                'exam_score' => null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Applicant, exam, and application created successfully',
                'applicant' => $applicant,
                'exam' => $exam,
                'application' => $application
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create applicant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}