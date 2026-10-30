<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\TradeCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherQuizController extends Controller
{
    /**
     * List available MCQ quizzes for teachers.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isEvaluator = $user->hasRole(['super_admin', 'lms_admin', 'facilitator']);

        $quizzesQuery = $isEvaluator ? Quiz::query() : Quiz::active();

        $quizzes = $quizzesQuery
            ->withCount('questions')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($quiz) use ($user) {
                $userAttempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->orderByDesc('completed_at')
                    ->get();

                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'slug' => $quiz->slug,
                    'description' => $quiz->description,
                    'instructions' => $quiz->instructions,
                    'category' => $quiz->category,
                    'time_limit_minutes' => $quiz->time_limit_minutes,
                    'passing_percentage' => $quiz->passing_percentage,
                    'total_questions' => $quiz->questions_count,
                    'is_active' => (bool) $quiz->is_active,
                    'user_attempts_count' => $userAttempts->count(),
                    'latest_attempt' => $userAttempts->first(),
                    'best_score' => $userAttempts->max('percentage') ?? null,
                    'has_passed' => $userAttempts->where('passed', true)->isNotEmpty(),
                ];
            });

        // Quick overview metrics for evaluators
        $evaluatorStats = null;
        if ($isEvaluator) {
            $totalAttempts = QuizAttempt::count();
            $passedAttempts = QuizAttempt::where('passed', true)->count();
            $avgPercentage = QuizAttempt::avg('percentage') ?? 0;

            $evaluatorStats = [
                'total_submissions' => $totalAttempts,
                'unique_teachers' => QuizAttempt::distinct('user_id')->count('user_id'),
                'avg_percentage' => round($avgPercentage, 1),
                'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0,
            ];
        }

        return Inertia::render('Teacher/Quizzes/Index', [
            'quizzes' => $quizzes,
            'isEvaluator' => $isEvaluator,
            'evaluatorStats' => $evaluatorStats,
            'availableCategories' => $this->getAvailableCategories(),
        ]);
    }

    /**
     * Create a new MCQ Quiz / Examination.
     */
    public function storeQuiz(Request $request): RedirectResponse
    {
        $this->authorizeEvaluator();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:5', 'max:300'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug ?: 'quiz-'.time();
        $counter = 1;
        while (Quiz::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $quiz = Quiz::create([
            'title' => trim($validated['title']),
            'slug' => $slug,
            'category' => trim($validated['category']),
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? 60,
            'passing_percentage' => $validated['passing_percentage'] ?? 60,
            'total_questions' => 0,
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
            'instructions' => ! empty($validated['instructions']) ? trim($validated['instructions']) : null,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_created',
            'entity_type' => Quiz::class,
            'entity_id' => $quiz->id,
            'description' => "Created new assessment exam: {$quiz->title} in category {$quiz->category}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.quizzes.questions.index', $quiz->id)
            ->with('success', "Exam '{$quiz->title}' created successfully! You can now add questions or upload CSV.");
    }

    /**
     * Update an existing Quiz / Examination.
     */
    public function updateQuiz(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeEvaluator();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:5', 'max:300'],
            'passing_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $quiz->update([
            'title' => trim($validated['title']),
            'category' => trim($validated['category']),
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? $quiz->time_limit_minutes,
            'passing_percentage' => $validated['passing_percentage'] ?? $quiz->passing_percentage,
            'description' => ! empty($validated['description']) ? trim($validated['description']) : null,
            'instructions' => ! empty($validated['instructions']) ? trim($validated['instructions']) : null,
            'is_active' => $request->boolean('is_active', $quiz->is_active),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_updated',
            'entity_type' => Quiz::class,
            'entity_id' => $quiz->id,
            'description' => "Updated assessment exam settings for {$quiz->title}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Exam '{$quiz->title}' settings updated successfully.");
    }

    /**
     * Delete an existing Quiz / Examination.
     */
    public function destroyQuiz(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeEvaluator();

        $title = $quiz->title;
        $quizId = $quiz->id;

        // Cascade delete questions and attempts
        $quiz->questions()->delete();
        $quiz->attempts()->delete();
        $quiz->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_deleted',
            'entity_type' => Quiz::class,
            'entity_id' => $quizId,
            'description' => "Deleted assessment exam {$title} and its associated questions/attempts.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('quizzes.index')
            ->with('success', "Exam '{$title}' was successfully deleted.");
    }

    /**
     * Take an interactive MCQ quiz.
     */
    public function show(Quiz $quiz): Response
    {
        if (! $quiz->is_active) {
            abort(404, 'Assessment is currently inactive.');
        }

        // Fetch questions without revealing correct answer to the client browser
        $questions = $quiz->questions()
            ->select('id', 'quiz_id', 'question_number', 'category', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'points')
            ->orderBy('question_number')
            ->get();

        $previousAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['id', 'score', 'percentage', 'passed', 'grade', 'completed_at']);

        return Inertia::render('Teacher/Quizzes/Take', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'slug' => $quiz->slug,
                'description' => $quiz->description,
                'instructions' => $quiz->instructions,
                'category' => $quiz->category,
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'passing_percentage' => $quiz->passing_percentage,
                'total_questions' => $questions->count(),
            ],
            'questions' => $questions,
            'previousAttempts' => $previousAttempts,
        ]);
    }

    /**
     * Submit quiz answers, evaluate automatically, and store results.
     */
    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'started_at' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $submittedAnswers = $validated['answers']; // key = question_id, value = 'a'|'b'|'c'|'d'
        $startedAt = $validated['started_at'] ? Carbon::parse($validated['started_at']) : now()->subMinutes(30);
        $completedAt = now();

        $questions = $quiz->questions()->orderBy('question_number')->get();
        $totalQuestions = $questions->count();

        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $totalScore = 0;
        $maxScore = 0;

        $detailedAnswers = [];
        $categoryBreakdown = [];

        foreach ($questions as $q) {
            $maxScore += $q->points;
            $qCategory = $q->category ?? 'General';

            if (! isset($categoryBreakdown[$qCategory])) {
                $categoryBreakdown[$qCategory] = [
                    'category' => $qCategory,
                    'total' => 0,
                    'correct' => 0,
                    'score' => 0,
                    'max_score' => 0,
                ];
            }
            $categoryBreakdown[$qCategory]['total']++;
            $categoryBreakdown[$qCategory]['max_score'] += $q->points;

            $selectedOption = isset($submittedAnswers[$q->id]) ? strtolower(trim((string) $submittedAnswers[$q->id])) : null;
            $correctOption = strtolower(trim((string) $q->correct_option));

            $isCorrect = false;
            if ($selectedOption === null || $selectedOption === '') {
                $unansweredCount++;
            } elseif ($selectedOption === $correctOption) {
                $isCorrect = true;
                $correctCount++;
                $totalScore += $q->points;
                $categoryBreakdown[$qCategory]['correct']++;
                $categoryBreakdown[$qCategory]['score'] += $q->points;
            } else {
                $incorrectCount++;
            }

            $optionField = "option_{$selectedOption}";
            $correctField = "option_{$correctOption}";

            $detailedAnswers[] = [
                'question_id' => $q->id,
                'question_number' => $q->question_number,
                'category' => $qCategory,
                'question_text' => $q->question_text,
                'selected_option' => $selectedOption,
                'selected_text' => $selectedOption && isset($q->{$optionField}) ? $q->{$optionField} : 'Not Answered',
                'correct_option' => $correctOption,
                'correct_text' => $q->{$correctField} ?? '',
                'is_correct' => $isCorrect,
                'points_awarded' => $isCorrect ? $q->points : 0,
                'points_possible' => $q->points,
                'explanation' => $q->explanation,
            ];
        }

        // Calculate category percentages
        foreach ($categoryBreakdown as $catKey => $catData) {
            $categoryBreakdown[$catKey]['percentage'] = $catData['max_score'] > 0
                ? round(($catData['score'] / $catData['max_score']) * 100, 1)
                : 0;
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0.00;
        $passed = $percentage >= $quiz->passing_percentage;

        // Assign official grade
        $grade = 'Needs Improvement';
        if ($percentage >= 80) {
            $grade = 'Distinction';
        } elseif ($percentage >= 70) {
            $grade = 'Merit';
        } elseif ($percentage >= 60) {
            $grade = 'Credit / Pass';
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'status' => 'completed',
            'score' => $totalScore,
            'max_score' => $maxScore,
            'total_questions' => $totalQuestions,
            'correct_answers_count' => $correctCount,
            'incorrect_answers_count' => $incorrectCount,
            'unanswered_count' => $unansweredCount,
            'percentage' => $percentage,
            'passed' => $passed,
            'grade' => $grade,
            'answers' => $detailedAnswers,
            'category_scores' => array_values($categoryBreakdown),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'teacher_quiz_submitted',
            'entity_type' => QuizAttempt::class,
            'entity_id' => $attempt->id,
            'description' => "Teacher {$user->name} completed {$quiz->title} with score {$totalScore}/{$maxScore} ({$percentage}%) - {$grade}.",
            'metadata' => [
                'quiz_id' => $quiz->id,
                'score' => $totalScore,
                'percentage' => $percentage,
                'passed' => $passed,
                'grade' => $grade,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('quizzes.attempt', $attempt->id)
            ->with('success', "Assessment submitted successfully! You scored {$totalScore}/{$maxScore} ({$percentage}%).");
    }

    /**
     * View individual quiz result & breakdown.
     */
    public function attempt(QuizAttempt $attempt): Response
    {
        $this->authorizeAttemptAccess($attempt);

        $attempt->load([
            'quiz:id,title,slug,category,passing_percentage,total_questions',
            'user:id,name,first_name,last_name,email,phone,institution,state,trade_specialization',
        ]);

        return Inertia::render('Teacher/Quizzes/Result', [
            'attempt' => $attempt,
            'canManage' => auth()->user()->hasRole(['super_admin', 'lms_admin', 'facilitator']),
        ]);
    }

    /**
     * Printable view of an individual teacher's assessment report / certificate.
     */
    public function printAttempt(QuizAttempt $attempt): Response
    {
        $this->authorizeAttemptAccess($attempt);

        $attempt->load([
            'quiz:id,title,slug,category,passing_percentage,total_questions',
            'user:id,name,first_name,last_name,email,phone,institution,state,trade_specialization',
        ]);

        return Inertia::render('Teacher/Quizzes/PrintIndividual', [
            'attempt' => $attempt,
        ]);
    }

    /**
     * Analytics & Evaluation Dashboard for Admins and Facilitators.
     */
    public function analytics(Request $request): Response
    {
        $this->authorizeEvaluator();

        $query = QuizAttempt::with([
            'quiz:id,title,slug,category,passing_percentage',
            'user:id,name,first_name,last_name,email,phone,institution,state,trade_specialization',
        ])->latest('completed_at');

        // Filter by state
        if ($request->filled('state') && $request->state !== 'all') {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('state', $request->state);
            });
        }

        // Filter by institution
        if ($request->filled('institution') && $request->institution !== 'all') {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('institution', $request->institution);
            });
        }

        // Filter by status (passed / needs_improvement)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'passed') {
                $query->where('passed', true);
            } elseif ($request->status === 'failed') {
                $query->where('passed', false);
            }
        }

        // Search teacher
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('institution', 'like', "%{$search}%")
                        ->orWhere('trade_specialization', 'like', "%{$search}%");
                });
            });
        }

        // Aggregate statistics for the filtered dataset
        $allMatchingAttempts = (clone $query)->get();

        $totalSubmissions = $allMatchingAttempts->count();
        $passedCount = $allMatchingAttempts->where('passed', true)->count();
        $averagePercentage = $totalSubmissions > 0 ? round($allMatchingAttempts->avg('percentage'), 1) : 0;
        $highestScore = $totalSubmissions > 0 ? $allMatchingAttempts->max('percentage') : 0;
        $lowestScore = $totalSubmissions > 0 ? $allMatchingAttempts->min('percentage') : 0;
        $passRate = $totalSubmissions > 0 ? round(($passedCount / $totalSubmissions) * 100, 1) : 0;

        // Misconception / Question Gap Analysis (compute which questions had lowest accuracy)
        $questionErrors = [];
        foreach ($allMatchingAttempts as $att) {
            if (is_array($att->answers)) {
                foreach ($att->answers as $ans) {
                    $qNum = $ans['question_number'] ?? 0;
                    if (! isset($questionErrors[$qNum])) {
                        $questionErrors[$qNum] = [
                            'question_number' => $qNum,
                            'question_text' => $ans['question_text'] ?? '',
                            'category' => $ans['category'] ?? '',
                            'total_attempts' => 0,
                            'incorrect_count' => 0,
                        ];
                    }
                    $questionErrors[$qNum]['total_attempts']++;
                    if (! ($ans['is_correct'] ?? false)) {
                        $questionErrors[$qNum]['incorrect_count']++;
                    }
                }
            }
        }

        $mostChallengingQuestions = collect($questionErrors)
            ->filter(fn ($q) => $q['total_attempts'] > 0)
            ->map(function ($q) {
                $q['error_rate'] = round(($q['incorrect_count'] / $q['total_attempts']) * 100, 1);
                $q['accuracy_rate'] = round(100 - $q['error_rate'], 1);

                return $q;
            })
            ->sortByDesc('error_rate')
            ->take(5)
            ->values();

        // Paginate results
        $attempts = $query->paginate(25)->withQueryString();

        // Available filter dropdown options
        $availableStates = User::whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        $availableInstitutions = User::whereNotNull('institution')
            ->distinct()
            ->orderBy('institution')
            ->pluck('institution');

        return Inertia::render('Teacher/Quizzes/Analytics', [
            'attempts' => $attempts,
            'filters' => [
                'search' => $request->search ?? '',
                'state' => $request->state ?? 'all',
                'institution' => $request->institution ?? 'all',
                'status' => $request->status ?? 'all',
            ],
            'stats' => [
                'total_submissions' => $totalSubmissions,
                'unique_teachers' => $allMatchingAttempts->pluck('user_id')->unique()->count(),
                'passed_count' => $passedCount,
                'failed_count' => $totalSubmissions - $passedCount,
                'average_percentage' => $averagePercentage,
                'pass_rate' => $passRate,
                'highest_score' => $highestScore,
                'lowest_score' => $lowestScore,
            ],
            'mostChallengingQuestions' => $mostChallengingQuestions,
            'availableStates' => $availableStates,
            'availableInstitutions' => $availableInstitutions,
        ]);
    }

    /**
     * Collective Master Evaluation Sheet (Printable Roster of Selected or All Teachers).
     */
    public function collectivePrint(Request $request): Response
    {
        $this->authorizeEvaluator();

        $selectedIds = $request->input('selected_ids', []);

        $query = QuizAttempt::with([
            'quiz:id,title,category',
            'user:id,name,first_name,last_name,email,phone,institution,state,trade_specialization',
        ])->orderBy('completed_at', 'desc');

        if (! empty($selectedIds) && is_array($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        } else {
            // If no specific IDs passed, apply any incoming filters
            if ($request->filled('state') && $request->state !== 'all') {
                $query->whereHas('user', fn ($q) => $q->where('state', $request->state));
            }
            if ($request->filled('institution') && $request->institution !== 'all') {
                $query->whereHas('user', fn ($q) => $q->where('institution', $request->institution));
            }
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('passed', $request->status === 'passed');
            }
        }

        $attempts = $query->get();

        $total = $attempts->count();
        $passed = $attempts->where('passed', true)->count();
        $avgScore = $total > 0 ? round($attempts->avg('percentage'), 1) : 0;

        return Inertia::render('Teacher/Quizzes/PrintCollective', [
            'attempts' => $attempts,
            'summary' => [
                'total_evaluated' => $total,
                'total_passed' => $passed,
                'total_needs_review' => $total - $passed,
                'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
                'average_score' => $avgScore,
                'generated_at' => now()->format('F j, Y, g:i A'),
                'evaluator_name' => auth()->user()->name,
            ],
        ]);
    }

    /**
     * Batch Print Individual Slips for all selected teachers.
     */
    public function batchIndividualPrint(Request $request): Response
    {
        $this->authorizeEvaluator();

        $selectedIds = $request->input('selected_ids', []);

        $query = QuizAttempt::with([
            'quiz:id,title,category,total_questions,passing_percentage',
            'user:id,name,first_name,last_name,email,phone,institution,state,trade_specialization',
        ])->orderBy('completed_at', 'desc');

        if (! empty($selectedIds) && is_array($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $attempts = $query->take(100)->get(); // safety bound at 100 slips

        return Inertia::render('Teacher/Quizzes/PrintBatchIndividual', [
            'attempts' => $attempts,
        ]);
    }

    /**
     * Export quiz assessment results to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorizeEvaluator();

        $selectedIds = $request->input('selected_ids', []);

        $query = QuizAttempt::with([
            'quiz:id,title',
            'user:id,name,email,phone,institution,state,trade_specialization',
        ])->latest('completed_at');

        if (! empty($selectedIds)) {
            $ids = is_string($selectedIds) ? explode(',', $selectedIds) : $selectedIds;
            $query->whereIn('id', $ids);
        }

        $filename = 'TVET_Teacher_MCQ_Evaluation_Results_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'Submission ID',
                'Teacher Name',
                'Email',
                'Phone',
                'Institution',
                'State',
                'Trade Specialization',
                'Quiz Title',
                'Score',
                'Max Score',
                'Percentage (%)',
                'Competency Status',
                'Grade',
                'Correct Count',
                'Incorrect Count',
                'Duration Taken',
                'Submission Date',
            ]);

            $query->chunk(100, function ($attempts) use ($handle) {
                foreach ($attempts as $att) {
                    $u = $att->user;
                    fputcsv($handle, [
                        $att->id,
                        $u?->name ?? 'N/A',
                        $u?->email ?? 'N/A',
                        $u?->phone ?? 'N/A',
                        $u?->institution ?? 'N/A',
                        $u?->state ?? 'N/A',
                        $u?->trade_specialization ?? 'N/A',
                        $att->quiz?->title ?? 'TVET Assessment',
                        $att->score,
                        $att->max_score,
                        $att->percentage,
                        $att->passed ? 'Competent (Passed)' : 'Needs Support',
                        $att->grade,
                        $att->correct_answers_count,
                        $att->incorrect_answers_count,
                        $att->formatted_duration,
                        $att->completed_at ? $att->completed_at->format('Y-m-d H:i:s') : 'N/A',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Manage, view, search, and set MCQ questions for a quiz across all lecture categories.
     */
    public function manageQuestions(Request $request, ?Quiz $quiz = null): Response
    {
        $this->authorizeEvaluator();

        $quiz = $quiz ?? Quiz::where('slug', 'tvet-teacher-competency-mcq-assessment')->first() ?? Quiz::firstOrFail();

        $query = $quiz->questions()->orderBy('question_number', 'asc');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('option_a', 'like', "%{$search}%")
                    ->orWhere('option_b', 'like', "%{$search}%")
                    ->orWhere('option_c', 'like', "%{$search}%")
                    ->orWhere('option_d', 'like', "%{$search}%");
            });
        }

        $questions = $query->paginate(25)->withQueryString();

        $allCategories = $this->getAvailableCategories($quiz);

        // Counts per category for this quiz
        $categoryCounts = QuizQuestion::where('quiz_id', $quiz->id)
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $allQuizzes = Quiz::orderBy('id', 'asc')
            ->withCount('questions')
            ->get()
            ->map(fn ($q) => [
                'id' => $q->id,
                'title' => $q->title,
                'slug' => $q->slug,
                'category' => $q->category,
                'total_questions' => $q->questions_count,
                'is_active' => (bool) $q->is_active,
            ]);

        return Inertia::render('Teacher/Quizzes/ManageQuestions', [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'slug' => $quiz->slug,
                'category' => $quiz->category,
                'total_questions' => $quiz->questions()->count(),
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'passing_percentage' => $quiz->passing_percentage,
                'description' => $quiz->description,
                'instructions' => $quiz->instructions,
                'is_active' => (bool) $quiz->is_active,
            ],
            'questions' => $questions,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? 'all',
            ],
            'availableCategories' => $allCategories,
            'categoryCounts' => $categoryCounts,
            'allQuizzes' => $allQuizzes,
        ]);
    }

    /**
     * Store a single newly created MCQ question.
     */
    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeEvaluator();

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:150'],
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'correct_option' => ['required', 'in:a,b,c,d,A,B,C,D'],
            'explanation' => ['nullable', 'string'],
            'points' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $nextNumber = ($quiz->questions()->max('question_number') ?? 0) + 1;

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_number' => $nextNumber,
            'category' => trim($validated['category']),
            'question_text' => trim($validated['question_text']),
            'option_a' => trim($validated['option_a']),
            'option_b' => trim($validated['option_b']),
            'option_c' => trim($validated['option_c']),
            'option_d' => trim($validated['option_d']),
            'correct_option' => strtolower(trim($validated['correct_option'])),
            'explanation' => ! empty($validated['explanation']) ? trim($validated['explanation']) : null,
            'points' => $validated['points'] ?? 1,
        ]);

        $quiz->update(['total_questions' => $quiz->questions()->count()]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_question_created',
            'entity_type' => QuizQuestion::class,
            'entity_id' => $question->id,
            'description' => "Created MCQ Question #{$nextNumber} in category '{$question->category}' for {$quiz->title}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "MCQ Question #{$nextNumber} created successfully in category '{$question->category}'.");
    }

    /**
     * Update an existing MCQ question.
     */
    public function updateQuestion(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeEvaluator();

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:150'],
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'correct_option' => ['required', 'in:a,b,c,d,A,B,C,D'],
            'explanation' => ['nullable', 'string'],
            'points' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $question->update([
            'category' => trim($validated['category']),
            'question_text' => trim($validated['question_text']),
            'option_a' => trim($validated['option_a']),
            'option_b' => trim($validated['option_b']),
            'option_c' => trim($validated['option_c']),
            'option_d' => trim($validated['option_d']),
            'correct_option' => strtolower(trim($validated['correct_option'])),
            'explanation' => ! empty($validated['explanation']) ? trim($validated['explanation']) : null,
            'points' => $validated['points'] ?? 1,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_question_updated',
            'entity_type' => QuizQuestion::class,
            'entity_id' => $question->id,
            'description' => "Updated MCQ Question #{$question->question_number} in category '{$question->category}'.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "MCQ Question #{$question->question_number} updated successfully.");
    }

    /**
     * Delete an MCQ question and re-sequence question numbers.
     */
    public function destroyQuestion(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeEvaluator();

        $quizId = $question->quiz_id;
        $quiz = Quiz::findOrFail($quizId);
        $deletedNumber = $question->question_number;

        $question->delete();

        // Re-sequence remaining questions
        $remaining = QuizQuestion::where('quiz_id', $quizId)
            ->orderBy('question_number', 'asc')
            ->get();

        foreach ($remaining as $idx => $q) {
            $newNum = $idx + 1;
            if ($q->question_number !== $newNum) {
                $q->update(['question_number' => $newNum]);
            }
        }

        $quiz->update(['total_questions' => $remaining->count()]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_question_deleted',
            'entity_type' => QuizQuestion::class,
            'entity_id' => $question->id,
            'description' => "Deleted MCQ Question #{$deletedNumber} from {$quiz->title}. Remaining questions re-sequenced.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Question #{$deletedNumber} deleted. Questions re-sequenced. Total questions: {$remaining->count()}.");
    }

    /**
     * Bulk CSV Upload of MCQ questions into a quiz.
     */
    public function bulkUploadCsv(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeEvaluator();

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'mode' => ['nullable', 'in:append,replace'],
        ]);

        $file = $request->file('csv_file');
        $mode = $request->input('mode', 'append');

        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return back()->with('error', 'Unable to open the uploaded CSV file.');
        }

        $rawHeaders = fgetcsv($handle);
        if (! $rawHeaders || count($rawHeaders) < 6) {
            fclose($handle);

            return back()->with('error', 'Invalid CSV format. Please download and use the provided sample template.');
        }

        // Clean BOM and lowercase headers
        $rawHeaders[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeaders[0]);
        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), $rawHeaders);

        // Find header column positions
        $colMap = [
            'category' => array_search('category', $headers),
            'question_text' => array_search('question_text', $headers) !== false ? array_search('question_text', $headers) : array_search('question', $headers),
            'option_a' => array_search('option_a', $headers),
            'option_b' => array_search('option_b', $headers),
            'option_c' => array_search('option_c', $headers),
            'option_d' => array_search('option_d', $headers),
            'correct_option' => array_search('correct_option', $headers) !== false ? array_search('correct_option', $headers) : (array_search('answer', $headers) !== false ? array_search('answer', $headers) : array_search('correct_answer', $headers)),
            'explanation' => array_search('explanation', $headers),
            'points' => array_search('points', $headers),
        ];

        if ($colMap['question_text'] === false || $colMap['option_a'] === false || $colMap['option_b'] === false || $colMap['option_c'] === false || $colMap['option_d'] === false || $colMap['correct_option'] === false) {
            fclose($handle);

            return back()->with('error', 'CSV is missing mandatory columns: question_text, option_a, option_b, option_c, option_d, correct_option.');
        }

        if ($mode === 'replace') {
            $quiz->questions()->delete();
            $nextNumber = 1;
        } else {
            $nextNumber = ($quiz->questions()->max('question_number') ?? 0) + 1;
        }

        $importedCount = 0;
        $skippedCount = 0;
        $categoriesSeen = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue; // skip blank line
            }

            $questionText = isset($row[$colMap['question_text']]) ? trim((string) $row[$colMap['question_text']]) : '';
            $optA = isset($row[$colMap['option_a']]) ? trim((string) $row[$colMap['option_a']]) : '';
            $optB = isset($row[$colMap['option_b']]) ? trim((string) $row[$colMap['option_b']]) : '';
            $optC = isset($row[$colMap['option_c']]) ? trim((string) $row[$colMap['option_c']]) : '';
            $optD = isset($row[$colMap['option_d']]) ? trim((string) $row[$colMap['option_d']]) : '';
            $correct = isset($row[$colMap['correct_option']]) ? strtolower(trim((string) $row[$colMap['correct_option']])) : '';

            // Clean single letter answer e.g. "A" -> "a" or "Option B" -> "b"
            if (strlen($correct) > 1 && preg_match('/([a-d])/i', $correct, $m)) {
                $correct = strtolower($m[1]);
            }

            if ($questionText === '' || $optA === '' || $optB === '' || $optC === '' || $optD === '' || ! in_array($correct, ['a', 'b', 'c', 'd'])) {
                $skippedCount++;

                continue;
            }

            $cat = ($colMap['category'] !== false && ! empty($row[$colMap['category']]))
                ? trim((string) $row[$colMap['category']])
                : 'General';

            $explanation = ($colMap['explanation'] !== false && ! empty($row[$colMap['explanation']]))
                ? trim((string) $row[$colMap['explanation']])
                : null;

            $points = ($colMap['points'] !== false && is_numeric($row[$colMap['points']]))
                ? max(1, (int) $row[$colMap['points']])
                : 1;

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_number' => $nextNumber++,
                'category' => $cat,
                'question_text' => $questionText,
                'option_a' => $optA,
                'option_b' => $optB,
                'option_c' => $optC,
                'option_d' => $optD,
                'correct_option' => $correct,
                'explanation' => $explanation,
                'points' => $points,
            ]);

            $importedCount++;
            $categoriesSeen[$cat] = true;
        }

        fclose($handle);

        $totalInQuiz = $quiz->questions()->count();
        $quiz->update(['total_questions' => $totalInQuiz]);

        $catList = implode(', ', array_keys($categoriesSeen));

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'quiz_questions_bulk_imported',
            'entity_type' => Quiz::class,
            'entity_id' => $quiz->id,
            'description' => "Bulk imported {$importedCount} MCQ questions into {$quiz->title} (mode: {$mode}).",
            'metadata' => [
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'total_questions' => $totalInQuiz,
                'categories' => array_keys($categoriesSeen),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $msg = "Bulk import complete! Successfully imported {$importedCount} MCQ question(s) across categories: {$catList}. Total quiz questions: {$totalInQuiz}.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} rows skipped due to missing text or invalid correct option).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Download a ready-to-use sample CSV template for MCQ questions.
     */
    public function downloadSampleCsv(): StreamedResponse
    {
        $this->authorizeEvaluator();

        $filename = 'sample_tvet_mcq_questions_template.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'category',
                'question_text',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_option',
                'explanation',
                'points',
            ]);

            $sampleRows = [
                [
                    'Solar PV Installation and Maintenance',
                    'When calculating solar battery bank capacity for a domestic installation, why is Depth of Discharge (DoD) a critical factor?',
                    'It determines the chemical color of the battery terminals',
                    'It prevents deep over-discharge that drastically reduces battery cycle life',
                    'It increases the solar panel open-circuit voltage',
                    'It changes the inverter alternating current frequency',
                    'b',
                    'Exceeding recommended DoD in lead-acid or lithium batteries accelerates sulfation and degrades cycle lifespan.',
                    '1',
                ],
                [
                    'CBT & Occupational Standards',
                    'What defines a trainee\'s successful completion of a CBT module?',
                    'Sitting in class for 120 mandatory clock hours',
                    'Proven ability to perform industry-defined tasks to standard',
                    'Scoring higher than 50% of classmates on a written exam',
                    'Writing a term paper summary of the textbook',
                    'b',
                    'CBT is mastery-based: competence is judged by demonstrated practical performance against occupational benchmarks.',
                    '1',
                ],
                [
                    'Electrical Works',
                    'Which instrument is used to test electrical insulation resistance on high-voltage power cables before energization?',
                    'Hydrometer',
                    'Megohmmeter (Megger)',
                    'Tachometer',
                    'Digital Vernier Caliper',
                    'b',
                    'A Megger applies high direct-current test voltage to verify insulation integrity and detect breakdown leakage.',
                    '1',
                ],
                [
                    'Workshop & Safety Management',
                    'What is the highest and most effective level in the Hierarchy of Hazard Controls?',
                    'Issuing safety boots and hard hats',
                    'Elimination of the hazard at source',
                    'Posting yellow warning caution signs',
                    'Conducting monthly staff safety lectures',
                    'b',
                    'Completely removing the hazard is the most effective and reliable safeguard in occupational safety.',
                    '1',
                ],
                [
                    'Welding and Fabrication',
                    'What is the primary function of shielding gas (such as Argon or CO2) in MIG/MAG welding?',
                    'To cool the metal workpiece rapidly to make it brittle',
                    'To protect the molten weld pool from atmospheric oxygen and nitrogen contamination',
                    'To polish the steel surface to a reflective shine',
                    'To ignite the electric arc without a grounding clamp',
                    'b',
                    'Shielding gas displaces ambient air around the molten pool, preventing porosity and slag inclusion.',
                    '1',
                ],
                [
                    'Generative AI in TVET',
                    'How can a technical instructor best leverage Generative AI for technical lesson preparation?',
                    'Blindly copy and paste computer outputs without review',
                    'Use structured prompts specifying the trade and NSQF level, then verify accuracy for workshop reality',
                    'Rely on AI to operate live workshop machines on behalf of trainees',
                    'Ask AI to generate fake learner attendance registers',
                    'b',
                    'Educators use AI as an ideation co-pilot while maintaining pedagogical oversight, safety checks, and technical accuracy.',
                    '1',
                ],
            ];

            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }

    protected function authorizeAttemptAccess(QuizAttempt $attempt): void
    {
        $user = auth()->user();
        if ($attempt->user_id !== $user->id && ! $user->hasRole(['super_admin', 'lms_admin', 'facilitator'])) {
            abort(403, 'Unauthorized access to this assessment attempt.');
        }
    }

    protected function authorizeEvaluator(): void
    {
        if (! auth()->user()->hasRole(['super_admin', 'lms_admin', 'facilitator'])) {
            abort(403, 'Unauthorized access to assessment evaluation and analytics.');
        }
    }

    /**
     * Get consolidated list of standard pedagogy, trade, and custom quiz categories.
     */
    protected function getAvailableCategories(?Quiz $quiz = null): array
    {
        $quizCategories = $quiz
            ? QuizQuestion::where('quiz_id', $quiz->id)->distinct()->pluck('category')->toArray()
            : QuizQuestion::distinct()->pluck('category')->toArray();

        $tradeCategories = TradeCategory::where('is_active', true)
            ->orderBy('order_index')
            ->pluck('name')
            ->toArray();

        $standardCategories = [
            'CBT & Occupational Standards',
            'Instructional Planning & Delivery',
            'Workshop & Safety Management',
            'Assessment & Rubrics',
            'Digital Tools & Inclusive Pedagogy',
            'Solar PV Installation and Maintenance',
            'Electrical Works',
            'Welding and Fabrication',
            'Automobile Repairs',
            'Carpentry and Joinery',
            'Agriculture',
            'Catering Services',
            'Garment Making',
            'ICT and Digital Literacy',
            'Cybersecurity',
            'Generative AI',
            'Web Development',
            'General Technical Education',
            'Instructor Professional Development',
        ];

        $allCategories = array_values(array_unique(array_filter(array_merge(
            $quizCategories,
            $tradeCategories,
            $standardCategories
        ))));
        sort($allCategories);

        return $allCategories;
    }
}
