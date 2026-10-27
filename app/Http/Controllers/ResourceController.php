<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'course_section_id' => 'nullable|exists:course_sections,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_type' => 'required|in:pdf,docx,pptx,xlsx,image,url,video_embed,worksheet',
            'external_url' => 'nullable|required_if:resource_type,url,video_embed|url|max:1000',
            'file' => 'nullable|required_if:resource_type,pdf,docx,pptx,xlsx,image,worksheet|file|max:20480|mimes:pdf,docx,doc,pptx,ppt,xlsx,xls,png,jpg,jpeg,webp,zip',
        ]);

        $filePath = null;
        $fileName = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $fileName = $uploaded->getClientOriginalName();
            $mimeType = $uploaded->getMimeType();
            $fileSize = $uploaded->getSize();

            // Secure private storage with randomized unique hash
            $hashName = Str::random(40).'.'.$uploaded->getClientOriginalExtension();
            $filePath = $uploaded->storeAs('private/resources/'.$course->id, $hashName);
        }

        $course->resources()->create([
            'course_section_id' => $validated['course_section_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'resource_type' => $validated['resource_type'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'external_url' => $validated['external_url'] ?? null,
            'is_downloadable' => true,
        ]);

        // Track Day 8 Step 4: Add a PDF or educational URL
        if ($course->user_id === $request->user()->id) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
            $day8->update(['step_4_resource_attached' => true]);
        }

        AuditLog::record('resource_uploaded', Resource::class, null, [
            'course_id' => $course->id,
            'title' => $validated['title'],
            'type' => $validated['resource_type'],
        ]);

        return back()->with('success', 'Resource attached successfully.');
    }

    public function download(Resource $resource): StreamedResponse
    {
        $course = $resource->course;
        $user = auth()->user();

        // Only course owner, enrolled students, or administrators/facilitators can download course files
        $isOwner = $course->user_id === $user->id;
        $isEnrolled = $course->enrollments()->where('user_id', $user->id)->exists();
        $isStaff = $user->hasRole(['super_admin', 'lms_admin', 'facilitator']);

        if (! $isOwner && ! $isEnrolled && ! $isStaff) {
            abort(403, 'Unauthorized access to download course materials. You must be enrolled in this course.');
        }

        if (! $resource->file_path || ! Storage::exists($resource->file_path)) {
            abort(404, 'The requested resource file could not be found.');
        }

        AuditLog::record('resource_downloaded', Resource::class, $resource->id, [
            'file_name' => $resource->file_name,
        ]);

        return Storage::download(
            $resource->file_path,
            $resource->file_name ?? 'learning_resource.'.pathinfo($resource->file_path, PATHINFO_EXTENSION)
        );
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource->course);

        if ($resource->file_path && Storage::exists($resource->file_path)) {
            Storage::delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }
}
