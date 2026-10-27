<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Day8PracticeProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class Day8WizardController extends Controller
{
    /**
     * Show Day 8 Practical Wizard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $progress = Day8PracticeProgress::firstOrCreate(['user_id' => $user->id]);

        $latestCourse = Course::where('user_id', $user->id)->latest()->first();
        if ($latestCourse && ! $progress->course_id) {
            $progress->update(['course_id' => $latestCourse->id]);
        }

        // Automatic verification against actual database evidence
        if ($progress->course_id) {
            $course = Course::find($progress->course_id);
            if ($course) {
                $hasSection = $course->sections()->exists();
                $hasObjective = $course->objectives()->exists();
                $hasResource = $course->resources()->exists();
                $assignment = $course->assignments()->first();
                $hasAssignment = (bool) $assignment;
                $hasDeadline = $assignment && ! empty($assignment->due_date);
                $hasRubric = $course->rubrics()->where('is_active', true)->exists();
                $hasGraded = AssignmentSubmission::whereHas('assignment', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->where('status', 'graded')->exists();

                $progress->update([
                    'step_1_course_created' => true,
                    'step_2_topic_labeled' => $hasSection,
                    'step_3_objective_saved' => $hasObjective,
                    'step_4_resource_attached' => $hasResource,
                    'step_5_assignment_created' => $hasAssignment,
                    'step_6_deadline_configured' => $hasDeadline,
                    'step_7_rubric_activated' => $hasRubric,
                    'step_9_sample_graded' => $hasGraded,
                ]);
            }
        }

        $practiceStudents = User::where('sandbox_owner_id', $user->id)->get(['id', 'name', 'email', 'is_synthetic']);

        return Inertia::render('Day8/Wizard', [
            'progress' => $progress->fresh(['course', 'verifier:id,name']),
            'practice_students' => $practiceStudents,
            'course' => $progress->course_id ? Course::with(['sections.lessons', 'assignments.rubric'])->find($progress->course_id) : null,
        ]);
    }

    /**
     * Generate Isolated Synthetic Practice Learner with Sample Submission.
     */
    public function generatePracticeLearner(Request $request): RedirectResponse
    {
        $user = $request->user();
        $progress = Day8PracticeProgress::firstOrCreate(['user_id' => $user->id]);

        if (! $progress->course_id) {
            return back()->withErrors(['wizard' => 'Please create a practice course first before generating a test learner.']);
        }

        $course = Course::findOrFail($progress->course_id);

        // 1. Create or retrieve synthetic learner scoped exclusively to this teacher
        $randomNum = rand(100, 999);
        $names = [
            'Dooshima Iorwuese',
            'Terkimbi Agbese',
            'Aondongu Tyav',
            'Ngoundu Chia',
            'Kator Awunah',
            'Sewuese Dzurgba',
        ];
        $selectedName = $names[array_rand($names)];

        $practiceUser = User::create([
            'name' => "[Practice Learner] {$selectedName}",
            'first_name' => explode(' ', $selectedName)[0],
            'last_name' => explode(' ', $selectedName)[1] ?? '',
            'username' => 'practice_'.Str::slug($selectedName).'_'.$randomNum,
            'email' => 'practice_'.Str::random(8).'@sandbox.local',
            'institution' => 'TVET Practice Sandbox',
            'state' => 'Benue',
            'trade_specialization' => $course->category->name ?? 'TVET Trade',
            'account_status' => 'active',
            'is_synthetic' => true,
            'sandbox_owner_id' => $user->id,
            'password' => Hash::make(Str::random(16)), // Unused secure random password
            'email_verified_at' => now(),
        ]);
        $practiceUser->syncRoles(['student']);

        // 2. Enroll practice learner into this teacher's course
        CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $practiceUser->id],
            ['role' => 'student', 'status' => 'active', 'progress_percentage' => 50]
        );

        // 3. If course has an assignment, create a practice submission ready for grading
        $assignment = $course->assignments()->first();
        if ($assignment) {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $practiceUser->id,
                'attempt_number' => 1,
                'status' => 'submitted',
                'online_text' => "PRACTICAL INSPECTION REPORT (Synthetic Submission)\nLearner: {$selectedName}\nTrade: {$course->title}\n\n1. Technical Identification:\nI physically checked the components and matched each against the schematic.\n- Inverter rating: 2.5kVA\n- Controller MPPT: 40A\n- All connections torqued to manufacturer specifications.\n\n2. Safety Observances:\n- Work boots and safety goggles donned.\n- DC circuit breaker verified open before terminal connections.",
                'submitted_at' => now()->subMinutes(15),
                'is_late' => false,
                'grade_released' => false,
            ]);
        }

        return back()->with('success', "Synthetic practice learner '{$practiceUser->name}' generated with a sample submission ready for rubric grading!");
    }

    /**
     * Facilitator Verification of Teacher Practice Completion.
     */
    public function facilitatorVerify(Request $request, Day8PracticeProgress $progress): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasRole(['facilitator', 'lms_admin', 'super_admin'])) {
            abort(403, 'Unauthorized. Only facilitators or administrators can verify trainee exercises.');
        }

        $validated = $request->validate([
            'feedback' => 'nullable|string|max:1000',
            'is_approved' => 'required|boolean',
        ]);

        $progress->update([
            'is_verified_by_facilitator' => $validated['is_approved'],
            'verified_by' => $user->id,
            'facilitator_feedback' => $validated['feedback'] ?? null,
            'completed_at' => $validated['is_approved'] ? now() : null,
        ]);

        return back()->with('success', 'Trainee Day 8 practice review saved.');
    }
}
