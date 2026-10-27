<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\Rubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function create(Course $course): Response
    {
        $this->authorize('update', $course);

        $course->load('sections:id,course_id,title');
        $rubrics = Rubric::where('user_id', auth()->id())
            ->orWhere('course_id', $course->id)
            ->get(['id', 'title', 'total_points']);

        return Inertia::render('Assignments/Create', [
            'course' => $course,
            'sections' => $course->sections,
            'rubrics' => $rubrics,
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'course_section_id' => 'nullable|exists:course_sections,id',
            'rubric_id' => 'nullable|exists:rubrics,id',
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'max_score' => 'required|numeric|min:1|max:1000',
            'allow_submissions_from' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:allow_submissions_from',
            'cut_off_date' => 'nullable|date|after_or_equal:due_date',
            'timezone' => 'required|string',
            'submission_type' => 'required|in:file,online_text,url,file_and_text',
            'accepted_file_types' => 'required|string',
            'max_files' => 'required|integer|min:1|max:10',
            'max_file_size_mb' => 'required|integer|min:1|max:50',
            'late_submission_policy' => 'required|in:allowed,disallowed,penalty',
            'status' => 'required|in:draft,published,archived',
        ]);

        $assignment = $course->assignments()->create($validated);

        // Day 8 Automatic Tracking
        if ($course->user_id === $request->user()->id) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
            $day8->update([
                'step_5_assignment_created' => true,
                'step_6_deadline_configured' => ! empty($validated['due_date']),
            ]);
        }

        AuditLog::record('assignment_created', Assignment::class, $assignment->id, [
            'course_id' => $course->id,
            'title' => $assignment->title,
        ]);

        return redirect()->route('courses.builder', $course->id)
            ->with('success', 'Assignment configured successfully.');
    }

    public function show(Assignment $assignment): Response
    {
        $this->authorize('view', $assignment);

        $assignment->load([
            'course:id,title,code,user_id',
            'section:id,title',
            'rubric.criteria.levels',
            'submissions' => function ($q) {
                $q->with(['student:id,name,email,is_synthetic', 'attachments', 'rubricAssessment.items'])
                    ->latest();
            },
        ]);

        $user = auth()->user();
        $mySubmission = null;

        if ($user->hasRole('student')) {
            $mySubmission = $assignment->submissions()
                ->where('user_id', $user->id)
                ->with(['attachments', 'rubricAssessment.items'])
                ->latest()
                ->first();
        }

        return Inertia::render('Assignments/Show', [
            'assignment' => $assignment,
            'my_submission' => $mySubmission,
            'can_grade' => $user->can('grade', $assignment),
            'is_teacher' => $user->can('update', $assignment),
        ]);
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'max_score' => 'required|numeric|min:1|max:1000',
            'rubric_id' => 'nullable|exists:rubrics,id',
            'allow_submissions_from' => 'nullable|date',
            'due_date' => 'nullable|date',
            'cut_off_date' => 'nullable|date|after_or_equal:due_date',
            'status' => 'required|in:draft,published,archived',
        ]);

        $assignment->update($validated);

        AuditLog::record('assignment_updated', Assignment::class, $assignment->id, [
            'title' => $assignment->title,
            'max_score' => $assignment->max_score,
            'status' => $assignment->status,
        ]);

        return back()->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $this->authorize('delete', $assignment);

        $courseId = $assignment->course_id;
        $title = $assignment->title;
        $assignment->delete();

        AuditLog::record('assignment_deleted', Assignment::class, $assignment->id, [
            'title' => $title,
            'course_id' => $courseId,
        ]);

        return redirect()->route('courses.builder', $courseId)
            ->with('success', "Assignment '{$title}' moved to trash (soft deleted).");
    }

    public function restore(int $id): RedirectResponse
    {
        $assignment = Assignment::onlyTrashed()->findOrFail($id);
        $this->authorize('update', $assignment);

        $assignment->restore();

        AuditLog::record('assignment_restored', Assignment::class, $assignment->id, [
            'title' => $assignment->title,
            'course_id' => $assignment->course_id,
        ]);

        return back()->with('success', "Assignment '{$assignment->title}' restored successfully.");
    }
}
