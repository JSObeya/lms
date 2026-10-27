<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AuditLog;
use App\Models\SubmissionAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('submit', $assignment);

        // Check cut-off date
        if ($assignment->isCutOffReached()) {
            return back()->withErrors(['submission' => 'The cut-off deadline for this assignment has passed. Submissions are no longer accepted.']);
        }

        $validated = $request->validate([
            'online_text' => 'nullable|string',
            'submission_url' => 'nullable|url',
            'files' => 'nullable|array|max:'.$assignment->max_files,
            'files.*' => 'file|max:'.($assignment->max_file_size_mb * 1024),
        ]);

        $isLate = $assignment->isPastDue();

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => 'submitted',
                'online_text' => $validated['online_text'] ?? null,
                'submission_url' => $validated['submission_url'] ?? null,
                'submitted_at' => now(),
                'is_late' => $isLate,
            ]
        );

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $hashName = Str::random(40).'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('private/submissions/'.$assignment->id, $hashName);

                $submission->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        AuditLog::record('assignment_submitted', AssignmentSubmission::class, $submission->id, [
            'assignment_id' => $assignment->id,
            'is_late' => $isLate,
        ]);

        return back()->with('success', 'Your assignment has been submitted successfully.');
    }

    public function downloadAttachment(SubmissionAttachment $attachment): StreamedResponse
    {
        $submission = $attachment->submission;
        $course = $submission->assignment->course;

        // Verify authorization: student owner, course owner, or admin/facilitator
        $user = auth()->user();
        if ($submission->user_id !== $user->id && $course->user_id !== $user->id && ! $user->hasRole(['super_admin', 'lms_admin', 'facilitator'])) {
            abort(403, 'Unauthorized access to student submission file.');
        }

        if (! Storage::exists($attachment->file_path)) {
            abort(404, 'Submission file not found.');
        }

        return Storage::download($attachment->file_path, $attachment->file_name);
    }
}
