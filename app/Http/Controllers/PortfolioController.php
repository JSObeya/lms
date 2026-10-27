<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\TeacherPortfolio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $portfolio = TeacherPortfolio::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => "{$user->name}'s Digital Teaching Portfolio"]
        );

        $portfolio->load('items');
        $facilitators = User::role('facilitator')->get(['id', 'name', 'email']);

        // Calculate completeness
        $categories = [
            'lesson_documents',
            'digital_resources',
            'objectives',
            'assignments',
            'rubrics',
            'sample_grading',
            'feedback_records',
            'microteaching',
            'school_plans',
        ];
        $covered = $portfolio->items->pluck('category')->unique()->count();
        $completeness = (int) round(($covered / count($categories)) * 100);
        $portfolio->update(['completion_percentage' => $completeness]);

        return Inertia::render('Portfolio/Index', [
            'portfolio' => $portfolio,
            'categories' => $categories,
            'facilitators' => $facilitators,
        ]);
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $portfolio = TeacherPortfolio::firstOrCreate(['user_id' => $request->user()->id]);

        $validated = $request->validate([
            'category' => 'required|in:lesson_documents,digital_resources,objectives,assignments,rubrics,sample_grading,feedback_records,microteaching,school_plans',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $portfolio->items()->create($validated);

        AuditLog::record('portfolio_item_added', TeacherPortfolio::class, $portfolio->id, [
            'title' => $validated['title'],
            'category' => $validated['category'],
        ]);

        return back()->with('success', 'Artifact added to your digital teaching portfolio.');
    }

    public function share(Request $request): RedirectResponse
    {
        $portfolio = TeacherPortfolio::firstOrCreate(['user_id' => $request->user()->id]);

        $validated = $request->validate([
            'shared_with_facilitator_id' => 'nullable|exists:users,id',
            'is_shared' => 'required|boolean',
        ]);

        $portfolio->update($validated);

        return back()->with('success', 'Portfolio sharing settings updated.');
    }
}
