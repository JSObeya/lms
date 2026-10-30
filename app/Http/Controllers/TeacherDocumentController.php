<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\TeacherDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherDocumentController extends Controller
{
    /**
     * Categories preset for organizing teacher documents.
     */
    public const DEFAULT_CATEGORIES = [
        'Curriculum & Standards',
        'Lesson Planning',
        'Policies & Guidelines',
        'Teaching Aids & Worksheets',
        'Assessment & Rubrics',
        'Workshop & Safety',
        'General',
    ];

    /**
     * Display the document repository for teachers and administrators.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $userRole = $user->getRoleNames()->first() ?? 'student';
        $isAdmin = $user->hasRole(['super_admin', 'lms_admin']);

        $search = $request->query('search');
        $category = $request->query('category');
        $fileType = $request->query('file_type');

        $query = TeacherDocument::query()
            ->with('uploader:id,name,email')
            ->latest();

        // Non-admins only see published documents for their audience
        if (! $isAdmin) {
            $query->published()->forAudience($userRole);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if (! empty($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        if (! empty($fileType) && $fileType !== 'all') {
            if ($fileType === 'pdf') {
                $query->where('file_extension', 'pdf');
            } elseif ($fileType === 'doc') {
                $query->whereIn('file_extension', ['doc', 'docx']);
            } else {
                $query->where('file_extension', $fileType);
            }
        }

        $documents = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $baseQuery = $isAdmin ? TeacherDocument::query() : TeacherDocument::query()->published()->forAudience($userRole);
        $totalDocuments = (clone $baseQuery)->count();
        $pdfCount = (clone $baseQuery)->where('file_extension', 'pdf')->count();
        $totalDownloads = (clone $baseQuery)->sum('download_count');

        // Existing categories in DB merged with defaults
        $dbCategories = TeacherDocument::distinct()->pluck('category')->filter()->values()->all();
        $allCategories = array_values(array_unique(array_merge(self::DEFAULT_CATEGORIES, $dbCategories)));

        return Inertia::render('Teacher/Documents/Index', [
            'documents' => $documents,
            'categories' => $allCategories,
            'filters' => [
                'search' => $search ?? '',
                'category' => $category ?? 'all',
                'file_type' => $fileType ?? 'all',
            ],
            'stats' => [
                'total_documents' => $totalDocuments,
                'pdf_count' => $pdfCount,
                'total_downloads' => (int) $totalDownloads,
            ],
            'canManage' => $isAdmin,
        ]);
    }

    /**
     * Store a newly uploaded document by admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Only administrators can upload documents for teachers.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'target_audience' => 'required|in:teachers,all,facilitators',
            'document_file' => 'required|file|max:30720|mimes:pdf,docx,doc,xlsx,xls,pptx,ppt,txt,zip',
            'is_published' => 'nullable|boolean',
        ]);

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $fileSize = $file->getSize();

        // Store file with unique hash in private teacher_documents folder
        $storedName = Str::random(40).'.'.$extension;
        $filePath = $file->storeAs('teacher_documents', $storedName, 'local');

        $document = TeacherDocument::create([
            'uploaded_by' => $user->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'target_audience' => $validated['target_audience'],
            'file_path' => $filePath,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'file_extension' => $extension,
            'download_count' => 0,
            'is_published' => $request->boolean('is_published', true),
        ]);

        AuditLog::record('teacher_document_uploaded', TeacherDocument::class, $document->id, [
            'title' => $document->title,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'category' => $document->category,
            'target_audience' => $document->target_audience,
        ]);

        return back()->with('success', "Document '{$document->title}' uploaded successfully for teachers.");
    }

    /**
     * Update an existing teacher document.
     */
    public function update(Request $request, TeacherDocument $teacherDocument): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Only administrators can update teacher documents.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'target_audience' => 'required|in:teachers,all,facilitators',
            'is_published' => 'required|boolean',
            'document_file' => 'nullable|file|max:30720|mimes:pdf,docx,doc,xlsx,xls,pptx,ppt,txt,zip',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'target_audience' => $validated['target_audience'],
            'is_published' => (bool) $validated['is_published'],
        ];

        // Replace file if new one was uploaded
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            $fileSize = $file->getSize();

            if ($teacherDocument->file_path && Storage::disk('local')->exists($teacherDocument->file_path)) {
                Storage::disk('local')->delete($teacherDocument->file_path);
            }

            $storedName = Str::random(40).'.'.$extension;
            $filePath = $file->storeAs('teacher_documents', $storedName, 'local');

            $updateData['file_path'] = $filePath;
            $updateData['file_name'] = $originalName;
            $updateData['file_size'] = $fileSize;
            $updateData['mime_type'] = $mimeType;
            $updateData['file_extension'] = $extension;
        }

        $teacherDocument->update($updateData);

        AuditLog::record('teacher_document_updated', TeacherDocument::class, $teacherDocument->id, [
            'title' => $teacherDocument->title,
            'category' => $teacherDocument->category,
        ]);

        return back()->with('success', "Document '{$teacherDocument->title}' updated successfully.");
    }

    /**
     * Download the document to teacher's individual system.
     */
    public function download(Request $request, TeacherDocument $teacherDocument): StreamedResponse
    {
        $user = $request->user();

        // Check audience permission
        if (! $user->hasRole(['super_admin', 'lms_admin'])) {
            if (! $teacherDocument->is_published) {
                abort(404, 'This document is currently unavailable.');
            }
        }

        if (! Storage::disk('local')->exists($teacherDocument->file_path)) {
            abort(404, 'The requested document file could not be found in storage.');
        }

        // Increment download counter
        $teacherDocument->increment('download_count');

        AuditLog::record('teacher_document_downloaded', TeacherDocument::class, $teacherDocument->id, [
            'title' => $teacherDocument->title,
            'file_name' => $teacherDocument->file_name,
            'download_count' => $teacherDocument->download_count,
        ]);

        return Storage::disk('local')->download(
            $teacherDocument->file_path,
            $teacherDocument->file_name
        );
    }

    /**
     * Preview PDF document inline in the browser.
     */
    public function preview(Request $request, TeacherDocument $teacherDocument): SymfonyResponse
    {
        $user = $request->user();

        if (! $user->hasRole(['super_admin', 'lms_admin'])) {
            if (! $teacherDocument->is_published) {
                abort(404, 'This document is currently unavailable.');
            }
        }

        if (! Storage::disk('local')->exists($teacherDocument->file_path)) {
            abort(404, 'The requested document file could not be found in storage.');
        }

        return Storage::disk('local')->response(
            $teacherDocument->file_path,
            $teacherDocument->file_name,
            [
                'Content-Type' => $teacherDocument->mime_type ?? 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$teacherDocument->file_name.'"',
            ]
        );
    }

    /**
     * Remove the document from the repository.
     */
    public function destroy(Request $request, TeacherDocument $teacherDocument): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Only administrators can remove teacher documents.');
        }

        $title = $teacherDocument->title;

        if ($teacherDocument->file_path && Storage::disk('local')->exists($teacherDocument->file_path)) {
            Storage::disk('local')->delete($teacherDocument->file_path);
        }

        $teacherDocument->delete();

        AuditLog::record('teacher_document_deleted', TeacherDocument::class, null, [
            'title' => $title,
        ]);

        return back()->with('success', "Document '{$title}' removed from repository.");
    }
}
