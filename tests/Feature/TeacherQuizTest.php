<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Database\Seeders\RoleAndCategorySeeder;
use Database\Seeders\TvEtCompetencyQuizSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherQuizTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;

    protected User $admin;

    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndCategorySeeder::class);
        $this->seed(TvEtCompetencyQuizSeeder::class);

        $this->teacher = User::factory()->create([
            'institution' => 'GTC Makurdi',
            'state' => 'Benue',
            'trade_specialization' => 'Solar PV & Electrical',
        ]);
        $this->teacher->assignRole('teacher');

        $this->admin = User::factory()->create([
            'institution' => 'IDEAS TVET HQ',
            'state' => 'Benue',
        ]);
        $this->admin->assignRole('lms_admin');

        $this->quiz = Quiz::where('slug', 'tvet-teacher-competency-mcq-assessment')->firstOrFail();
    }

    public function test_unauthenticated_user_cannot_access_quizzes(): void
    {
        $response = $this->get(route('quizzes.index'));
        $response->assertRedirect('/login');
    }

    public function test_teacher_can_view_quizzes_index(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('quizzes.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/Index')
            ->has('quizzes', 1)
            ->where('quizzes.0.total_questions', 50)
            ->where('isEvaluator', false)
        );
    }

    public function test_teacher_can_view_assessment_taking_screen(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('quizzes.show', $this->quiz->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/Take')
            ->has('questions', 50)
            ->where('quiz.total_questions', 50)
        );
    }

    public function test_teacher_can_submit_answers_and_receive_instant_score_and_grade(): void
    {
        // Construct answers for all 50 questions: answer 40 correctly and 10 incorrectly
        $questions = $this->quiz->questions()->orderBy('question_number')->get();
        $answers = [];

        foreach ($questions as $index => $q) {
            if ($index < 40) {
                // Correct answer
                $answers[$q->id] = $q->correct_option;
            } else {
                // Incorrect answer
                $answers[$q->id] = $q->correct_option === 'a' ? 'b' : 'a';
            }
        }

        $response = $this->actingAs($this->teacher)->post(route('quizzes.submit', $this->quiz->id), [
            'answers' => $answers,
            'started_at' => now()->subMinutes(25)->toISOString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $attempt = QuizAttempt::where('user_id', $this->teacher->id)->firstOrFail();

        $this->assertEquals(40, $attempt->score);
        $this->assertEquals(50, $attempt->max_score);
        $this->assertEquals(80.00, $attempt->percentage);
        $this->assertTrue($attempt->passed);
        $this->assertEquals('Distinction', $attempt->grade);
        $this->assertEquals(40, $attempt->correct_answers_count);
        $this->assertEquals(10, $attempt->incorrect_answers_count);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->teacher->id,
            'action' => 'teacher_quiz_submitted',
        ]);
    }

    public function test_teacher_can_view_and_print_own_evaluation_result(): void
    {
        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->teacher->id,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now(),
            'status' => 'completed',
            'score' => 45,
            'max_score' => 50,
            'total_questions' => 50,
            'correct_answers_count' => 45,
            'incorrect_answers_count' => 5,
            'unanswered_count' => 0,
            'percentage' => 90.00,
            'passed' => true,
            'grade' => 'Distinction',
            'answers' => [],
            'category_scores' => [],
        ]);

        // View result
        $response = $this->actingAs($this->teacher)->get(route('quizzes.attempt', $attempt->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/Result')
            ->where('attempt.score', 45)
            ->where('attempt.percentage', 90)
        );

        // Print view
        $printResponse = $this->actingAs($this->teacher)->get(route('quizzes.attempt.print', $attempt->id));
        $printResponse->assertStatus(200);
        $printResponse->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/PrintIndividual')
            ->where('attempt.id', $attempt->id)
        );
    }

    public function test_teacher_cannot_view_another_teachers_attempt(): void
    {
        $anotherTeacher = User::factory()->create();
        $anotherTeacher->assignRole('teacher');

        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $anotherTeacher->id,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now(),
            'status' => 'completed',
            'score' => 35,
            'max_score' => 50,
            'total_questions' => 50,
            'percentage' => 70.00,
            'passed' => true,
            'grade' => 'Merit',
        ]);

        $response = $this->actingAs($this->teacher)->get(route('quizzes.attempt', $attempt->id));
        $response->assertStatus(403);
    }

    public function test_ordinary_teacher_cannot_access_admin_analytics_hub(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('admin.quizzes.analytics'));
        $response->assertStatus(403);
    }

    public function test_admin_and_facilitator_can_access_evaluation_analytics(): void
    {
        // Create sample attempts
        QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->teacher->id,
            'started_at' => now()->subMinutes(35),
            'completed_at' => now(),
            'status' => 'completed',
            'score' => 42,
            'max_score' => 50,
            'total_questions' => 50,
            'percentage' => 84.00,
            'passed' => true,
            'grade' => 'Distinction',
            'answers' => [
                ['question_number' => 1, 'category' => 'CBT', 'is_correct' => true, 'question_text' => 'Sample Q1'],
                ['question_number' => 2, 'category' => 'CBT', 'is_correct' => false, 'question_text' => 'Sample Q2'],
            ],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quizzes.analytics'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/Analytics')
            ->has('attempts.data', 1)
            ->where('stats.total_submissions', 1)
            ->where('stats.average_percentage', fn ($val) => (float) $val === 84.0)
        );
    }

    public function test_admin_can_generate_collective_evaluation_sheet_for_selected_teachers(): void
    {
        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->teacher->id,
            'started_at' => now()->subMinutes(20),
            'completed_at' => now(),
            'status' => 'completed',
            'score' => 38,
            'max_score' => 50,
            'total_questions' => 50,
            'percentage' => 76.00,
            'passed' => true,
            'grade' => 'Merit',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quizzes.collective-print', [
            'selected_ids' => [$attempt->id],
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Teacher/Quizzes/PrintCollective')
            ->has('attempts', 1)
            ->where('summary.total_evaluated', 1)
            ->where('summary.total_passed', 1)
        );
    }

    public function test_admin_can_export_evaluation_results_as_csv(): void
    {
        QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->teacher->id,
            'started_at' => now()->subMinutes(20),
            'completed_at' => now(),
            'status' => 'completed',
            'score' => 40,
            'max_score' => 50,
            'total_questions' => 50,
            'percentage' => 80.00,
            'passed' => true,
            'grade' => 'Distinction',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quizzes.export-csv'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('TVET_Teacher_MCQ_Evaluation_Results', $response->headers->get('content-disposition'));
    }
}
