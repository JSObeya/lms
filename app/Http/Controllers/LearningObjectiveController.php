<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\LearningObjective;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LearningObjectiveController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'course_section_id' => 'nullable|exists:course_sections,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'action_verb' => 'required|string|max:100',
            'condition' => 'required|string|max:500',
            'performance_standard' => 'required|string|max:500',
            'complete_text' => 'nullable|string|max:1000',
            'bloom_level' => 'required|in:remember,understand,apply,analyze,evaluate,create',
        ]);

        $completeText = ! empty($validated['complete_text'])
            ? $validated['complete_text']
            : "{$validated['condition']}, the learner will {$validated['action_verb']} {$validated['performance_standard']}.";

        $course->objectives()->create([
            'course_section_id' => $validated['course_section_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'action_verb' => $validated['action_verb'],
            'condition' => $validated['condition'],
            'performance_standard' => $validated['performance_standard'],
            'complete_text' => $completeText,
            'bloom_level' => $validated['bloom_level'],
        ]);

        // Track Day 8 Step 3: Write a Measurable Learning Objective
        if ($course->user_id === $request->user()->id) {
            $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
            $day8->update(['step_3_objective_saved' => true]);
        }

        return back()->with('success', 'Measurable learning objective saved.');
    }

    public function update(Request $request, LearningObjective $objective): RedirectResponse
    {
        $this->authorize('update', $objective->course);

        $validated = $request->validate([
            'action_verb' => 'required|string|max:100',
            'condition' => 'required|string|max:500',
            'performance_standard' => 'required|string|max:500',
            'complete_text' => 'required|string|max:1000',
            'bloom_level' => 'required|in:remember,understand,apply,analyze,evaluate,create',
        ]);

        $objective->update($validated);

        return back()->with('success', 'Learning objective updated.');
    }

    public function destroy(LearningObjective $objective): RedirectResponse
    {
        $this->authorize('update', $objective->course);

        $objective->delete();

        return back()->with('success', 'Learning objective removed.');
    }
}
