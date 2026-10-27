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
        // 1. Day 8 TVET Practical Wizard Progress Tracking
        Schema::create('day8_practice_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Teacher
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->boolean('step_1_course_created')->default(false);
            $table->boolean('step_2_topic_labeled')->default(false);
            $table->boolean('step_3_objective_saved')->default(false);
            $table->boolean('step_4_resource_attached')->default(false);
            $table->boolean('step_5_assignment_created')->default(false);
            $table->boolean('step_6_deadline_configured')->default(false);
            $table->boolean('step_7_rubric_activated')->default(false);
            $table->boolean('step_8_preview_viewed')->default(false);
            $table->boolean('step_9_sample_graded')->default(false);
            $table->boolean('step_10_report_exported')->default(false);
            $table->boolean('is_verified_by_facilitator')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('facilitator_feedback')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        // 2. TVET Practical Competencies
        Schema::create('tvet_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('trade_category_id')->nullable()->constrained('trade_categories')->nullOnDelete();
            $table->string('occupational_task'); // e.g. "Install 500W Monocrystalline PV Array with Inverter"
            $table->text('required_knowledge')->nullable();
            $table->text('required_skills')->nullable();
            $table->text('performance_conditions')->nullable();
            $table->text('required_tools')->nullable();
            $table->text('safety_precautions')->nullable();
            $table->timestamps();
        });

        // 3. TVET Performance Criteria
        Schema::create('tvet_performance_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_competency_id')->constrained('tvet_competencies')->cascadeOnDelete();
            $table->string('criterion_text');
            $table->boolean('is_critical_safety')->default(false); // Immediate fail if violated
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 4. Practical Assessments
        Schema::create('practical_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_competency_id')->constrained('tvet_competencies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Student / Trainee
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete(); // Assessor
            $table->enum('outcome', ['competent', 'not_yet_competent', 'requires_further_evidence'])->default('not_yet_competent');
            $table->boolean('safety_passed')->default(true);
            $table->text('assessor_comments')->nullable();
            $table->date('reassessment_date')->nullable();
            $table->timestamps();
        });

        // 5. Competency Evidence
        Schema::create('competency_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practical_assessment_id')->constrained('practical_assessments')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->default('image'); // image, pdf, document
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 6. Teacher Portfolios
        Schema::create('teacher_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('TVET Digital Teaching Portfolio');
            $table->text('bio')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->boolean('is_shared')->default(false);
            $table->foreignId('shared_with_facilitator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique('user_id');
        });

        // 7. Portfolio Items
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_portfolio_id')->constrained('teacher_portfolios')->cascadeOnDelete();
            $table->enum('category', [
                'lesson_documents',
                'digital_resources',
                'objectives',
                'assignments',
                'rubrics',
                'sample_grading',
                'feedback_records',
                'microteaching',
                'school_plans'
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });

        // 8. Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. "course_created", "grade_released", "user_imported"
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 9. Export Logs
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->enum('export_type', ['gradebook_csv', 'gradebook_xlsx', 'portfolio_pdf', 'audit_csv']);
            $table->string('file_name');
            $table->integer('records_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('teacher_portfolios');
        Schema::dropIfExists('competency_evidence');
        Schema::dropIfExists('practical_assessments');
        Schema::dropIfExists('tvet_performance_criteria');
        Schema::dropIfExists('tvet_competencies');
        Schema::dropIfExists('day8_practice_progress');
    }
};
