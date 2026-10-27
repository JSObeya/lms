<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\ExportLog;
use App\Models\Grade;
use App\Models\GradeHistory;
use App\Models\Rubric;
use App\Models\RubricAssessment;
use App\Models\RubricAssessmentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradingController extends Controller
{
    /**
     * Display Course Gradebook.
     */
    public function gradebook(Request $request, Course $course): Response
    {
        $this->authorize('view', $course);

        $course->load([
            'assignments' => fn ($q) => $q->orderBy('created_at'),
            'students' => fn ($s) => $s->orderBy('name'),
        ]);

        $grades = Grade::where('course_id', $course->id)
            ->with(['student:id,name,email,username', 'assignment:id,title,max_score'])
            ->get();

        $submissions = AssignmentSubmission::whereIn('assignment_id', $course->assignments->pluck('id'))
            ->with(['student:id,name', 'attachments', 'rubricAssessment.items'])
            ->get();

        return Inertia::render('Grading/Gradebook', [
            'course' => $course,
            'assignments' => $course->assignments,
            'students' => $course->students,
            'grades' => $grades,
            'submissions' => $submissions,
            'can_grade' => auth()->user()->can('grade', $course),
        ]);
    }

    /**
     * Submit Rubric Assessment for a Submission.
     */
    public function gradeWithRubric(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $assignment = $submission->assignment;
        $this->authorize('grade', $assignment);

        $validated = $request->validate([
            'rubric_id' => 'required|exists:rubrics,id',
            'scores' => 'required|array', // [criterion_id => ['level_id' => x, 'points' => y, 'comment' => z]]
            'general_feedback' => 'nullable|string',
            'is_released' => 'boolean',
        ]);

        $rubric = Rubric::findOrFail($validated['rubric_id']);

        DB::transaction(function () use ($submission, $assignment, $rubric, $validated, $request) {
            $totalScore = 0;

            // Delete old assessment items if regrading (preserves immutable snapshot in grade history)
            $oldAssessment = $submission->rubricAssessment;
            if ($oldAssessment) {
                $oldAssessment->items()->delete();
                $oldAssessment->delete();
            }

            $assessment = RubricAssessment::create([
                'assignment_submission_id' => $submission->id,
                'rubric_id' => $rubric->id,
                'assessed_by' => $request->user()->id,
                'total_calculated_score' => 0,
                'general_feedback' => $validated['general_feedback'] ?? null,
                'is_released' => $request->boolean('is_released', true),
            ]);

            foreach ($validated['scores'] as $criterionId => $scoreData) {
                $points = (float) $scoreData['points'];
                $totalScore += $points;

                RubricAssessmentItem::create([
                    'rubric_assessment_id' => $assessment->id,
                    'rubric_criterion_id' => $criterionId,
                    'rubric_level_id' => $scoreData['level_id'] ?? null,
                    'awarded_points' => $points,
                    'assessor_comment' => $scoreData['comment'] ?? null,
                ]);
            }

            $assessment->update(['total_calculated_score' => $totalScore]);

            // Update Submission
            $submission->update([
                'status' => 'graded',
                'final_score' => $totalScore,
                'grade_released' => $request->boolean('is_released', true),
                'feedback' => $validated['general_feedback'] ?? null,
                'graded_by' => $request->user()->id,
                'graded_at' => now(),
            ]);

            // Update or Create Grade Record
            $maxScore = $assignment->max_score > 0 ? $assignment->max_score : 100;
            $percentage = round(($totalScore / $maxScore) * 100, 2);

            $grade = Grade::updateOrCreate(
                [
                    'course_id' => $assignment->course_id,
                    'assignment_id' => $assignment->id,
                    'user_id' => $submission->user_id,
                ],
                [
                    'max_score' => $maxScore,
                    'obtained_score' => $totalScore,
                    'percentage' => $percentage,
                    'is_released' => $request->boolean('is_released', true),
                    'feedback' => $validated['general_feedback'] ?? null,
                    'graded_by' => $request->user()->id,
                    'graded_at' => now(),
                ]
            );

            // Record Grade History
            GradeHistory::create([
                'grade_id' => $grade->id,
                'previous_score' => null,
                'new_score' => $totalScore,
                'changed_by' => $request->user()->id,
                'reason' => 'Rubric-based assessment grading',
            ]);
        });

        // Day 8 Automatic Tracking: Step 9 Sample Graded
        if ($assignment->course->user_id === $request->user()->id) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
            $day8->update(['step_9_sample_graded' => true]);
        }

        AuditLog::record('submission_graded', AssignmentSubmission::class, $submission->id, [
            'assignment_id' => $assignment->id,
            'student_id' => $submission->user_id,
        ]);

        return back()->with('success', 'Submission evaluated and grade calculated with rubric.');
    }

    /**
     * Toggle Grade Release.
     */
    public function releaseGrade(Grade $grade): RedirectResponse
    {
        $this->authorize('grade', $grade->course);

        $grade->update(['is_released' => ! $grade->is_released]);

        return back()->with('success', $grade->is_released ? 'Grade released to student.' : 'Grade withheld.');
    }

    /**
     * Export Course Gradebook to CSV with Formula Injection Protection.
     */
    public function exportCsv(Course $course): StreamedResponse
    {
        $this->authorize('export', $course);

        $fileName = 'gradebook_'.$course->code.'_'.date('Y-m-d_His').'.csv';

        // Escaping formula characters to prevent CSV injection attacks
        $sanitize = function ($value) {
            $value = (string) $value;
            if (in_array(substr($value, 0, 1), ['=', '+', '-', '@', "\t", "\r"])) {
                return "'".$value;
            }

            return $value;
        };

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($course, $sanitize) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Course Info Header
            fputcsv($handle, ['Greg ICT Hub LMS — Course Gradebook Report']);
            fputcsv($handle, ['Course Title:', $sanitize($course->title)]);
            fputcsv($handle, ['Course Code:', $sanitize($course->code)]);
            fputcsv($handle, ['Generated At:', now()->toDateTimeString()]);
            fputcsv($handle, ['Instructor:', $sanitize($course->user->name ?? 'N/A')]);
            fputcsv($handle, []); // Blank line

            // Table Headers
            fputcsv($handle, [
                'Student ID',
                'Student Name',
                'Email',
                'Assignment',
                'Max Score',
                'Obtained Score',
                'Percentage (%)',
                'Status',
                'Graded At',
                'Feedback',
            ]);

            $grades = Grade::where('course_id', $course->id)
                ->with(['student', 'assignment'])
                ->get();

            foreach ($grades as $grade) {
                fputcsv($handle, [
                    $sanitize($grade->student->id ?? 'N/A'),
                    $sanitize($grade->student->name ?? 'Unknown Student'),
                    $sanitize($grade->student->email ?? 'N/A'),
                    $sanitize($grade->assignment->title ?? 'N/A'),
                    $grade->max_score,
                    $grade->obtained_score,
                    $grade->percentage.'%',
                    $grade->is_released ? 'Released' : 'Draft',
                    $grade->graded_at ? $grade->graded_at->toDateTimeString() : 'N/A',
                    $sanitize($grade->feedback ?? ''),
                ]);
            }

            fclose($handle);
        };

        // Track Day 8 Step 10: Export Grade Report
        if ($course->user_id === auth()->id()) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => auth()->id()]);
            $day8->update([
                'step_10_report_exported' => true,
                'completed_at' => $day8->completedStepsCount() >= 9 ? now() : null,
            ]);
        }

        ExportLog::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'export_type' => 'gradebook_csv',
            'file_name' => $fileName,
            'records_count' => Grade::where('course_id', $course->id)->count(),
            'created_at' => now(),
        ]);

        return response()->stream($callback, 200, $headers);
    }
}
