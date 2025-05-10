<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Application;
use App\Models\Program;
use App\Models\Applicant;
use App\Models\Exam;
use App\Models\Event;



class ApplicationController extends Controller
{
    public function applicantDashboard(Request $request)
    {
        $applicantId = $request->query('applicant_id');

        if (!$applicantId) {
            return response()->json(['error' => 'applicant_id is required'], 400);
        }

        $applications = Application::with('program')
            ->where('applicant_id', $applicantId)
            ->get();

        $data = $applications->map(function ($application) {
            return [
                'application_status' => $application->status,
                'application_date' => $application->application_date,
                'course_code' => $application->program->program_code,
                'course_title' => $application->program->program_name,
                'course_category' => $application->program->program_category,
                'course_description' => $application->program->program_description,
            ];
        });

        return response()->json($data);
    }
    public function programStatistics(Request $request)
    {
        $programCode = $request->query('program_code');

        if (!$programCode) {
            return response()->json(['error' => 'program_code is required'], 400);
        }

        // Fetch statistics
        $query = Application::where('program_code', $programCode);

        $statistics = [
            'course_Failed_applicants'  => $query->clone()->where('status', 'failed')->count(),
            'Course_Passed_applicants'  => $query->clone()->where('status', 'passed')->count(),
            'Course_Pending_applicants' => $query->clone()->where('status', 'pending')->count(),
            'Course_Total_applicants'   => $query->count(),
        ];

        // Applicant list with name, status, exam scores, and application date
        $applicantList = Application::with(['applicant', 'exam'])
            ->where('program_code', $programCode)
            ->get()
            ->map(function ($application) {
                return [
                    'name'           => $application->applicant->applicant_firstName . ' ' . $application->applicant->applicant_lastName,
                    'status'         => $application->status,
                    'exam_score'     => $application->exam_score ?? 'N/A',
                    'application_date' => $application->application_date,
                ];
            });

        // Exams list with title, date, time, location, and type
        $examsList = Exam::where('program_code', $programCode)
            ->get()
            ->map(function ($exam) {
                return [
                    'title'         => $exam->exam_title,
                    'date'          => $exam->exam_date,
                    'time'          => $exam->exam_time,
                    'location'      => $exam->exam_location,
                    'type'          => $exam->exam_type ?? 'Entrance',
                ];
            });

        // Events list with title, date, and type
        $eventsList = Event::all()->map(function ($event) {
            return [
                'title'         => $event->title,
                'event_date'    => $event->event_date,
                'event_type'    => $event->event_type,
            ];
        });

        // Return all statistics and lists
        return response()->json([
            'statistics' => $statistics,
            'applicant_list' => $applicantList,
            'exams_list' => $examsList,
            'events_list' => $eventsList,
        ]);
    }
    public function interviewerDashboard()
    {
        $applications = Application::with(['exam', 'program', 'applicant'])->get();

        $data = $applications->map(function ($application) {
            $exam = $application->exam;
            $applicant = $application->applicant;
            $program = $application->program;

            $name = $applicant?->applicant_firstName . ' ' . $applicant?->applicant_lastName;

            // Default structure
            $pendingRemarks = [];
            $completedInterviews = [];

            if ($exam) {
                $isFuture = $exam->exam_date >= now()->toDateString();

                if ($isFuture && $application->status === 'pending') {
                    $pendingRemarks[] = [
                        'name'     => $name,
                        'status'   => $application->status,
                        'date'     => $exam->exam_date,
                        'time'     => $exam->exam_time,
                        'location' => $exam->exam_location,
                    ];
                }

                if ($exam->exam_date < now()->toDateString()) {
                    $completedInterviews[] = [
                        'name'     => $name,
                        'status'   => 'Completed',
                        'date'     => $exam->exam_date,
                        'time'     => $exam->exam_time,
                        'location' => $exam->exam_location,
                        'remarks'  => $application->remarks ?? 'No remarks provided',
                    ];
                }
            }

            return [
                'exam_time'           => $exam?->exam_time,
                'exam_title'          => $exam?->exam_title,
                'exam_location'       => $exam?->exam_location,
                'admission_date'      => $application->application_date,
                'upcoming_interviews' => $exam && $exam->exam_date >= now()->toDateString() ? [[
                    'name'           => $name,
                    'date'           => $exam->exam_date,
                    'time'           => $exam->exam_time,
                    'program_title'  => $program?->program_name,
                    'location'       => $exam->exam_location,
                ]] : [],
                'pending_remarks'     => $pendingRemarks,
                'completed_interviews' => $completedInterviews,
            ];
        });

        return response()->json($data);
    }



    public function adminDashboard()
    {
        // Total number of programs
        $totalCourses = Program::count();

        // Application status counts
        $failedApplicants  = Application::where('status', 'failed')->count();
        $passedApplicants  = Application::where('status', 'passed')->count();
        $pendingApplicants = Application::where('status', 'pending')->count();
        $totalApplicants   = Application::count();

        // Applicant detailed info
        $applicantInfo = Applicant::select('applicant_id', 'applicant_firstName', 'applicant_lastName', 'applicant_email')->get();

        // Application trends (grouped by month)
        $applicationTrends = Application::selectRaw("DATE_FORMAT(application_date, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Applicant List with additional data
        $applicantList = Application::with(['applicant', 'program', 'exam'])
            ->get()
            ->map(function ($application) {
                return [
                    'name'             => $application->applicant->applicant_firstName . ' ' . $application->applicant->applicant_lastName,
                    'course_name'      => $application->program->program_name,
                    'status'           => $application->status,
                    'exam_score'       => $application->exam_score ?? 'N/A',
                    'application_date' => $application->application_date
                ];
            });

        $coursesList = DB::table('tbl_program as p')
            ->leftJoin('tbl_applications as a', 'p.program_code', '=', 'a.program_code')
            ->select(
                'p.program_code as code',
                'p.program_name as title',
                'p.program_category as category',
                'p.program_description as specific_course',
                DB::raw('MAX(a.application_date) as admission_date')
            )
            ->groupBy('p.program_code', 'p.program_name', 'p.program_category', 'p.program_description')
            ->get();


        return response()->json([
            'Total_course'          => $totalCourses,
            'Failed_applicants'     => $failedApplicants,
            'Passed_applicants'     => $passedApplicants,
            'Pending_applicants'    => $pendingApplicants,
            'Total_applicants'      => $totalApplicants,
            'Application_trends'    => $applicationTrends,
            'Applicant_information' => Applicant::select('applicant_id', 'applicant_firstName', 'applicant_lastName', 'applicant_email')->get(),
            'Applicant_list'        => $applicantList,
            'Courses_list'          => $coursesList,
        ]);
    }
}
