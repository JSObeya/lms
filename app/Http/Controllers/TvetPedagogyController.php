<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CompetencyLesson;
use App\Models\CompetencyLessonRevision;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\InclusiveLessonAdaptation;
use App\Models\PedagogyReview;
use App\Models\PedagogyTrainingProgress;
use App\Models\TeacherPortfolio;
use App\Models\TradeCategory;
use App\Models\TvetOccupationalStandard;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TvetPedagogyController extends Controller
{
    /**
     * TVET Pedagogy Studio Hub:
     * Unified overview of the progressive training journey (Days 8, 9, 10, 11, 12).
     */
    public function studio(Request $request): Response
    {
        $user = $request->user();

        // 1. Day 8 Progress
        $day8Progress = Day8PracticeProgress::firstOrCreate(['user_id' => $user->id]);

        // 2. Day 9 & Day 10 Pedagogy Progress
        $pedagogyProgress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);

        // 3. User's active lessons
        $myLessons = CompetencyLesson::where('user_id', $user->id)
            ->with(['tradeCategory', 'occupationalStandard', 'inclusiveAdaptation', 'course:id,title'])
            ->latest()
            ->get();

        // If progress doesn't have a linked lesson yet, link the most recent one
        if (! $pedagogyProgress->competency_lesson_id && $myLessons->isNotEmpty()) {
            $pedagogyProgress->competency_lesson_id = $myLessons->first()->id;
            $pedagogyProgress->recalculateForLesson($myLessons->first());
        }

        // 4. System Templates (8 IDEAS-TVET trades)
        $templates = CompetencyLesson::where('is_template', true)
            ->with(['tradeCategory', 'occupationalStandard', 'inclusiveAdaptation'])
            ->get();

        // 5. Digital Portfolio
        $portfolio = TeacherPortfolio::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => "{$user->name}'s TVET Teaching Portfolio"]
        );
        $portfolio->load('items');

        // 6. User's courses available for lesson linking
        $courses = Course::where('user_id', $user->id)->get(['id', 'title', 'is_practice']);

        return Inertia::render('Pedagogy/Studio', [
            'my_lessons' => $myLessons,
            'templates' => $templates,
            'courses' => $courses,
            'day8_progress' => [
                'percentage' => $day8Progress->progressPercentage(),
                'is_completed' => $day8Progress->isFullyCompleted(),
                'is_verified' => $day8Progress->is_verified_by_facilitator,
                'completed_steps' => $day8Progress->completedStepsCount(),
            ],
            'pedagogy_progress' => [
                'day9_demonstration_ready' => (bool) $pedagogyProgress->day9_demonstration_ready,
                'day9_exercise_ready' => (bool) $pedagogyProgress->day9_exercise_ready,
                'day9_criteria_ready' => (bool) $pedagogyProgress->day9_criteria_ready,
                'day9_completed' => (bool) $pedagogyProgress->day9_completed,
                'day10_differentiated_ready' => (bool) $pedagogyProgress->day10_differentiated_ready,
                'day10_udl_ready' => (bool) $pedagogyProgress->day10_udl_ready,
                'day10_safety_verified' => (bool) $pedagogyProgress->day10_safety_verified,
                'day10_completed' => (bool) $pedagogyProgress->day10_completed,
                'day9_completed_at' => $pedagogyProgress->day9_completed_at?->format('Y-m-d H:i'),
                'day10_completed_at' => $pedagogyProgress->day10_completed_at?->format('Y-m-d H:i'),
                'active_lesson_id' => $pedagogyProgress->competency_lesson_id,
            ],
            'portfolio' => [
                'completion_percentage' => $portfolio->completion_percentage,
                'total_items' => $portfolio->items->count(),
            ],
        ]);
    }

    /**
     * Lesson Builder Index / List.
     */
    public function lessonBuilderIndex(Request $request): Response
    {
        $user = $request->user();

        $lessons = CompetencyLesson::where('user_id', $user->id)
            ->with(['tradeCategory', 'occupationalStandard', 'course:id,title', 'inclusiveAdaptation'])
            ->latest()
            ->paginate(12);

        $templates = CompetencyLesson::where('is_template', true)
            ->with(['tradeCategory', 'occupationalStandard'])
            ->get();

        return Inertia::render('Pedagogy/LessonBuilderIndex', [
            'lessons' => $lessons,
            'templates' => $templates,
        ]);
    }

    /**
     * Create a new Competency-Based Practical Lesson.
     */
    public function lessonBuilderCreate(Request $request): Response
    {
        Gate::authorize('create', CompetencyLesson::class);

        $user = $request->user();
        $templateId = $request->query('template_id');
        $courseId = $request->query('course_id');

        $template = null;
        if ($templateId) {
            $template = CompetencyLesson::where('is_template', true)->find($templateId);
        }

        $standards = TvetOccupationalStandard::where('is_active', true)
            ->with('tradeCategory:id,name,slug')
            ->orderBy('is_official', 'desc')
            ->orderBy('code')
            ->get();

        $tradeCategories = TradeCategory::where('is_tvet_trade', true)
            ->orWhere('is_active', true)
            ->orderBy('order_index')
            ->get(['id', 'name', 'slug', 'icon']);

        $courses = Course::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'title', 'is_practice', 'trade_category_id']);

        return Inertia::render('Pedagogy/LessonBuilder', [
            'is_editing' => false,
            'lesson' => $template ? [
                'title' => $template->title,
                'occupational_task' => $template->occupational_task,
                'trade_category_id' => $template->trade_category_id,
                'occupational_standard_id' => $template->occupational_standard_id,
                'course_id' => $courseId ? (int) $courseId : null,
                'duration_minutes' => $template->duration_minutes,
                'target_level' => $template->target_level,
                'learning_outcomes' => $template->learning_outcomes,
                'demonstration_plan' => $template->demonstration_plan,
                'guided_practice' => $template->guided_practice,
                'independent_practice' => $template->independent_practice,
                'project_based_tasks' => $template->project_based_tasks,
                'fault_diagnosis_scenarios' => $template->fault_diagnosis_scenarios,
                'work_based_learning' => $template->work_based_learning,
                'assessment_criteria' => $template->assessment_criteria,
                'status' => 'draft',
            ] : [
                'title' => '',
                'occupational_task' => '',
                'trade_category_id' => null,
                'occupational_standard_id' => null,
                'course_id' => $courseId ? (int) $courseId : null,
                'duration_minutes' => 120,
                'target_level' => 'NSQF Level 3 / Intermediate Craftsperson',
                'learning_outcomes' => [],
                'demonstration_plan' => [],
                'guided_practice' => [],
                'independent_practice' => [],
                'project_based_tasks' => [],
                'fault_diagnosis_scenarios' => [],
                'work_based_learning' => [],
                'assessment_criteria' => [],
                'status' => 'draft',
            ],
            'standards' => $standards,
            'trade_categories' => $tradeCategories,
            'courses' => $courses,
        ]);
    }

    /**
     * Store new Competency-Based Practical Lesson.
     */
    public function lessonBuilderStore(Request $request): RedirectResponse
    {
        Gate::authorize('create', CompetencyLesson::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'occupational_task' => 'required|string|max:500',
            'trade_category_id' => 'nullable|exists:trade_categories,id',
            'occupational_standard_id' => 'nullable|exists:tvet_occupational_standards,id',
            'course_id' => 'nullable|exists:courses,id',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'target_level' => 'nullable|string|max:100',
            'learning_outcomes' => 'nullable|array',
            'demonstration_plan' => 'nullable|array',
            'guided_practice' => 'nullable|array',
            'independent_practice' => 'nullable|array',
            'project_based_tasks' => 'nullable|array',
            'fault_diagnosis_scenarios' => 'nullable|array',
            'work_based_learning' => 'nullable|array',
            'assessment_criteria' => 'nullable|array',
            'status' => 'required|in:draft,published,in_review',
        ]);

        $user = $request->user();

        $lesson = DB::transaction(function () use ($validated, $user) {
            $lesson = CompetencyLesson::create(array_merge($validated, [
                'user_id' => $user->id,
                'version' => 1,
                'is_template' => false,
            ]));

            // Revision snapshot
            CompetencyLessonRevision::create([
                'competency_lesson_id' => $lesson->id,
                'user_id' => $user->id,
                'version' => 1,
                'snapshot_data' => $lesson->toArray(),
                'change_summary' => 'Initial Lesson Creation',
            ]);

            // Automatically sync with user's Pedagogy Progress
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);
            $progress->competency_lesson_id = $lesson->id;
            $progress->recalculateForLesson($lesson);

            // Sync with Digital Teaching Portfolio if compliant
            if ($lesson->isDay9Compliant()) {
                $portfolio = TeacherPortfolio::firstOrCreate(['user_id' => $user->id]);
                $portfolio->items()->updateOrCreate(
                    [
                        'teacher_portfolio_id' => $portfolio->id,
                        'category' => 'lesson_documents',
                        'title' => "Day 9 Competency Lesson: {$lesson->title}",
                    ],
                    [
                        'description' => "Occupational Task: {$lesson->occupational_task} with 4-step demonstration plan, learner practice, and measurable criteria.",
                        'url' => route('pedagogy.lessons.edit', $lesson->id),
                    ]
                );
            }

            AuditLog::record(
                'competency_lesson_created',
                CompetencyLesson::class,
                $lesson->id,
                ['title' => $lesson->title, 'task' => $lesson->occupational_task]
            );

            return $lesson;
        });

        return redirect()->route('pedagogy.lessons.edit', $lesson->id)
            ->with('success', 'Competency-based practical lesson saved successfully!');
    }

    /**
     * Edit an existing Competency-Based Lesson.
     */
    public function lessonBuilderEdit(Request $request, CompetencyLesson $lesson): Response
    {
        Gate::authorize('view', $lesson);

        $user = $request->user();
        $lesson->load(['tradeCategory', 'occupationalStandard', 'course:id,title', 'inclusiveAdaptation', 'reviews.facilitator:id,name']);

        $standards = TvetOccupationalStandard::where('is_active', true)
            ->with('tradeCategory:id,name,slug')
            ->orderBy('is_official', 'desc')
            ->orderBy('code')
            ->get();

        $tradeCategories = TradeCategory::where('is_tvet_trade', true)
            ->orWhere('is_active', true)
            ->orderBy('order_index')
            ->get(['id', 'name', 'slug', 'icon']);

        $courses = Course::where('user_id', $user->id)
            ->latest()
            ->get(['id', 'title', 'is_practice', 'trade_category_id']);

        return Inertia::render('Pedagogy/LessonBuilder', [
            'is_editing' => true,
            'lesson' => $lesson,
            'standards' => $standards,
            'trade_categories' => $tradeCategories,
            'courses' => $courses,
            'can_update' => Gate::allows('update', $lesson),
        ]);
    }

    /**
     * Update an existing Competency-Based Lesson.
     */
    public function lessonBuilderUpdate(Request $request, CompetencyLesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'occupational_task' => 'required|string|max:500',
            'trade_category_id' => 'nullable|exists:trade_categories,id',
            'occupational_standard_id' => 'nullable|exists:tvet_occupational_standards,id',
            'course_id' => 'nullable|exists:courses,id',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'target_level' => 'nullable|string|max:100',
            'learning_outcomes' => 'nullable|array',
            'demonstration_plan' => 'nullable|array',
            'guided_practice' => 'nullable|array',
            'independent_practice' => 'nullable|array',
            'project_based_tasks' => 'nullable|array',
            'fault_diagnosis_scenarios' => 'nullable|array',
            'work_based_learning' => 'nullable|array',
            'assessment_criteria' => 'nullable|array',
            'status' => 'required|in:draft,published,in_review',
        ]);

        $user = $request->user();

        DB::transaction(function () use ($validated, $lesson, $user) {
            $nextVersion = $lesson->version + 1;
            $lesson->update(array_merge($validated, [
                'version' => $nextVersion,
            ]));

            // Log revision
            CompetencyLessonRevision::create([
                'competency_lesson_id' => $lesson->id,
                'user_id' => $user->id,
                'version' => $nextVersion,
                'snapshot_data' => $lesson->toArray(),
                'change_summary' => "Lesson updated to version {$nextVersion}",
            ]);

            // Sync with Pedagogy Progress
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);
            $progress->competency_lesson_id = $lesson->id;
            $progress->recalculateForLesson($lesson);

            // Sync with Portfolio if compliant
            if ($lesson->isDay9Compliant()) {
                $portfolio = TeacherPortfolio::firstOrCreate(['user_id' => $user->id]);
                $portfolio->items()->updateOrCreate(
                    [
                        'teacher_portfolio_id' => $portfolio->id,
                        'category' => 'lesson_documents',
                        'title' => "Day 9 Competency Lesson: {$lesson->title}",
                    ],
                    [
                        'description' => "Occupational Task: {$lesson->occupational_task} with 4-step demonstration plan, learner practice, and measurable criteria.",
                        'url' => route('pedagogy.lessons.edit', $lesson->id),
                    ]
                );
            }

            AuditLog::record(
                'competency_lesson_updated',
                CompetencyLesson::class,
                $lesson->id,
                ['version' => $nextVersion, 'status' => $lesson->status]
            );
        });

        return redirect()->back()->with('success', 'Lesson updated and versioned successfully!');
    }

    /**
     * Duplicate a Competency Lesson or Template.
     */
    public function duplicateLesson(Request $request, CompetencyLesson $lesson): RedirectResponse
    {
        Gate::authorize('duplicate', $lesson);

        $user = $request->user();

        $newLesson = DB::transaction(function () use ($lesson, $user) {
            $replica = $lesson->replicate([
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $replica->user_id = $user->id;
            $replica->title = "Copy of {$lesson->title}";
            $replica->is_template = false;
            $replica->template_trade = null;
            $replica->status = 'draft';
            $replica->version = 1;
            $replica->save();

            // If source lesson has an inclusive adaptation, replicate it as well
            if ($lesson->inclusiveAdaptation) {
                $replicaAdaptation = $lesson->inclusiveAdaptation->replicate([
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ]);
                $replicaAdaptation->competency_lesson_id = $replica->id;
                $replicaAdaptation->user_id = $user->id;
                $replicaAdaptation->status = 'draft';
                $replicaAdaptation->save();
            }

            AuditLog::record(
                'competency_lesson_duplicated',
                CompetencyLesson::class,
                $replica->id,
                ['original_id' => $lesson->id, 'title' => $replica->title]
            );

            return $replica;
        });

        return redirect()->route('pedagogy.lessons.edit', $newLesson->id)
            ->with('success', 'Lesson duplicated successfully! You can now customize your copy.');
    }

    /**
     * Export / Printable view of Competency Lesson.
     */
    public function exportLesson(CompetencyLesson $lesson): Response
    {
        Gate::authorize('view', $lesson);

        $lesson->load(['tradeCategory', 'occupationalStandard', 'user:id,name,institution', 'inclusiveAdaptation']);

        return Inertia::render('Pedagogy/ExportLesson', [
            'lesson' => $lesson,
            'printed_at' => now()->format('d M Y, H:i'),
        ]);
    }

    /**
     * Inclusive Lesson Designer Hub / Redirect to active lesson.
     */
    public function inclusiveDesignerIndex(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);

        $lessonId = $request->query('lesson_id') ?: $progress->competency_lesson_id;

        if ($lessonId) {
            $lesson = CompetencyLesson::find($lessonId);
            if ($lesson && Gate::allows('view', $lesson)) {
                return redirect()->route('pedagogy.inclusive-designer.edit', $lesson->id);
            }
        }

        // Show picker if no specific lesson active
        $myLessons = CompetencyLesson::where('user_id', $user->id)->latest()->get();
        $templates = CompetencyLesson::where('is_template', true)->get();

        return Inertia::render('Pedagogy/InclusiveDesignerIndex', [
            'my_lessons' => $myLessons,
            'templates' => $templates,
        ]);
    }

    /**
     * Inclusive Lesson Designer Editor (Day 10).
     */
    public function inclusiveDesignerEdit(Request $request, CompetencyLesson $lesson): Response
    {
        Gate::authorize('view', $lesson);

        $user = $request->user();
        $lesson->load(['tradeCategory', 'occupationalStandard', 'course:id,title']);

        // Load or initialize Inclusive Adaptation
        $adaptation = $lesson->inclusiveAdaptation;
        if (! $adaptation) {
            $adaptation = new InclusiveLessonAdaptation([
                'competency_lesson_id' => $lesson->id,
                'user_id' => $user->id,
                'structured_plan' => [
                    ['phase' => 'Safety Briefing & Motivation', 'duration_min' => 15, 'teacher_activity' => '', 'learner_activity' => ''],
                    ['phase' => 'Instructor Demonstration', 'duration_min' => 25, 'teacher_activity' => '', 'learner_activity' => ''],
                    ['phase' => 'Guided Hands-On Practice', 'duration_min' => 40, 'teacher_activity' => '', 'learner_activity' => ''],
                    ['phase' => 'Differentiated Practical Lab', 'duration_min' => 75, 'teacher_activity' => '', 'learner_activity' => ''],
                    ['phase' => 'Tool Return, Clean-up & Debrief', 'duration_min' => 25, 'teacher_activity' => '', 'learner_activity' => ''],
                ],
                'differentiated_activities' => [
                    'beginner' => [],
                    'intermediate' => [],
                    'advanced' => [],
                ],
                'udl_engagement' => [],
                'udl_representation' => [],
                'udl_action_expression' => [],
                'learner_accommodations' => [],
                'safety_standards_maintained' => true,
                'gender_responsive_strategies' => [],
                'workshop_rotation_schedule' => [
                    'intervals_minutes' => 30,
                    'stations' => [
                        ['station_id' => 1, 'name' => 'Preparation & Tool Inspection', 'equipment' => '', 'group_capacity' => 4],
                        ['station_id' => 2, 'name' => 'Fabrication & Assembly Bay', 'equipment' => '', 'group_capacity' => 4],
                        ['station_id' => 3, 'name' => 'Testing & Diagnostics', 'equipment' => '', 'group_capacity' => 4],
                        ['station_id' => 4, 'name' => 'Quality Assurance & Documentation', 'equipment' => '', 'group_capacity' => 4],
                    ],
                ],
                'workshop_safety_arrangements' => [],
                'status' => 'draft',
            ]);
        }

        return Inertia::render('Pedagogy/InclusiveDesigner', [
            'lesson' => $lesson,
            'adaptation' => $adaptation,
            'can_update' => Gate::allows('update', $lesson),
        ]);
    }

    /**
     * Update Inclusive Lesson Adaptation (Day 10).
     */
    public function inclusiveDesignerUpdate(Request $request, CompetencyLesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $validated = $request->validate([
            'structured_plan' => 'nullable|array',
            'differentiated_activities' => 'nullable|array',
            'udl_engagement' => 'nullable|array',
            'udl_representation' => 'nullable|array',
            'udl_action_expression' => 'nullable|array',
            'learner_accommodations' => 'nullable|array',
            'safety_standards_maintained' => 'required|boolean',
            'gender_responsive_strategies' => 'nullable|array',
            'workshop_rotation_schedule' => 'nullable|array',
            'workshop_safety_arrangements' => 'nullable|array',
            'status' => 'required|in:draft,submitted,approved,revisions_requested',
        ]);

        $user = $request->user();

        // Safety Non-Negotiable Enforcement:
        // Essential occupational safety standards can never be set to false.
        if ($validated['safety_standards_maintained'] !== true) {
            return redirect()->back()->withErrors([
                'safety_standards_maintained' => 'Non-Negotiable Safety Violation: Essential occupational safety standards cannot be compromised or lowered for learner accommodations.',
            ]);
        }

        DB::transaction(function () use ($validated, $lesson, $user) {
            $adaptation = InclusiveLessonAdaptation::updateOrCreate(
                ['competency_lesson_id' => $lesson->id],
                array_merge($validated, [
                    'user_id' => $user->id,
                ])
            );

            // Sync with Pedagogy Training Progress
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);
            $progress->inclusive_adaptation_id = $adaptation->id;
            $progress->recalculateForLesson($lesson);

            // Sync with Portfolio if compliant
            if ($adaptation->isDay10Compliant()) {
                $portfolio = TeacherPortfolio::firstOrCreate(['user_id' => $user->id]);
                $portfolio->items()->updateOrCreate(
                    [
                        'teacher_portfolio_id' => $portfolio->id,
                        'category' => 'lesson_documents',
                        'title' => "Day 10 Inclusive Adaptation: {$lesson->title}",
                    ],
                    [
                        'description' => '3-Tier differentiated instructional plan (Beginner, Intermediate, Advanced) with Universal Design for Learning (UDL), accommodations, and workshop rotation matrix.',
                        'url' => route('pedagogy.inclusive-designer.edit', $lesson->id),
                    ]
                );
            }

            AuditLog::record(
                'inclusive_adaptation_saved',
                InclusiveLessonAdaptation::class,
                $adaptation->id,
                ['lesson_id' => $lesson->id, 'status' => $adaptation->status]
            );
        });

        return redirect()->back()->with('success', 'Inclusive lesson adaptation saved successfully!');
    }

    /**
     * Submit Inclusive Lesson for Facilitator Assessment.
     */
    public function submitInclusiveLesson(Request $request, CompetencyLesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $adaptation = $lesson->inclusiveAdaptation;
        if (! $adaptation) {
            return redirect()->back()->withErrors(['adaptation' => 'Please save your inclusive lesson design before submitting.']);
        }

        if (! $adaptation->isDay10Compliant()) {
            return redirect()->back()->withErrors([
                'adaptation' => 'Inclusive adaptation incomplete. Ensure 3-tier differentiated activities (Beginner, Intermediate, Advanced) and UDL principles are provided.',
            ]);
        }

        $user = $request->user();

        DB::transaction(function () use ($adaptation, $lesson, $user) {
            $adaptation->update(['status' => 'submitted']);

            // Pick an assigned facilitator or first facilitator
            $facilitator = User::role('facilitator')->first() ?? $user;

            PedagogyReview::updateOrCreate(
                [
                    'competency_lesson_id' => $lesson->id,
                    'module_type' => 'day10',
                ],
                [
                    'inclusive_adaptation_id' => $adaptation->id,
                    'teacher_id' => $user->id,
                    'facilitator_id' => $facilitator->id,
                    'status' => 'under_review',
                    'submitted_at' => now(),
                ]
            );

            AuditLog::record(
                'inclusive_adaptation_submitted',
                InclusiveLessonAdaptation::class,
                $adaptation->id,
                ['teacher_id' => $user->id, 'lesson_id' => $lesson->id]
            );
        });

        return redirect()->back()->with('success', 'Adapted lesson successfully submitted for Facilitator Assessment!');
    }

    /**
     * Demonstration & Project Planner View.
     */
    public function demoProjectPlanner(Request $request, ?CompetencyLesson $lesson = null): Response
    {
        $user = $request->user();

        if (! $lesson || ! $lesson->exists) {
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);
            $lesson = $progress->competencyLesson ?: CompetencyLesson::where('user_id', $user->id)->latest()->first();
        }

        if ($lesson) {
            Gate::authorize('view', $lesson);
            $lesson->load(['tradeCategory', 'occupationalStandard']);
        }

        $myLessons = CompetencyLesson::where('user_id', $user->id)->get(['id', 'title']);

        return Inertia::render('Pedagogy/DemoProjectPlanner', [
            'lesson' => $lesson,
            'my_lessons' => $myLessons,
            'can_update' => $lesson ? Gate::allows('update', $lesson) : false,
        ]);
    }

    /**
     * Workshop Management Planner View.
     */
    public function workshopPlanner(Request $request, ?CompetencyLesson $lesson = null): Response
    {
        $user = $request->user();

        if (! $lesson || ! $lesson->exists) {
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);
            $lesson = $progress->competencyLesson ?: CompetencyLesson::where('user_id', $user->id)->latest()->first();
        }

        if ($lesson) {
            Gate::authorize('view', $lesson);
            $lesson->load(['tradeCategory', 'inclusiveAdaptation']);
        }

        $myLessons = CompetencyLesson::where('user_id', $user->id)->get(['id', 'title']);

        return Inertia::render('Pedagogy/WorkshopPlanner', [
            'lesson' => $lesson,
            'adaptation' => $lesson?->inclusiveAdaptation,
            'my_lessons' => $myLessons,
            'can_update' => $lesson ? Gate::allows('update', $lesson) : false,
        ]);
    }

    /**
     * Day 9 Practice Wizard:
     * Interactive checklist validating competency-based practical lesson components.
     */
    public function day9Wizard(Request $request): Response
    {
        $user = $request->user();
        $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);

        $activeLesson = $progress->competencyLesson ?: CompetencyLesson::where('user_id', $user->id)->latest()->first();

        if ($activeLesson) {
            $progress->competency_lesson_id = $activeLesson->id;
            $progress->recalculateForLesson($activeLesson);
        }

        $myLessons = CompetencyLesson::where('user_id', $user->id)->get(['id', 'title', 'occupational_task']);

        return Inertia::render('Pedagogy/Day9Wizard', [
            'active_lesson' => $activeLesson?->load(['tradeCategory', 'occupationalStandard']),
            'progress' => $progress,
            'my_lessons' => $myLessons,
        ]);
    }

    /**
     * Day 10 Practice Wizard:
     * Interactive checklist validating inclusive adaptations, UDL and non-negotiable safety.
     */
    public function day10Wizard(Request $request): Response
    {
        $user = $request->user();
        $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $user->id]);

        $activeLesson = $progress->competencyLesson ?: CompetencyLesson::where('user_id', $user->id)->latest()->first();

        if ($activeLesson) {
            $activeLesson->load('inclusiveAdaptation');
            $progress->recalculateForLesson($activeLesson);
        }

        $myLessons = CompetencyLesson::where('user_id', $user->id)->get(['id', 'title', 'occupational_task']);

        return Inertia::render('Pedagogy/Day10Wizard', [
            'active_lesson' => $activeLesson,
            'adaptation' => $activeLesson?->inclusiveAdaptation,
            'progress' => $progress,
            'my_lessons' => $myLessons,
        ]);
    }

    /**
     * Facilitator Portal: Review Submitted Day 9 & Day 10 Lessons.
     */
    public function facilitatorReviewIndex(Request $request): Response
    {
        $user = $request->user();
        if (! $user->isFacilitator() && ! $user->isLmsAdmin() && ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to facilitator evaluation portal.');
        }

        $pendingReviews = PedagogyReview::with([
            'competencyLesson.user:id,name,email,institution',
            'competencyLesson.tradeCategory:id,name',
            'inclusiveAdaptation',
            'teacher:id,name,email,institution',
            'facilitator:id,name',
        ])->latest()->paginate(15);

        return Inertia::render('Pedagogy/FacilitatorReviewIndex', [
            'reviews' => $pendingReviews,
        ]);
    }

    /**
     * Facilitator submits rubric grading and feedback.
     */
    public function facilitatorReviewStore(Request $request, CompetencyLesson $lesson): RedirectResponse
    {
        Gate::authorize('assess', $lesson);

        $validated = $request->validate([
            'module_type' => 'required|in:day9,day10',
            'status' => 'required|in:approved,revisions_requested',
            'rubric_scores' => 'required|array',
            'total_score' => 'required|numeric|min:0|max:100',
            'written_feedback' => 'required|string|min:10',
            'revision_notes' => 'nullable|string',
        ]);

        $facilitator = $request->user();

        DB::transaction(function () use ($validated, $lesson, $facilitator) {
            $review = PedagogyReview::updateOrCreate(
                [
                    'competency_lesson_id' => $lesson->id,
                    'module_type' => $validated['module_type'],
                ],
                [
                    'inclusive_adaptation_id' => $lesson->inclusiveAdaptation?->id,
                    'teacher_id' => $lesson->user_id,
                    'facilitator_id' => $facilitator->id,
                    'status' => $validated['status'],
                    'rubric_scores' => $validated['rubric_scores'],
                    'total_score' => $validated['total_score'],
                    'written_feedback' => $validated['written_feedback'],
                    'revision_notes' => $validated['revision_notes'] ?? null,
                    'reviewed_at' => now(),
                ]
            );

            // Update Pedagogy Progress
            $progress = PedagogyTrainingProgress::firstOrCreate(['user_id' => $lesson->user_id]);
            if ($validated['module_type'] === 'day9') {
                $progress->day9_verified_by = $facilitator->id;
                if ($validated['status'] === 'approved') {
                    $progress->day9_completed = true;
                    $progress->day9_completed_at = now();
                }
            } else {
                $progress->day10_verified_by = $facilitator->id;
                if ($validated['status'] === 'approved') {
                    $progress->day10_completed = true;
                    $progress->day10_completed_at = now();
                    if ($lesson->inclusiveAdaptation) {
                        $lesson->inclusiveAdaptation->update(['status' => 'approved']);
                    }
                } elseif ($validated['status'] === 'revisions_requested') {
                    if ($lesson->inclusiveAdaptation) {
                        $lesson->inclusiveAdaptation->update(['status' => 'revisions_requested']);
                    }
                }
            }
            $progress->facilitator_feedback = $validated['written_feedback'];
            $progress->save();

            AuditLog::record(
                'pedagogy_review_submitted',
                PedagogyReview::class,
                $review->id,
                ['teacher_id' => $lesson->user_id, 'status' => $validated['status'], 'score' => $validated['total_score']]
            );
        });

        return redirect()->back()->with('success', 'Facilitator assessment and feedback recorded successfully!');
    }
}
