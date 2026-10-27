<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, CourseSection $section): RedirectResponse
    {
        $this->authorize('update', $section->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $maxOrder = $section->lessons()->max('order_index') ?? 0;

        $section->lessons()->create([
            'course_id' => $section->course_id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'order_index' => $maxOrder + 1,
            'is_published' => true,
        ]);

        return back()->with('success', 'Lesson added to section.');
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->authorize('update', $lesson->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $lesson->update($validated);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $this->authorize('update', $lesson->course);

        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }
}
