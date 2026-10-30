<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\Grade;
use App\Models\Rubric;
use App\Models\TeacherDocument;
use App\Models\TradeCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        if ($user->hasRole(['super_admin', 'lms_admin'])) {
            return $this->adminDashboard($user);
        }

        if ($user->hasRole('facilitator')) {
            return $this->facilitatorDashboard($user);
        }

        if ($user->hasRole('teacher')) {
            return $this->teacherDashboard($user);
        }

        return $this->studentDashboard($user);
    }

    protected function adminDashboard(User $user): Response
    {
        $totalTeachers = User::role('teacher')->count();
        $activeTeachers = User::role('teacher')->where('account_status', 'active')->count();
        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $draftCourses = Course::where('status', 'draft')->count();
        $practiceCourses = Course::where('is_practice', true)->count();
        $institutionalCourses = Course::where('is_practice', false)->count();
        $totalAssignments = Assignment::count();
        $totalRubrics = Rubric::count();
        $totalSubmissions = AssignmentSubmission::count();
        $gradedSubmissions = AssignmentSubmission::where('status', 'graded')->count();

        // Day 8 Completion stats
        $totalDay8Records = Day8PracticeProgress::count();
        $completedDay8 = Day8PracticeProgress::whereNotNull('completed_at')->count();
        $completionRate = $totalDay8Records > 0 ? (int) round(($completedDay8 / $totalDay8Records) * 100) : 0;

        // Trade Distribution
        $tradeDistribution = TradeCategory::withCount('courses')
            ->orderByDesc('courses_count')
            ->take(8)
            ->get(['id', 'name', 'courses_count', 'icon']);

        // Recent Activity
        $recentAudits = AuditLog::with('user:id,name,email')
            ->latest('created_at')
            ->take(6)
            ->get();

        $recentDocuments = TeacherDocument::latest('created_at')->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_teachers' => $totalTeachers,
                'active_teachers' => $activeTeachers,
                'total_courses' => $totalCourses,
                'published_courses' => $publishedCourses,
                'draft_courses' => $draftCourses,
                'practice_courses' => $practiceCourses,
                'institutional_courses' => $institutionalCourses,
                'total_assignments' => $totalAssignments,
                'total_rubrics' => $totalRubrics,
                'total_submissions' => $totalSubmissions,
                'graded_submissions' => $gradedSubmissions,
                'day8_completion_rate' => $completionRate,
                'total_documents' => TeacherDocument::count(),
            ],
            'trade_distribution' => $tradeDistribution,
            'recent_audits' => $recentAudits,
            'recent_documents' => $recentDocuments,
        ]);
    }

    protected function facilitatorDashboard(User $user): Response
    {
        // Teachers and their progress
        $teachers = User::role('teacher')
            ->with(['day8Progress', 'courses' => function ($q) {
                $q->latest()->take(3);
            }])
            ->latest()
            ->paginate(15);

        $pendingReviews = Course::where('status', 'under_review')->count();
        $verifiedWizards = Day8PracticeProgress::where('is_verified_by_facilitator', true)->count();

        return Inertia::render('Facilitator/Dashboard', [
            'teachers' => $teachers,
            'metrics' => [
                'total_assigned_teachers' => User::role('teacher')->count(),
                'pending_course_reviews' => $pendingReviews,
                'verified_wizards' => $verifiedWizards,
            ],
        ]);
    }

    protected function teacherDashboard(User $user): Response
    {
        $courses = Course::where('user_id', $user->id)
            ->with(['category:id,name', 'sections:id,course_id,title'])
            ->withCount(['students', 'assignments'])
            ->latest()
            ->get();

        $day8Progress = Day8PracticeProgress::firstOrCreate(['user_id' => $user->id]);

        $pendingSubmissions = AssignmentSubmission::whereHas('assignment.course', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'submitted')->with(['assignment:id,title', 'student:id,name'])->take(5)->get();

        $practiceLearners = User::where('sandbox_owner_id', $user->id)->get(['id', 'name', 'email', 'is_synthetic', 'created_at']);

        $recentDocuments = TeacherDocument::published()
            ->forAudience('teacher')
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('Teacher/Dashboard', [
            'courses' => $courses,
            'day8_progress' => [
                'completed_count' => $day8Progress->completedStepsCount(),
                'percentage' => $day8Progress->progressPercentage(),
                'is_verified' => $day8Progress->is_verified_by_facilitator,
                'facilitator_feedback' => $day8Progress->facilitator_feedback,
                'data' => $day8Progress,
            ],
            'pending_submissions' => $pendingSubmissions,
            'practice_learners' => $practiceLearners,
            'recent_documents' => $recentDocuments,
        ]);
    }

    protected function studentDashboard(User $user): Response
    {
        $enrolledCourses = $user->enrolledCourses()
            ->with(['category:id,name', 'user:id,name'])
            ->get();

        $upcomingAssignments = Assignment::whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('cut_off_date')->orWhere('cut_off_date', '>=', now());
            })
            ->with('course:id,title,code')
            ->latest()
            ->take(5)
            ->get();

        $recentGrades = Grade::where('user_id', $user->id)
            ->where('is_released', true)
            ->with(['course:id,title,code', 'assignment:id,title'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Student/Dashboard', [
            'enrolled_courses' => $enrolledCourses,
            'upcoming_assignments' => $upcomingAssignments,
            'recent_grades' => $recentGrades,
        ]);
    }
}
