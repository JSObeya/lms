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
        // 1. Rubrics
        Schema::create('rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Creator
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_points', 6, 2)->default(100.00);
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        // 2. Rubric Criteria
        Schema::create('rubric_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_id')->constrained('rubrics')->cascadeOnDelete();
            $table->string('title'); // e.g. "Criterion 1 — Component identification"
            $table->text('description')->nullable();
            $table->decimal('max_points', 6, 2)->default(20.00);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 3. Rubric Achievement Levels
        Schema::create('rubric_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_criterion_id')->constrained('rubric_criteria')->cascadeOnDelete();
            $table->string('title'); // e.g. "Exemplary", "Proficient", "Developing", "Novice"
            $table->text('descriptor'); // Explanation of standard required
            $table->decimal('points', 6, 2)->default(0.00);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 4. Assignments
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->foreignId('rubric_id')->nullable()->constrained('rubrics')->nullOnDelete();
            $table->string('title');
            $table->longText('instructions');
            $table->decimal('max_score', 6, 2)->default(100.00);
            $table->dateTime('allow_submissions_from')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('cut_off_date')->nullable();
            $table->string('timezone')->default('Africa/Lagos');
            $table->enum('submission_type', ['file', 'online_text', 'url', 'file_and_text'])->default('file');
            $table->string('accepted_file_types')->default('pdf,docx,jpg,png,zip');
            $table->integer('max_files')->default(3);
            $table->integer('max_file_size_mb')->default(15);
            $table->enum('late_submission_policy', ['allowed', 'disallowed', 'penalty'])->default('allowed');
            $table->integer('penalty_percentage')->default(0);
            $table->integer('attempt_limit')->default(1);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamps();
        });

        // 5. Assignment Submissions
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Student
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned'])->default('submitted');
            $table->longText('online_text')->nullable();
            $table->text('submission_url')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('grade_released')->default(false);
            $table->decimal('final_score', 6, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
        });

        // 6. Submission Attachments (Stored privately)
        Schema::create('submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        // 7. Rubric Assessments (Evaluation instance)
        Schema::create('rubric_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->foreignId('rubric_id')->constrained('rubrics')->cascadeOnDelete();
            $table->foreignId('assessed_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_calculated_score', 6, 2)->default(0.00);
            $table->text('general_feedback')->nullable();
            $table->boolean('is_released')->default(false);
            $table->timestamps();
        });

        // 8. Rubric Assessment Items (Scores per criterion)
        Schema::create('rubric_assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_assessment_id')->constrained('rubric_assessments')->cascadeOnDelete();
            $table->foreignId('rubric_criterion_id')->constrained('rubric_criteria')->cascadeOnDelete();
            $table->foreignId('rubric_level_id')->nullable()->constrained('rubric_levels')->nullOnDelete();
            $table->decimal('awarded_points', 6, 2)->default(0.00);
            $table->text('assessor_comment')->nullable();
            $table->timestamps();
        });

        // 9. Course Grades & Gradebook
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Student
            $table->decimal('max_score', 6, 2)->default(100.00);
            $table->decimal('obtained_score', 6, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('is_override')->default(false);
            $table->text('override_reason')->nullable();
            $table->boolean('is_released')->default(false);
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'assignment_id', 'user_id']);
        });

        // 10. Grade Histories
        Schema::create('grade_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->decimal('previous_score', 6, 2)->nullable();
            $table->decimal('new_score', 6, 2);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_histories');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('rubric_assessment_items');
        Schema::dropIfExists('rubric_assessments');
        Schema::dropIfExists('submission_attachments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('rubric_levels');
        Schema::dropIfExists('rubric_criteria');
        Schema::dropIfExists('rubrics');
    }
};
