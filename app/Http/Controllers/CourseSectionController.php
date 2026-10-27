<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Day8PracticeProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseSectionController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $course->sections()->max('order_index') ?? 0;

        $section = $course->sections()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order_index' => $maxOrder + 1,
            'is_published' => true,
        ]);

        // Track Day 8 Step 2: Label a Topic
        if ($course->user_id === $request->user()->id) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
            $day8->update(['step_2_topic_labeled' => true]);
        }

        return back()->with('success', "Section '{$section->title}' created.");
    }

    public function update(Request $request, CourseSection $section): RedirectResponse
    {
        $this->authorize('update', $section->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section updated.');
    }

    public function reorder(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:course_sections,id',
        ]);

        DB::transaction(function () use ($validated, $course) {
            foreach ($validated['ordered_ids'] as $index => $id) {
                CourseSection::where('id', $id)
                    ->where('course_id', $course->id)
                    ->update(['order_index' => $index + 1]);
            }
        });

        return back()->with('success', 'Sections reordered successfully.');
    }

    public function destroy(CourseSection $section): RedirectResponse
    {
        $this->authorize('update', $section->course);

        $section->delete();

        return back()->with('success', 'Section deleted.');
    }
}
