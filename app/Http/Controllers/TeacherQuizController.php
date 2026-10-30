<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $quizzes = Quiz::active()
            ->withCount('questions')
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
        ]);
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
}
