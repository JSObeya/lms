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
        // 1. Categories (TVET Trades + ICT / Professional Development)
        Schema::create('trade_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_tvet_trade')->default(true);
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->foreignId('trade_category_id')->constrained('trade_categories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Creator/Teacher
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('objectives_overview')->nullable();
            $table->string('duration')->nullable(); // e.g. "4 Weeks", "40 Hours"
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('enrollment_type', ['open', 'approval', 'invite_only'])->default('open');
            $table->enum('visibility', ['public', 'enrolled_only', 'private'])->default('public');
            $table->enum('status', ['draft', 'under_review', 'published', 'archived'])->default('draft');
            $table->boolean('is_practice')->default(false); // Mode A (Sandbox) vs Mode B (Institutional)
            $table->json('settings')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Course Instructors / Co-instructors / Facilitators
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['co_instructor', 'facilitator', 'assessor'])->default('co_instructor');
            $table->timestamps();
            $table->unique(['course_id', 'user_id', 'role']);
        });

        // 4. Course Enrollments
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['student', 'observer'])->default('student');
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
        });

        // 5. Course Sections (Topics)
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 6. Lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->integer('order_index')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 7. Learning Objectives
        Schema::create('learning_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->string('action_verb'); // e.g. Identify, Assemble, Calibrate
            $table->text('condition'); // e.g. "Given a supervised solar PV rig and reference sheet"
            $table->text('performance_standard'); // e.g. "correctly identify at least 8 of 10 PV components"
            $table->text('complete_text'); // Full measurable text
            $table->enum('bloom_level', ['remember', 'understand', 'apply', 'analyze', 'evaluate', 'create'])->default('apply');
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 8. Learning Resources (PDF, DOCX, Video, Worksheet, URLs)
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('resource_type', ['pdf', 'docx', 'pptx', 'xlsx', 'image', 'url', 'video_embed', 'worksheet'])->default('pdf');
            $table->string('file_path')->nullable(); // Saved in secure private storage
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->text('external_url')->nullable();
            $table->boolean('is_downloadable')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('learning_objectives');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_instructors');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('trade_categories');
    }
};
