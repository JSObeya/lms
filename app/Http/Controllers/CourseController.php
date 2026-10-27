<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\TradeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::with(['category', 'user:id,name'])
            ->withCount(['students', 'sections', 'assignments']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by mode (practice vs institutional)
        if ($request->filled('mode')) {
            if ($request->mode === 'practice') {
                $query->where('is_practice', true);
            } elseif ($request->mode === 'institutional') {
                $query->where('is_practice', false);
            }
        }

        // Filter for "My Courses" if teacher
        if ($request->boolean('my_courses') || (! $request->user()->hasRole(['super_admin', 'lms_admin']) && $request->user()->hasRole('teacher'))) {
            $query->where('user_id', $request->user()->id);
        } else {
            // Only published courses for students/public
            if ($request->user()->hasRole('student')) {
                $query->where('status', 'published');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $courses = $query->latest()->paginate(12)->withQueryString();
        $categories = TradeCategory::where('is_active', true)->orderBy('order_index')->get(['id', 'name', 'slug', 'is_tvet_trade']);

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'mode', 'my_courses']),
        ]);
    }

    public function create(): Response
    {
        $categories = TradeCategory::where('is_active', true)->orderBy('order_index')->get();

        return Inertia::render('Courses/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:courses,code',
            'trade_category_id' => 'required|exists:trade_categories,id',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:100',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'visibility' => 'required|in:public,enrolled_only,private',
            'is_practice' => 'boolean',
        ]);

        $code = ! empty($validated['code'])
            ? strtoupper($validated['code'])
            : 'TVET-'.strtoupper(Str::random(6));

        $slug = Str::slug($validated['title']).'-'.strtolower(Str::random(4));

        $course = Course::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'code' => $code,
            'trade_category_id' => $validated['trade_category_id'],
            'user_id' => $request->user()->id,
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'] ?? '4 Weeks',
            'difficulty_level' => $validated['difficulty_level'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'visibility' => $validated['visibility'],
            'status' => 'draft',
            'is_practice' => $request->boolean('is_practice', true),
        ]);

        // Automatic Day 8 Practical Wizard Evidence Tracking
        $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
        $day8->update([
            'course_id' => $course->id,
            'step_1_course_created' => true,
        ]);

        AuditLog::record('course_created', Course::class, $course->id, [
            'title' => $course->title,
            'code' => $course->code,
            'is_practice' => $course->is_practice,
        ]);

        return redirect()->route('courses.builder', $course->id)
            ->with('success', 'Course created successfully! Welcome to the Visual Course Builder.');
    }

    public function show(Course $course): Response
    {
        $this->authorize('view', $course);

        $course->load([
            'category',
            'user:id,name,institution,trade_specialization',
            'sections' => function ($q) {
                $q->orderBy('order_index')
                    ->with(['lessons', 'objectives', 'resources', 'assignments']);
            },
        ]);

        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = $course->enrollments()->where('user_id', auth()->id())->exists();
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'can_edit' => auth()->user()?->can('update', $course),
        ]);
    }

    public function builder(Course $course): Response
    {
        $this->authorize('update', $course);

        $course->load([
            'category',
            'sections' => function ($q) {
                $q->orderBy('order_index')
                    ->with([
                        'lessons' => fn ($l) => $l->orderBy('order_index'),
                        'objectives' => fn ($o) => $o->orderBy('order_index'),
                        'resources' => fn ($r) => $r->orderBy('order_index'),
                        'assignments' => fn ($a) => $a->with('rubric:id,title,total_points'),
                    ]);
            },
            'rubrics' => function ($r) {
                $r->with('criteria.levels');
            },
        ]);

        $day8Progress = Day8PracticeProgress::where('user_id', auth()->id())->first();

        return Inertia::render('Courses/Builder', [
            'course' => $course,
            'day8_progress' => $day8Progress,
        ]);
    }

    public function preview(Course $course): Response
    {
        $this->authorize('view', $course);

        $course->load([
            'category',
            'user:id,name,institution,trade_specialization',
            'sections' => function ($q) {
                $q->where('is_published', true)
                    ->orderBy('order_index')
                    ->with([
                        'lessons' => fn ($l) => $l->where('is_published', true)->orderBy('order_index'),
                        'objectives' => fn ($o) => $o->orderBy('order_index'),
                        'resources' => fn ($r) => $r->orderBy('order_index'),
                        'assignments' => fn ($a) => $a->where('status', 'published')->with('rubric.criteria.levels'),
                    ]);
            },
        ]);

        // Track Day 8 Step 8: Preview Opened
        if (auth()->check() && $course->user_id === auth()->id()) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => auth()->id()]);
            $day8->update(['step_8_preview_viewed' => true]);
        }

        return Inertia::render('Courses/Preview', [
            'course' => $course,
            'is_preview_mode' => true,
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,under_review,published,archived',
            'visibility' => 'required|in:public,enrolled_only,private',
            'is_practice' => 'boolean',
        ]);

        $course->update($validated);

        AuditLog::record('course_updated', Course::class, $course->id, [
            'status' => $course->status,
        ]);

        return back()->with('success', 'Course settings updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        AuditLog::record('course_deleted', Course::class, $course->id);

        return redirect()->route('courses.index')->with('success', 'Course archived.');
    }
}
