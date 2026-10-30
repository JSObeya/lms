<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('category', 100)->default('TVET Pedagogy');
            $table->unsignedSmallInteger('time_limit_minutes')->nullable()->default(60);
            $table->unsignedTinyInteger('passing_percentage')->default(60);
            $table->unsignedSmallInteger('total_questions')->default(50);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_active', 'category']);
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->unsignedSmallInteger('question_number')->default(1);
            $table->string('category', 100)->default('General');
            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->string('correct_option', 5); // 'a', 'b', 'c', or 'd'
            $table->text('explanation')->nullable();
            $table->unsignedSmallInteger('points')->default(1);
            $table->timestamps();

            $table->index(['quiz_id', 'question_number']);
            $table->index(['quiz_id', 'category']);
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 30)->default('completed'); // 'in_progress', 'completed'
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedSmallInteger('max_score')->default(50);
            $table->unsignedSmallInteger('total_questions')->default(50);
            $table->unsignedSmallInteger('correct_answers_count')->default(0);
            $table->unsignedSmallInteger('incorrect_answers_count')->default(0);
            $table->unsignedSmallInteger('unanswered_count')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('passed')->default(false);
            $table->string('grade', 50)->default('Needs Improvement');
            $table->json('answers')->nullable(); // [{"question_id": 1, "selected": "b", "is_correct": true}, ...]
            $table->json('category_scores')->nullable(); // {"Safety": {"correct": 8, "total": 10}, ...}
            $table->text('evaluator_feedback')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['quiz_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['passed', 'percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
