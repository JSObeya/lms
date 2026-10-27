<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Day8PracticeProgress;
use App\Models\Rubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RubricController extends Controller
{
    public function index(Request $request): Response
    {
        $rubrics = Rubric::where('user_id', $request->user()->id)
            ->with(['course:id,title,code', 'criteria.levels'])
            ->withCount(['criteria', 'assignments'])
            ->latest()
            ->paginate(10);

        $courses = Course::where('user_id', $request->user()->id)->get(['id', 'title', 'code']);

        return Inertia::render('Rubrics/Index', [
            'rubrics' => $rubrics,
            'courses' => $courses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'required|array|min:1',
            'criteria.*.title' => 'required|string|max:255',
            'criteria.*.description' => 'nullable|string',
            'criteria.*.max_points' => 'required|numeric|min:0',
            'criteria.*.levels' => 'required|array|min:1',
            'criteria.*.levels.*.title' => 'required|string|max:100',
            'criteria.*.levels.*.descriptor' => 'required|string',
            'criteria.*.levels.*.points' => 'required|numeric|min:0',
        ]);

        $rubric = DB::transaction(function () use ($validated, $request) {
            $totalPoints = 0;
            foreach ($validated['criteria'] as $crit) {
                $totalPoints += (float) $crit['max_points'];
            }

            $rubric = Rubric::create([
                'course_id' => $validated['course_id'] ?? null,
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'total_points' => $totalPoints,
                'is_active' => true,
                'version' => 1,
            ]);

            foreach ($validated['criteria'] as $cIndex => $critData) {
                $criterion = $rubric->criteria()->create([
                    'title' => $critData['title'],
                    'description' => $critData['description'] ?? null,
                    'max_points' => $critData['max_points'],
                    'order_index' => $cIndex + 1,
                ]);

                foreach ($critData['levels'] as $lIndex => $levelData) {
                    $criterion->levels()->create([
                        'title' => $levelData['title'],
                        'descriptor' => $levelData['descriptor'],
                        'points' => $levelData['points'],
                        'order_index' => $lIndex + 1,
                    ]);
                }
            }

            return $rubric;
        });

        // Track Day 8 Step 7: Define a Grading Rubric
        $day8 = Day8PracticeProgress::firstOrCreate(['user_id' => $request->user()->id]);
        $day8->update(['step_7_rubric_activated' => true]);

        AuditLog::record('rubric_created', Rubric::class, $rubric->id, [
            'title' => $rubric->title,
            'total_points' => $rubric->total_points,
        ]);

        return back()->with('success', "Rubric '{$rubric->title}' created with {$rubric->total_points} total points.");
    }

    public function show(Rubric $rubric): Response
    {
        $rubric->load(['course:id,title,code', 'criteria.levels']);

        return Inertia::render('Rubrics/Show', [
            'rubric' => $rubric,
            'can_edit' => auth()->user()?->can('update', $rubric),
        ]);
    }

    public function duplicate(Rubric $rubric): RedirectResponse
    {
        $newRubric = DB::transaction(function () use ($rubric) {
            $clone = $rubric->replicate();
            $clone->title = $rubric->title.' (Copy)';
            $clone->user_id = auth()->id();
            $clone->save();

            foreach ($rubric->criteria()->with('levels')->get() as $criterion) {
                $newCrit = $criterion->replicate();
                $newCrit->rubric_id = $clone->id;
                $newCrit->save();

                foreach ($criterion->levels as $level) {
                    $newLevel = $level->replicate();
                    $newLevel->rubric_criterion_id = $newCrit->id;
                    $newLevel->save();
                }
            }

            return $clone;
        });

        return back()->with('success', "Rubric duplicated as '{$newRubric->title}'.");
    }

    public function destroy(Rubric $rubric): RedirectResponse
    {
        $this->authorize('delete', $rubric);

        if ($rubric->assessments()->exists()) {
            $rubric->update(['is_active' => false]);

            AuditLog::record('rubric_deactivated', Rubric::class, $rubric->id, [
                'title' => $rubric->title,
                'reason' => 'associated_assessments_exist',
            ]);

            return back()->with('success', 'Rubric is associated with historical assessments. Deactivated instead of deleted.');
        }

        $title = $rubric->title;
        $rubric->delete();

        AuditLog::record('rubric_deleted', Rubric::class, $rubric->id, [
            'title' => $title,
        ]);

        return back()->with('success', "Rubric '{$title}' moved to trash (soft deleted).");
    }

    public function restore(int $id): RedirectResponse
    {
        $rubric = Rubric::onlyTrashed()->findOrFail($id);
        $this->authorize('update', $rubric);

        $rubric->restore();

        AuditLog::record('rubric_restored', Rubric::class, $rubric->id, [
            'title' => $rubric->title,
        ]);

        return back()->with('success', "Rubric '{$rubric->title}' restored successfully.");
    }
}
