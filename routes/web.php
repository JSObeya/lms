<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Day8WizardController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\LearningObjectiveController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RubricController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserManagementController;
use App\Models\Course;
use App\Models\TradeCategory;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $featuredCourses = Course::where('status', 'published')
        ->with(['category', 'user:id,name'])
        ->latest()
        ->take(6)
        ->get();

    $categories = TradeCategory::where('is_active', true)
        ->orderBy('order_index')
        ->get();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'featured_courses' => $featuredCourses,
        'categories' => $categories,
    ]);
})->name('home');

// Trainee User Manual & Guide (Publicly accessible for learners)
Route::get('/trainee-guide', function () {
    return Inertia::render('Trainee/Guide');
})->name('trainee.guide');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dynamic Role-Scoping Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course Management & Builder
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{id}/restore', [CourseController::class, 'restore'])->name('courses.restore');
    Route::get('/courses/{course}/builder', [CourseController::class, 'builder'])->name('courses.builder');
    Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');

    // Sections & Lessons
    Route::post('/courses/{course}/sections', [CourseSectionController::class, 'store'])->name('sections.store');
    Route::put('/sections/{section}', [CourseSectionController::class, 'update'])->name('sections.update');
    Route::post('/courses/{course}/sections/reorder', [CourseSectionController::class, 'reorder'])->name('sections.reorder');
    Route::delete('/sections/{section}', [CourseSectionController::class, 'destroy'])->name('sections.destroy');
    Route::post('/sections/{section}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Measurable Learning Objectives
    Route::post('/courses/{course}/objectives', [LearningObjectiveController::class, 'store'])->name('objectives.store');
    Route::put('/objectives/{objective}', [LearningObjectiveController::class, 'update'])->name('objectives.update');
    Route::delete('/objectives/{objective}', [LearningObjectiveController::class, 'destroy'])->name('objectives.destroy');

    // Resources & Secure Private Streaming
    Route::post('/courses/{course}/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');

    // Assignments & Submissions
    Route::get('/courses/{course}/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post('/assignments/{id}/restore', [AssignmentController::class, 'restore'])->name('assignments.restore');
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submission-attachments/{attachment}/download', [SubmissionController::class, 'downloadAttachment'])->name('submission-attachments.download');

    // Rubrics
    Route::get('/rubrics', [RubricController::class, 'index'])->name('rubrics.index');
    Route::post('/rubrics', [RubricController::class, 'store'])->name('rubrics.store');
    Route::get('/rubrics/{rubric}', [RubricController::class, 'show'])->name('rubrics.show');
    Route::post('/rubrics/{rubric}/duplicate', [RubricController::class, 'duplicate'])->name('rubrics.duplicate');
    Route::delete('/rubrics/{rubric}', [RubricController::class, 'destroy'])->name('rubrics.destroy');
    Route::post('/rubrics/{id}/restore', [RubricController::class, 'restore'])->name('rubrics.restore');

    // Grading & Gradebook
    Route::get('/courses/{course}/gradebook', [GradingController::class, 'gradebook'])->name('gradebook.show');
    Route::post('/submissions/{submission}/grade-rubric', [GradingController::class, 'gradeWithRubric'])->name('submissions.grade-rubric');
    Route::post('/grades/{grade}/toggle-release', [GradingController::class, 'releaseGrade'])->name('grades.toggle-release');
    Route::get('/courses/{course}/gradebook/export-csv', [GradingController::class, 'exportCsv'])->name('gradebook.export-csv');

    // Day 8 TVET Practical Wizard
    Route::get('/day8-wizard', [Day8WizardController::class, 'index'])->name('day8.wizard');
    Route::post('/day8-wizard/generate-practice-learner', [Day8WizardController::class, 'generatePracticeLearner'])->name('day8.generate-practice-learner');
    Route::post('/day8-wizard/{progress}/verify', [Day8WizardController::class, 'facilitatorVerify'])->name('day8.verify');

    // Digital Teaching Portfolio
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio/items', [PortfolioController::class, 'storeItem'])->name('portfolio.items.store');
    Route::post('/portfolio/share', [PortfolioController::class, 'share'])->name('portfolio.share');

    // User Administration & Bulk 200 Teacher Import
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{id}/restore', [UserManagementController::class, 'restore'])->name('admin.users.restore');
    Route::delete('/admin/users/{id}/force-delete', [UserManagementController::class, 'forceDelete'])->name('admin.users.force-delete');
    Route::post('/admin/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::post('/admin/users/bulk-import', [UserManagementController::class, 'bulkImportTeachers'])->name('admin.users.bulk-import');

    // System Audit Trail & Logs
    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    Route::post('/admin/audit-logs/clear-old', [AuditLogController::class, 'clearOld'])->name('admin.audit-logs.clear-old');
});

require __DIR__.'/auth.php';
