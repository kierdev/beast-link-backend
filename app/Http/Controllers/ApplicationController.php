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



    public function getAdminDashboardData()
    {
        // Gather stats
        $totalApplicants = Application::count();
        $passedApplicants = Application::where('status', 'passed')->count();
        $failedApplicants = Application::where('status', 'failed')->count();
        $pendingApplicants = Application::where('status', 'pending')->count();
        $totalCourses = Program::count();

        // Mocked Chart Data
        $barChartData = Program::limit(5)->get()->map(function ($program, $index) {
            $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"];
            return [
                'label' => $program->program_code,
                'value' => Application::where('program_code', $program->program_code)->count(),
                'color' => $colors[$index % count($colors)],
            ];
        });

        $lineChartData = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'])->map(function ($month, $index) {
            return [
                'label' => $month,
                'value' => rand(40, 100), // Replace with actual trend if available
            ];
        });

        // Pie chart
        $pieChartData = [
            ['label' => 'Failed', 'value' => $failedApplicants, 'color' => '#FF0000'],
            ['label' => 'Passed', 'value' => $passedApplicants, 'color' => '#00ff00'],
            ['label' => 'Pending', 'value' => $pendingApplicants, 'color' => '#ffa500'],
        ];

        // Sample applicants list
        $applicants = Applicant::limit(50)->get()->map(function ($applicant, $i) {
            $statuses = ['Pending', 'Approved', 'Rejected'];
            $courses = ['BSIT', 'BSCS', 'BSCE', 'BSA', 'BSSW'];
            return [
                'id' => 'APP-' . (1000 + $i),
                'name' => $applicant->First_Name . ' ' . $applicant->Last_Name,
                'email' => $applicant->applicant_email,
                'course' => $courses[$i % count($courses)],
                'status' => $statuses[$i % count($statuses)],
                'appliedDate' => now()->subDays($i)->toDateString(),
                'documents' => $i % 4 === 0 ? 'Incomplete' : 'Complete',
            ];
        });

        // Courses
        $courses = Program::get()->map(function ($program, $i) {
            return [
                'id' => $program->id,
                'code' => $program->program_code,
                'name' => $program->program_name,
                'seats' => 100 + ($i * 10),
                'enrolled' => rand(40, 100),
            ];
        });

        // Notifications (placeholder)
        $notifications = [
            [
                'id' => 1,
                'type' => 'Application Update',
                'message' => 'Your Computer Science application has been received.',
                'time' => '10 minutes ago',
                'read' => false,
            ],
        ];

        return response()->json([
            'stats' => [
                'totalApplicants' => $totalApplicants,
                'passedApplicants' => $passedApplicants,
                'failedApplicants' => $failedApplicants,
                'pendingApplicants' => $pendingApplicants,
                'totalCourses' => $totalCourses,
            ],
            'charts' => [
                'pieChartData' => $pieChartData,
                'barChartData' => $barChartData,
                'lineChartData' => $lineChartData,
            ],
            'applicants' => $applicants,
            'courses' => $courses,
            'notifications' => $notifications,
        ]);
    }
    public function getChairpersonDashboardData(Request $request)
    {
        $programCode = $request->query('program_code');

        if (!$programCode) {
            return response()->json(['error' => 'program_code is required'], 400);
        }

        // Basic counts
        $query = Application::where('program_code', $programCode);
        $totalApplicants = $query->count();
        $passedApplicants = $query->clone()->where('status', 'passed')->count();
        $failedApplicants = $query->clone()->where('status', 'failed')->count();
        $pendingApplicants = $query->clone()->where('status', 'pending')->count();

        // Exam score distribution
        $scoreDistribution = [
            '90-100' => 0,
            '80-89' => 0,
            '70-79' => 0,
            '60-69' => 0,
            'Below 60' => 0,
        ];

        $scores = $query->clone()->pluck('exam_score');
        foreach ($scores as $score) {
            if ($score >= 90) $scoreDistribution['90-100']++;
            elseif ($score >= 80) $scoreDistribution['80-89']++;
            elseif ($score >= 70) $scoreDistribution['70-79']++;
            elseif ($score >= 60) $scoreDistribution['60-69']++;
            elseif ($score !== null) $scoreDistribution['Below 60']++;
        }

        $program = Program::where('program_code', $programCode)->first();
        $departmentName = $program->program_name ?? 'N/A';

        // Fake unread notification count (or fetch from DB if available)
        $unreadNotifications = 3;

        // Build programStatistics
        $programStatistics = [
            'totalApplicants' => $totalApplicants,
            'passedApplicants' => $passedApplicants,
            'failedApplicants' => $failedApplicants,
            'pendingApplicants' => $pendingApplicants,
            'department' => $departmentName,
            'unreadNotifications' => $unreadNotifications,
            'examScoreDistribution' => array_map(
                fn($label, $value) => ['label' => $label, 'value' => $value],
                array_keys($scoreDistribution),
                array_values($scoreDistribution)
            ),
        ];

        // Applicant list
        $applicants = Application::with('applicant')
            ->where('program_code', $programCode)
            ->get()
            ->map(function ($application, $i) {
                return [
                    'id' => 'APP-' . (1000 + $i),
                    'name' => optional($application->applicant)->applicant_firstName . ' ' . optional($application->applicant)->applicant_lastName,
                    'email' => optional($application->applicant)->applicant_email,
                    'status' => ucfirst($application->status),
                    'appliedDate' => $application->application_date,
                    'documents' => $i % 4 === 0 ? 'Incomplete' : 'Complete',
                ];
            });

        // Exams list
        $exams = Exam::where('program_code', $programCode)
            ->get()
            ->map(function ($exam, $i) {
                return [
                    'id' => 'EXM-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'name' => $exam->exam_title,
                    'date' => $exam->exam_date,
                    'time' => $exam->exam_time,
                    'location' => $exam->exam_location,
                    'status' => $exam->exam_date < now()->toDateString() ? 'Completed' : 'Scheduled',
                    'participants' => Application::where('exam_id', $exam->exam_id)->count(),
                ];
            });

        return response()->json([
            'programStatistics' => $programStatistics,
            'applicants' => $applicants,
            'exams' => $exams,
        ]);
    }


    public function getInterviewerDashboardData()
    {
        $applications = Application::with(['exam', 'program', 'applicant'])->get();

        $today = now()->toDateString();
        $dashboardStats = [
            'todaysInterviews' => 0,
            'passedInterviews' => 0,
            'failedInterviews' => 0,
            'pendingRemarks'   => 0,
        ];

        $upcomingInterviews = [];
        $pendingRemarks = [];
        $completedInterviews = [];

        foreach ($applications as $application) {
            $exam = $application->exam;
            $applicant = $application->applicant;
            $program = $application->program;

            if (!$exam || !$applicant || !$program) {
                continue;
            }

            $name = $applicant->applicant_firstName . ' ' . $applicant->applicant_lastName;
            $date = \Carbon\Carbon::parse($exam->exam_date)->format('F d, Y');
            $time = \Carbon\Carbon::parse($exam->exam_time)->format('g:i A');
            $location = $exam->exam_location;
            $programTitle = $program->program_name;
            $status = $application->status;

            if ($exam->exam_date === $today) {
                $dashboardStats['todaysInterviews']++;
            }

            if ($exam->exam_date > $today) {
                $upcomingInterviews[] = [
                    'id'       => $application->id,
                    'name'     => $name,
                    'date'     => $date,
                    'time'     => $time,
                    'program'  => $programTitle,
                    'location' => $location,
                    'status'   => 'scheduled',
                ];
            }

            if ($exam->exam_date < $today) {
                $interviewResult = $application->remarks ?? null;

                if (!$interviewResult) {
                    $pendingRemarks[] = [
                        'id'       => $application->id,
                        'name'     => $name,
                        'date'     => $date,
                        'time'     => $time,
                        'program'  => $programTitle,
                        'location' => $location,
                        'status'   => 'pending',
                    ];
                    $dashboardStats['pendingRemarks']++;
                } else {
                    $result = strtolower($interviewResult);
                    if ($result === 'passed') {
                        $dashboardStats['passedInterviews']++;
                    } elseif ($result === 'failed') {
                        $dashboardStats['failedInterviews']++;
                    }

                    $completedInterviews[] = [
                        'id'       => $application->id,
                        'name'     => $name,
                        'date'     => $date,
                        'time'     => $time,
                        'program'  => $programTitle,
                        'location' => $location,
                        'status'   => 'completed',
                        'result'   => $result,
                    ];
                }
            }
        }

        return response()->json([
            'dashboardStats'      => $dashboardStats,
            'upcomingInterviews'  => $upcomingInterviews,
            'pendingRemarks'      => $pendingRemarks,
            'completedInterviews' => $completedInterviews,
        ]);
    }

    public function getApplicantDashboardData()
    {
        // Hardcoded data simulating what would typically come from DB or services
        $courses = [
            [
                'id'          => 1,
                'title'       => "Computer Science",
                'count'       => "5,000+ Courses",
                'description' => "Covers algorithms, programming, and problem-solving, essential for computing and software development.",
                'college'     => "College of Technology",
                'date'        => "Apr 25, 2025",
                'category'    => "technology",
            ],
            [
                'id'          => 2,
                'title'       => "Information Technology",
                'count'       => "5,000+ Courses",
                'description' => "Covers networking, cybersecurity, and software development, essential for IT support and system management.",
                'college'     => "College of Education",
                'date'        => "Apr 25, 2025",
                'category'    => "technology",
            ],
            [
                'id'          => 3,
                'title'       => "Computer Engineering",
                'count'       => "5,000+ Courses",
                'description' => "Covers hardware, software, and embedded systems, essential for designing and optimizing computing technologies.",
                'college'     => "College of Technology",
                'date'        => "Apr 25, 2025",
                'category'    => "technology",
            ],
            [
                'id'          => 4,
                'title'       => "Elementary Education",
                'count'       => "5,000+ Courses",
                'description' => "Prepares future teachers for primary education, focusing on child development, pedagogy, and subject-specific teaching for Grades 1-6.",
                'college'     => "College of Education",
                'date'        => "Apr 25, 2025",
                'category'    => "education",
            ],
            [
                'id'          => 5,
                'title'       => "Secondary Education",
                'count'       => "5,000+ Courses",
                'description' => "Trains educators to teach in junior and senior high school (Grades 7-12), specializing in subjects like Math, Science, English, or Social Studies.",
                'college'     => "College of Education",
                'date'        => "Apr 25, 2025",
                'category'    => "education",
            ],
            [
                'id'          => 6,
                'title'       => "Special Education",
                'count'       => "5,000+ Courses",
                'description' => "Equips teachers with strategies to support students with disabilities and special needs, promoting inclusive and adaptive learning.",
                'college'     => "College of Education",
                'date'        => "Apr 25, 2025",
                'category'    => "education",
            ],
            [
                'id'          => 7,
                'title'       => "Accountancy",
                'count'       => "5,000+ Courses",
                'description' => "Prepares students for careers in accounting, auditing, and taxation, with a strong focus on financial reporting and CPA licensure.",
                'college'     => "College of Business and Accountancy",
                'date'        => "Apr 25, 2025",
                'category'    => "business",
            ],
            [
                'id'          => 8,
                'title'       => "Human Resource Development Management",
                'count'       => "5,000+ Courses",
                'description' => "Equips students with skills in recruitment, training, and labor relations, essential for effective workforce management and organizational development.",
                'college'     => "College of Business and Accountancy",
                'date'        => "Apr 25, 2025",
                'category'    => "business",
            ],
            [
                'id'          => 9,
                'title'       => "Financial Management",
                'count'       => "5,000+ Courses",
                'description' => "Teaches financial analysis, investment strategies, and risk management, preparing students for careers in banking, corporate finance, and investment planning.",
                'college'     => "College of Business and Accountancy",
                'date'        => "Apr 25, 2025",
                'category'    => "business",
            ],
            
        ];

        $applications = [
            [
                'id'     => 1,
                'name'   => "Computer Science",
                'date'   => "2025-02-15",
                'status' => "pending",
            ],
            [
                'id'     => 2,
                'name'   => "Human Resource Development Management",
                'date'   => "2025-02-20",
                'status' => "pending",
            ],
        ];

        $progress = [
            ['label' => "Application", 'status' => "completed"],
            ['label' => "Document", 'status' => "current"],
            ['label' => "Exam", 'status' => "upcoming"],
            ['label' => "Interview", 'status' => "upcoming"],
            ['label' => "Decision", 'status' => "upcoming"],
            ['label' => "Enrollment", 'status' => "upcoming"],
        ];

        return response()->json([
            'courses'      => $courses,
            'applications' => $applications,
            'progress'     => $progress,
        ]);
    }
}
