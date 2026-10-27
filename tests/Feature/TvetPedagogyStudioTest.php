<?php

namespace Tests\Feature;

use App\Models\CompetencyLesson;
use App\Models\Course;
use App\Models\InclusiveLessonAdaptation;
use App\Models\PedagogyTrainingProgress;
use App\Models\TeacherPortfolio;
use App\Models\TradeCategory;
use App\Models\TvetOccupationalStandard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TvetPedagogyStudioTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacherA;

    protected User $teacherB;

    protected User $facilitator;

    protected TradeCategory $trade;

    protected TvetOccupationalStandard $standard;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'lms_admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'facilitator']);

        $this->teacherA = User::factory()->create([
            'name' => 'Engr. Terver Shima',
            'email' => 'terver.teacher@gregicthub.com',
            'account_status' => 'active',
        ]);
        $this->teacherA->assignRole('teacher');

        $this->teacherB = User::factory()->create([
            'name' => 'Dooshima Akor',
            'email' => 'dooshima.teacher@gregicthub.com',
            'account_status' => 'active',
        ]);
        $this->teacherB->assignRole('teacher');

        $this->facilitator = User::factory()->create([
            'name' => 'Dr. James Obeya',
            'email' => 'james.facilitator@gregicthub.com',
            'account_status' => 'active',
        ]);
        $this->facilitator->assignRole('facilitator');

        $this->trade = TradeCategory::create([
            'name' => 'Solar PV Installation and Maintenance',
            'slug' => 'solar-pv-installation-and-maintenance',
            'description' => 'Solar photovoltaic installation',
            'is_tvet_trade' => true,
            'is_active' => true,
        ]);

        $this->standard = TvetOccupationalStandard::create([
            'trade_category_id' => $this->trade->id,
            'code' => 'NBTE/NOS/SLR-01/2023',
            'title' => 'Install, Test, and Commission Photovoltaic Systems',
            'source_authority' => 'NBTE National Occupational Standards Nigeria',
            'version' => '2023 / Rev 2.0',
            'is_official' => true,
            'performance_criteria' => [
                ['order' => 1, 'criterion' => 'Select and wear correct PPE.', 'is_critical_safety' => true],
                ['order' => 2, 'criterion' => 'Verify open circuit voltage Voc with multimeter.', 'is_critical_safety' => false],
            ],
        ]);
    }

    /**
     * 1. A teacher creates an owned course.
     */
    public function test_teacher_can_create_an_owned_course(): void
    {
        $response = $this->actingAs($this->teacherA)->post(route('courses.store'), [
            'title' => 'Solar PV Practical Installation Level 3',
            'description' => 'Comprehensive vocational training on solar system sizing and combiner termination.',
            'trade_category_id' => $this->trade->id,
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'duration' => '6 Weeks',
            'is_practice' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', [
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV Practical Installation Level 3',
            'trade_category_id' => $this->trade->id,
        ]);
    }

    /**
     * 2. The teacher creates a competency-based practical lesson.
     */
    public function test_teacher_can_create_a_competency_based_practical_lesson(): void
    {
        $course = Course::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV Tech',
            'slug' => 'solar-pv-tech',
            'code' => 'SOL-101',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'trade_category_id' => $this->trade->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->teacherA)->post(route('pedagogy.lessons.store'), [
            'course_id' => $course->id,
            'trade_category_id' => $this->trade->id,
            'occupational_standard_id' => $this->standard->id,
            'title' => 'Combiner Box Termination and DC Disconnect Wiring',
            'occupational_task' => 'Terminate 4-string PV array into DC combiner box with 1000V DC surge protection.',
            'duration_minutes' => 180,
            'target_level' => 'NSQF Level 3',
            'status' => 'published',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competency_lessons', [
            'user_id' => $this->teacherA->id,
            'title' => 'Combiner Box Termination and DC Disconnect Wiring',
            'occupational_standard_id' => $this->standard->id,
            'version' => 1,
        ]);
    }

    /**
     * 3. The teacher defines measurable learning outcomes.
     * 4. The teacher adds a demonstration and learner exercise.
     * 5. The teacher links assessment criteria.
     */
    public function test_teacher_defines_outcomes_demonstrations_exercises_and_criteria(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'trade_category_id' => $this->trade->id,
            'occupational_standard_id' => $this->standard->id,
            'title' => 'Inverter Battery Bank Balancing',
            'occupational_task' => 'Balance flooded lead-acid battery bank to 1.265 specific gravity.',
            'status' => 'draft',
            'version' => 1,
        ]);

        $response = $this->actingAs($this->teacherA)->put(route('pedagogy.lessons.update', $lesson->id), [
            'title' => 'Inverter Battery Bank Balancing',
            'occupational_task' => 'Balance flooded lead-acid battery bank to 1.265 specific gravity.',
            'trade_category_id' => $this->trade->id,
            'occupational_standard_id' => $this->standard->id,
            'duration_minutes' => 120,
            'target_level' => 'NSQF Level 3',
            'learning_outcomes' => [
                ['verb' => 'Measure', 'condition' => 'Given optical refractometer', 'standard' => 'Target 1.265 ±0.005 at 25°C', 'outcome_text' => 'Measure specific gravity accurately.'],
            ],
            'demonstration_plan' => [
                ['step' => 1, 'name' => 'Preparation & PPE', 'instructor_action' => 'Demonstrate donning acid-resistant apron and face shield.', 'safety_ppe' => 'Full chemical PPE', 'key_tolerance' => 'Zero skin contact'],
                ['step' => 2, 'name' => 'Specific Gravity Reading', 'instructor_action' => 'Draw electrolyte into refractometer and hold toward light.', 'safety_ppe' => 'Acid gloves', 'key_tolerance' => '1.265 ±0.005'],
            ],
            'guided_practice' => [
                ['step' => 1, 'activity' => 'Trainees test battery bank cell 1 in pairs.', 'check_point' => 'Instructor checks refractometer reading.', 'paired' => true],
            ],
            'independent_practice' => [
                ['activity' => 'Inspect and balance full 48V bank autonomously.', 'time_minutes' => 45, 'success_metric' => 'All cells equalized within ±0.010'],
            ],
            'assessment_criteria' => [
                ['criterion' => 'Adherence to acid handling and chemical PPE', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Mandatory'],
                ['criterion' => 'Hydrometer reading accuracy', 'evidence_type' => 'Gauge test', 'is_safety_critical' => false, 'passing_score' => '±0.005'],
            ],
            'status' => 'published',
        ]);

        $response->assertSessionHasNoErrors();
        $lesson->refresh();

        $this->assertEquals(2, $lesson->version);
        $this->assertTrue($lesson->hasDemonstrationPlan());
        $this->assertTrue($lesson->hasLearnerExercises());
        $this->assertTrue($lesson->hasMeasurableAssessmentCriteria());
        $this->assertTrue($lesson->isDay9Compliant());

        // Verify training progress updated
        $progress = PedagogyTrainingProgress::where('user_id', $this->teacherA->id)->first();
        $this->assertNotNull($progress);
        $this->assertTrue($progress->day9_completed);
    }

    /**
     * 6. The teacher opens the lesson in the Inclusive Lesson Designer.
     */
    public function test_teacher_can_open_lesson_in_inclusive_lesson_designer(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Electrical Conduit Bending',
            'occupational_task' => 'Bend 20mm PVC conduit with spring.',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->teacherA)->get(route('pedagogy.inclusive-designer.edit', $lesson->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Pedagogy/InclusiveDesigner')
            ->has('lesson')
            ->has('adaptation')
        );
    }

    /**
     * 7. The teacher creates differentiated activities.
     * 8. The teacher applies UDL and accessibility measures.
     * 9. The teacher saves and previews the adapted lesson.
     */
    public function test_teacher_creates_differentiated_activities_udl_and_previews(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Welding 3G Butt Joint',
            'occupational_task' => 'Execute vertical-up SMAW butt weld.',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->teacherA)->put(route('pedagogy.inclusive-designer.update', $lesson->id), [
            'structured_plan' => [
                ['phase' => 'Safety Briefing', 'duration_min' => 15, 'teacher_activity' => 'Review hot work', 'learner_activity' => 'Don PPE'],
                ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Model weave', 'learner_activity' => 'Observe on monitor'],
            ],
            'differentiated_activities' => [
                'beginner' => [['task' => 'Weld horizontal fillet tee joint (2F) with guide rest jig', 'support' => 'Bench jig support']],
                'intermediate' => [['task' => 'Weld 10mm butt joint in 3G vertical-up position']],
                'advanced' => [['task' => 'Weld 3G open root butt joint with backing bar removed', 'challenge' => 'Act as QA Inspector']],
            ],
            'udl_engagement' => ['Real-world structural bridge and pipeline demand.'],
            'udl_representation' => ['Magnified video feed of the weld puddle on screen.', 'Tactile weld samples.'],
            'udl_action_expression' => ['Practical coupon submission with guided root bend test.'],
            'learner_accommodations' => [
                ['need' => 'Asthma sensitivity', 'adaptation' => 'Powered air respirator PAPR', 'safety_rule_maintained' => 'Zero smoke exposure allowed; full PPE preserved'],
            ],
            'safety_standards_maintained' => true,
            'gender_responsive_strategies' => ['Flame-retardant welding jackets in female proportions.'],
            'workshop_rotation_schedule' => [
                'intervals_minutes' => 30,
                'stations' => [
                    ['station_id' => 1, 'name' => 'Edge Prep', 'equipment' => 'Grinders', 'group_capacity' => 3],
                    ['station_id' => 2, 'name' => 'Welding Booths', 'equipment' => 'Welders', 'group_capacity' => 4],
                ],
            ],
            'workshop_safety_arrangements' => ['Heavy-duty fire blanket and ABC extinguisher.'],
            'status' => 'draft',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('inclusive_lesson_adaptations', [
            'competency_lesson_id' => $lesson->id,
            'safety_standards_maintained' => true,
        ]);

        $adaptation = InclusiveLessonAdaptation::where('competency_lesson_id', $lesson->id)->first();
        $this->assertTrue($adaptation->hasDifferentiatedActivities());
        $this->assertTrue($adaptation->hasUdlAdaptations());
        $this->assertTrue($adaptation->isDay10Compliant());

        // Preview / Export view
        $exportResponse = $this->actingAs($this->teacherA)->get(route('pedagogy.lessons.export', $lesson->id));
        $exportResponse->assertStatus(200);
        $exportResponse->assertInertia(fn ($page) => $page
            ->component('Pedagogy/ExportLesson')
            ->has('lesson')
        );
    }

    /**
     * 10. The facilitator reviews and grades the work.
     */
    public function test_facilitator_can_review_and_grade_the_work(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Commercial Kitchen HACCP',
            'occupational_task' => 'Execute high-hygiene food production.',
            'status' => 'published',
        ]);

        $adaptation = InclusiveLessonAdaptation::create([
            'competency_lesson_id' => $lesson->id,
            'user_id' => $this->teacherA->id,
            'safety_standards_maintained' => true,
            'differentiated_activities' => [
                'beginner' => [['task' => 'Prep with safety guide']],
                'intermediate' => [['task' => 'Roast supreme with probe log']],
                'advanced' => [['task' => 'Manage buffet line CCP audit']],
            ],
            'udl_engagement' => ['Cultural Benue cuisine'],
            'udl_representation' => ['Pictorial HACCP cards'],
            'udl_action_expression' => ['Hands-on tasting panel'],
            'status' => 'submitted',
        ]);

        // Facilitator evaluates
        $response = $this->actingAs($this->facilitator)->post(route('facilitator.pedagogy-reviews.store', $lesson->id), [
            'module_type' => 'day10',
            'status' => 'approved',
            'rubric_scores' => [
                ['criterion' => 'Modeling clarity', 'score' => 20, 'max_score' => 20],
                ['criterion' => '3-tier differentiation', 'score' => 19, 'max_score' => 20],
                ['criterion' => 'UDL principles', 'score' => 19, 'max_score' => 20],
                ['criterion' => 'Safety preservation', 'score' => 20, 'max_score' => 20],
                ['criterion' => 'Rotation schedule', 'score' => 18, 'max_score' => 20],
            ],
            'total_score' => 96.00,
            'written_feedback' => 'Exceptional technical lesson adaptation with robust hygiene safeguards and realistic rotation intervals.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pedagogy_reviews', [
            'competency_lesson_id' => $lesson->id,
            'facilitator_id' => $this->facilitator->id,
            'module_type' => 'day10',
            'status' => 'approved',
            'total_score' => 96.00,
        ]);

        // Verify training progress reflects facilitator verification
        $progress = PedagogyTrainingProgress::where('user_id', $this->teacherA->id)->first();
        $this->assertNotNull($progress);
        $this->assertTrue($progress->day10_completed);
        $this->assertEquals($this->facilitator->id, $progress->day10_verified_by);
    }

    /**
     * 11. Both outputs appear in the teacher's portfolio.
     */
    public function test_both_outputs_appear_in_teachers_portfolio(): void
    {
        // 1. Day 9 Lesson output sync
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Automotive Brake Overhaul',
            'occupational_task' => 'Overhaul hydraulic floating caliper assembly.',
            'demonstration_plan' => [
                ['step' => 1, 'instructor_action' => 'Chassis lifting and jack stand placement with mechanical lock.'],
            ],
            'guided_practice' => [
                ['step' => 1, 'activity' => 'Measure disc runout with dial indicator.', 'paired' => true],
            ],
            'independent_practice' => [
                ['activity' => 'Overhaul caliper and bleed circuit.', 'time_minutes' => 60, 'success_metric' => 'Zero air bubbles'],
            ],
            'assessment_criteria' => [
                ['criterion' => 'Chassis jacking stand security', 'evidence_type' => 'Observation', 'is_safety_critical' => true, 'passing_score' => '100%'],
            ],
            'status' => 'draft',
            'version' => 1,
        ]);

        // Trigger Day 9 lesson update
        $this->actingAs($this->teacherA)->put(route('pedagogy.lessons.update', $lesson->id), [
            'title' => $lesson->title,
            'occupational_task' => $lesson->occupational_task,
            'demonstration_plan' => $lesson->demonstration_plan,
            'guided_practice' => $lesson->guided_practice,
            'independent_practice' => $lesson->independent_practice,
            'assessment_criteria' => $lesson->assessment_criteria,
            'status' => 'published',
        ]);

        // 2. Day 10 Inclusive Adaptation output sync
        $this->actingAs($this->teacherA)->put(route('pedagogy.inclusive-designer.update', $lesson->id), [
            'structured_plan' => [['phase' => 'Lifting', 'duration_min' => 20]],
            'differentiated_activities' => [
                'beginner' => [['task' => 'Bench disc measurement']],
                'intermediate' => [['task' => 'Full caliper overhaul']],
                'advanced' => [['task' => 'OBD-II ABS diagnostic bleed']],
            ],
            'udl_engagement' => ['Passenger road safety'],
            'udl_representation' => ['Color hydraulic flowcharts'],
            'udl_action_expression' => ['Hands-on servicing with fluid boiling test'],
            'safety_standards_maintained' => true,
            'status' => 'draft',
        ]);

        $portfolio = TeacherPortfolio::where('user_id', $this->teacherA->id)->first();
        $this->assertNotNull($portfolio);

        // Check both portfolio items exist
        $day9Item = $portfolio->items()->where('title', 'like', 'Day 9%')->first();
        $day10Item = $portfolio->items()->where('title', 'like', 'Day 10%')->first();

        $this->assertNotNull($day9Item, 'Day 9 output must be registered in the teacher portfolio.');
        $this->assertNotNull($day10Item, 'Day 10 output must be registered in the teacher portfolio.');
    }

    /**
     * 12. Another teacher cannot modify the lesson without permission.
     */
    public function test_another_teacher_cannot_modify_lesson_without_permission(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Haunched Mortise and Tenon Joint',
            'occupational_task' => 'Mark out, cut, fit haunched joint in hardwood.',
            'status' => 'published',
        ]);

        // Teacher B attempts to update Teacher A's lesson
        $response = $this->actingAs($this->teacherB)->put(route('pedagogy.lessons.update', $lesson->id), [
            'title' => 'Hacked Title By Teacher B',
            'occupational_task' => 'Unauthorized alteration',
            'status' => 'published',
        ]);

        // Policy denies with 403 Forbidden
        $response->assertStatus(403);

        $this->assertDatabaseMissing('competency_lessons', [
            'id' => $lesson->id,
            'title' => 'Hacked Title By Teacher B',
        ]);
    }

    /**
     * Non-negotiable safety enforcement test.
     */
    public function test_safety_standards_cannot_be_disabled_in_inclusive_designer(): void
    {
        $lesson = CompetencyLesson::create([
            'user_id' => $this->teacherA->id,
            'title' => 'Arc Welding Safety Check',
            'occupational_task' => 'SMAW joint fabrication.',
            'status' => 'published',
        ]);

        // Attempt to set safety_standards_maintained to false
        $response = $this->actingAs($this->teacherA)->put(route('pedagogy.inclusive-designer.update', $lesson->id), [
            'safety_standards_maintained' => false,
            'status' => 'draft',
        ]);

        // Controller rejects with validation error
        $response->assertSessionHasErrors('safety_standards_maintained');
    }

    /**
     * Duplicate lesson functionality.
     */
    public function test_teacher_can_duplicate_lesson_or_template(): void
    {
        $template = CompetencyLesson::firstOrCreate(
            ['is_template' => true],
            [
                'user_id' => $this->teacherA->id,
                'title' => 'Master Welding Template',
                'occupational_task' => 'Fabricate steel frame joint.',
                'status' => 'published',
            ]
        );

        $response = $this->actingAs($this->teacherA)->post(route('pedagogy.lessons.duplicate', $template->id));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('competency_lessons', [
            'user_id' => $this->teacherA->id,
            'title' => "Copy of {$template->title}",
            'is_template' => false,
        ]);
    }
}
