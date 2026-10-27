<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Days 9 & 10 TVET Pedagogy & Inclusive Design Studio.
     */
    public function up(): void
    {
        // 1. Occupational Standards and Competency Mappings
        Schema::create('tvet_occupational_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_category_id')->nullable()->constrained('trade_categories')->nullOnDelete();
            $table->string('code')->index(); // e.g. NOS-SLR-L3-01
            $table->string('title'); // e.g. "Install and Maintain Solar Photovoltaic Systems"
            $table->string('source_authority'); // e.g. "NBTE National Occupational Standards (NOS) Nigeria"
            $table->string('version')->default('2024 / Rev 2.1');
            $table->boolean('is_official')->default(true); // false if instructor-created example
            $table->json('performance_criteria')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Competency-Based Lesson Plans (Day 9 Foundation)
        Schema::create('competency_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Author Teacher
            $table->foreignId('trade_category_id')->nullable()->constrained('trade_categories')->nullOnDelete();
            $table->foreignId('occupational_standard_id')->nullable()->constrained('tvet_occupational_standards')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->string('title');
            $table->string('occupational_task');
            $table->integer('duration_minutes')->default(120);
            $table->string('target_level')->default('Level 3 / Intermediate');
            $table->json('learning_outcomes')->nullable();
            $table->json('demonstration_plan')->nullable();
            $table->json('guided_practice')->nullable();
            $table->json('independent_practice')->nullable();
            $table->json('project_based_tasks')->nullable();
            $table->json('fault_diagnosis_scenarios')->nullable();
            $table->json('work_based_learning')->nullable();
            $table->json('assessment_criteria')->nullable();
            $table->enum('status', ['draft', 'published', 'in_review'])->default('draft');
            $table->integer('version')->default(1);
            $table->boolean('is_template')->default(false);
            $table->string('template_trade')->nullable()->index(); // solar, electrical, welding, agriculture, catering, carpentry, automobile, garment
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Inclusive Lesson Designer Adaptations (Day 10 Extension)
        Schema::create('inclusive_lesson_adaptations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_lesson_id')->constrained('competency_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->json('structured_plan')->nullable(); // Timing & sequential phase flow
            $table->json('differentiated_activities')->nullable(); // Beginner, Intermediate, Advanced
            $table->json('udl_engagement')->nullable(); // Universal Design for Learning: Engagement
            $table->json('udl_representation')->nullable(); // UDL: Representation & Accessible Formats
            $table->json('udl_action_expression')->nullable(); // UDL: Action and Expression
            $table->json('learner_accommodations')->nullable(); // Accommodations matrix
            $table->boolean('safety_standards_maintained')->default(true); // Non-negotiable safety check
            $table->json('gender_responsive_strategies')->nullable(); // Gender-responsive instruction
            $table->json('workshop_rotation_schedule')->nullable(); // Equipment & station matrix
            $table->json('workshop_safety_arrangements')->nullable(); // PPE, emergency protocols, lockout
            $table->enum('status', ['draft', 'submitted', 'approved', 'revisions_requested'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. Practical Training Progress across Days 9 and 10
        Schema::create('pedagogy_training_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('competency_lesson_id')->nullable()->constrained('competency_lessons')->nullOnDelete();
            $table->foreignId('inclusive_adaptation_id')->nullable()->constrained('inclusive_lesson_adaptations')->nullOnDelete();

            // Day 9 flags
            $table->boolean('day9_demonstration_ready')->default(false);
            $table->boolean('day9_exercise_ready')->default(false);
            $table->boolean('day9_criteria_ready')->default(false);
            $table->boolean('day9_completed')->default(false);
            $table->timestamp('day9_completed_at')->nullable();
            $table->foreignId('day9_verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Day 10 flags
            $table->boolean('day10_differentiated_ready')->default(false);
            $table->boolean('day10_udl_ready')->default(false);
            $table->boolean('day10_safety_verified')->default(false);
            $table->boolean('day10_completed')->default(false);
            $table->timestamp('day10_completed_at')->nullable();
            $table->foreignId('day10_verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('facilitator_feedback')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // 5. Facilitator Reviews & Rubric Scoring
        Schema::create('pedagogy_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_lesson_id')->constrained('competency_lessons')->cascadeOnDelete();
            $table->foreignId('inclusive_adaptation_id')->nullable()->constrained('inclusive_lesson_adaptations')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('facilitator_id')->constrained('users')->cascadeOnDelete();
            $table->enum('module_type', ['day9', 'day10'])->index();
            $table->enum('status', ['under_review', 'approved', 'revisions_requested'])->default('under_review');
            $table->json('rubric_scores')->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->default(100.00);
            $table->text('written_feedback')->nullable();
            $table->text('revision_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // 6. Lesson Revisions History
        Schema::create('competency_lesson_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_lesson_id')->constrained('competency_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('version');
            $table->json('snapshot_data');
            $table->string('change_summary')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_lesson_revisions');
        Schema::dropIfExists('pedagogy_reviews');
        Schema::dropIfExists('pedagogy_training_progress');
        Schema::dropIfExists('inclusive_lesson_adaptations');
        Schema::dropIfExists('competency_lessons');
        Schema::dropIfExists('tvet_occupational_standards');
    }
};
