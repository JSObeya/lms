<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSection;
use App\Models\Day8PracticeProgress;
use App\Models\Grade;
use App\Models\Resource;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use App\Models\RubricLevel;
use App\Models\TradeCategory;
use App\Models\User;
use Database\Seeders\RoleAndCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $facilitator;

    protected User $teacherA;

    protected User $teacherB;

    protected User $student;

    protected TradeCategory $trade;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles, permissions and trade categories
        $this->seed(RoleAndCategorySeeder::class);

        $this->trade = TradeCategory::where('code', 'SPV')->first()
            ?? TradeCategory::create([
                'name' => 'Solar Photovoltaic (PV) Installation & Maintenance',
                'code' => 'SPV',
                'slug' => 'solar-pv-installation',
                'icon' => 'sun',
                'description' => 'Solar panel mounting, inverter setup, battery sizing, and TVET electrical safety.',
                'is_active' => true,
            ]);

        // Create Admin
        $this->admin = User::factory()->create([
            'email' => 'admin.test@gregicthub.com',
            'name' => 'LMS Administrator',
        ]);
        $this->admin->assignRole('super_admin');

        // Create Facilitator
        $this->facilitator = User::factory()->create([
            'email' => 'facilitator.test@gregicthub.com',
            'name' => 'TVET Master Facilitator',
        ]);
        $this->facilitator->assignRole('facilitator');

        // Create Teacher A
        $this->teacherA = User::factory()->create([
            'email' => 'teacher.a@gregicthub.com',
            'name' => 'Engr. Terver Paul',
            'trade_specialization' => 'Solar PV Installation',
            'institution' => 'Government Science & Technical College, Makurdi',
        ]);
        $this->teacherA->assignRole('teacher');

        // Create Teacher B
        $this->teacherB = User::factory()->create([
            'email' => 'teacher.b@gregicthub.com',
            'name' => 'Engr. Dooshima Akaa',
            'trade_specialization' => 'Electrical Installation & Maintenance',
            'institution' => 'Benue State Technical College, Gboko',
        ]);
        $this->teacherB->assignRole('teacher');

        // Create Student
        $this->student = User::factory()->create([
            'email' => 'student.test@gregicthub.com',
            'name' => 'Sunday Ochigbo',
        ]);
        $this->student->assignRole('student');
    }

    /**
     * 1. Admin can bulk import 200 TVET teachers via CSV
     */
    public function test_admin_can_bulk_import_teachers_via_csv(): void
    {
        Storage::fake('local');

        $csvContent = "First Name,Last Name,Email,Phone,Institution,State,Trade Specialization\n"
            ."Joseph,Aliyu,joseph.aliyu@test.com,08011112222,GSTC Makurdi,Benue,Solar PV\n"
            ."Mary,Ochoche,mary.ochoche@test.com,08033334444,STC Otukpo,Benue,Garment Making\n";

        $file = UploadedFile::fake()->createWithContent('tvet_teachers.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.users.bulk-import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'joseph.aliyu@test.com',
            'institution' => 'GSTC Makurdi',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'mary.ochoche@test.com',
            'trade_specialization' => 'Garment Making',
        ]);

        $importedTeacher = User::where('email', 'joseph.aliyu@test.com')->first();
        $this->assertTrue($importedTeacher->hasRole('teacher'));
    }

    /**
     * 2. Teacher authentication and dashboard isolation
     */
    public function test_teacher_authentication_and_dashboard_isolation(): void
    {
        // Teacher sees teacher dashboard
        $response = $this->actingAs($this->teacherA)->get(route('dashboard'));
        $response->assertStatus(200);

        // Student sees student dashboard
        $studentResponse = $this->actingAs($this->student)->get(route('dashboard'));
        $studentResponse->assertStatus(200);

        // Teacher cannot access admin user management
        $adminUsersResponse = $this->actingAs($this->teacherA)->get(route('admin.users.index'));
        $adminUsersResponse->assertStatus(403);

        // Student cannot access admin user management
        $studentAdminResponse = $this->actingAs($this->student)->get(route('admin.users.index'));
        $studentAdminResponse->assertStatus(403);
    }

    /**
     * 3. Teacher can create independent course
     */
    public function test_teacher_can_create_independent_course(): void
    {
        $response = $this->actingAs($this->teacherA)->post(route('courses.store'), [
            'trade_category_id' => $this->trade->id,
            'title' => 'Advanced Solar PV Inverter Sizing & Maintenance',
            'code' => 'TVET-SPV-301',
            'summary' => 'Hands-on practical training on pure sine wave inverters.',
            'description' => 'Comprehensive workshop on charge controllers and inverter wiring.',
            'difficulty_level' => 'intermediate',
            'duration' => '6 Weeks',
            'visibility' => 'public',
            'is_practice' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'code' => 'TVET-SPV-301',
            'user_id' => $this->teacherA->id,
            'trade_category_id' => $this->trade->id,
        ]);
    }

    /**
     * 4. Teacher can create section and measurable learning objective
     */
    public function test_teacher_can_create_section_and_measurable_objective(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV Safety and Wiring',
            'code' => 'TVET-SPV-102',
            'slug' => 'solar-pv-safety-wiring',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        // Create Section
        $sectionResponse = $this->actingAs($this->teacherA)->post(route('sections.store', $course), [
            'title' => 'Personal Protective Equipment & Electrical Hazard Isolation',
            'description' => 'Safety protocol prior to touching DC combiner boxes.',
        ]);
        $sectionResponse->assertRedirect();

        $section = CourseSection::where('course_id', $course->id)->first();
        $this->assertNotNull($section);

        // Create Measurable Learning Objective (ABCD)
        $objectiveResponse = $this->actingAs($this->teacherA)->post(route('objectives.store', $course), [
            'course_section_id' => $section->id,
            'action_verb' => 'inspect and isolate DC disconnect switches',
            'condition' => 'Given a 48V solar bench simulator and safety gloves',
            'performance_standard' => 'with 100% adherence to IEC 62548 electrical safety standards',
            'bloom_level' => 'apply',
        ]);
        $objectiveResponse->assertRedirect();

        $this->assertDatabaseHas('learning_objectives', [
            'course_id' => $course->id,
            'course_section_id' => $section->id,
            'action_verb' => 'inspect and isolate DC disconnect switches',
            'bloom_level' => 'apply',
        ]);
    }

    /**
     * 5. Teacher can attach PDF and URL resources with secure access control
     */
    public function test_teacher_can_attach_resources_and_access_is_controlled(): void
    {
        Storage::fake('local');

        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar System Inspection',
            'code' => 'TVET-SPV-103',
            'slug' => 'solar-system-inspection',
            'difficulty_level' => 'intermediate',
            'visibility' => 'private',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Tools & Schematics',
            'order_index' => 1,
            'is_published' => true,
        ]);

        // Attach URL Resource
        $urlResponse = $this->actingAs($this->teacherA)->post(route('resources.store', $course), [
            'course_section_id' => $section->id,
            'title' => 'Standard Solar Photovoltaic Handbook (NAPTIN)',
            'resource_type' => 'url',
            'external_url' => 'https://example.com/resources/solar-handbook.pdf',
        ]);
        $urlResponse->assertRedirect();

        $this->assertDatabaseHas('resources', [
            'course_id' => $course->id,
            'course_section_id' => $section->id,
            'title' => 'Standard Solar Photovoltaic Handbook (NAPTIN)',
            'resource_type' => 'url',
        ]);

        // Attach PDF File Resource
        $fakePdf = UploadedFile::fake()->create('solar_manual.pdf', 500, 'application/pdf');
        $fileResponse = $this->actingAs($this->teacherA)->post(route('resources.store', $course), [
            'course_section_id' => $section->id,
            'title' => 'Workshop Safety Manual PDF',
            'resource_type' => 'pdf',
            'file' => $fakePdf,
        ]);
        $fileResponse->assertRedirect();

        $pdfResource = Resource::where('course_id', $course->id)->where('resource_type', 'pdf')->first();
        $this->assertNotNull($pdfResource);

        // Teacher A can download it
        $downloadResponse = $this->actingAs($this->teacherA)->get(route('resources.download', $pdfResource));
        $downloadResponse->assertStatus(200);

        // Teacher B (unauthorized on private course) cannot download it
        $unauthorizedResponse = $this->actingAs($this->teacherB)->get(route('resources.download', $pdfResource));
        $unauthorizedResponse->assertStatus(403);
    }

    /**
     * 6. Teacher can create assignment with cut-off dates and submission rules
     */
    public function test_teacher_can_create_assignment(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar Battery Sizing',
            'code' => 'TVET-SPV-104',
            'slug' => 'solar-battery-sizing',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Battery Storage Calculation',
            'order_index' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->teacherA)->post(route('assignments.store', $course), [
            'course_section_id' => $section->id,
            'title' => 'Solar PV 5kVA Inverter System Sizing Task',
            'instructions' => 'Calculate battery amp-hour capacity for a 24-hour backup period.',
            'max_score' => 100,
            'submission_type' => 'file_and_text',
            'due_date' => now()->addDays(7)->toDateTimeString(),
            'cut_off_date' => now()->addDays(10)->toDateTimeString(),
            'timezone' => 'Africa/Lagos',
            'accepted_file_types' => '.pdf,.docx,.xlsx',
            'max_files' => 2,
            'max_file_size_mb' => 10,
            'late_submission_policy' => 'allowed',
            'status' => 'published',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'title' => 'Solar PV 5kVA Inverter System Sizing Task',
            'max_score' => 100,
            'late_submission_policy' => 'allowed',
        ]);
    }

    /**
     * 7. Teacher can create rubric with multi-criteria and levels
     */
    public function test_teacher_can_create_rubric(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV Practical Installation',
            'code' => 'TVET-SPV-105',
            'slug' => 'solar-pv-practical-installation',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $response = $this->actingAs($this->teacherA)->post(route('rubrics.store'), [
            'title' => 'Solar PV Technical Inspection Rubric',
            'description' => 'Comprehensive 100-point rubric for electrical safety and load sizing.',
            'course_id' => $course->id,
            'criteria' => [
                [
                    'title' => 'Safety Protocol & Hazard Identification',
                    'description' => 'PPE adherence and tool check',
                    'max_points' => 50,
                    'levels' => [
                        ['title' => 'Exemplary', 'descriptor' => 'Zero safety violations.', 'points' => 50],
                        ['title' => 'Proficient', 'descriptor' => 'Minor isolation delay.', 'points' => 40],
                        ['title' => 'Unsatisfactory', 'descriptor' => 'High electrical risk.', 'points' => 0],
                    ],
                ],
                [
                    'title' => 'Mathematical Sizing & Load Accuracy',
                    'description' => 'Accurate load computation',
                    'max_points' => 50,
                    'levels' => [
                        ['title' => 'Exemplary', 'descriptor' => 'Accurate load computation.', 'points' => 50],
                        ['title' => 'Developing', 'descriptor' => 'Minor rounding discrepancy.', 'points' => 30],
                        ['title' => 'Unsatisfactory', 'descriptor' => 'Infeasible calculation.', 'points' => 0],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('rubrics', [
            'title' => 'Solar PV Technical Inspection Rubric',
            'user_id' => $this->teacherA->id,
            'total_points' => 100,
        ]);

        $rubric = Rubric::where('title', 'Solar PV Technical Inspection Rubric')->first();
        $this->assertCount(2, $rubric->criteria);
    }

    /**
     * 8. Teacher can preview course as student
     */
    public function test_teacher_can_preview_course_as_student(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar Preview Course',
            'code' => 'TVET-SPV-PREVIEW',
            'slug' => 'solar-preview-course',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $response = $this->actingAs($this->teacherA)->get(route('courses.preview', $course));
        $response->assertStatus(200);
    }

    /**
     * 9. Teacher can generate isolated synthetic practice learner
     */
    public function test_teacher_can_generate_isolated_synthetic_practice_learner(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Sandbox Solar Course',
            'code' => 'TVET-SPV-SANDBOX',
            'slug' => 'sandbox-solar-course',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        // Teacher has progress record
        Day8PracticeProgress::create([
            'user_id' => $this->teacherA->id,
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($this->teacherA)->post(route('day8.generate-practice-learner'));
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'is_synthetic' => true,
            'sandbox_owner_id' => $this->teacherA->id,
        ]);

        $synthetic = User::where('sandbox_owner_id', $this->teacherA->id)->first();
        $this->assertNotNull($synthetic);
        $this->assertTrue($synthetic->hasRole('student'));

        // Enrolled in Teacher A's course
        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'user_id' => $synthetic->id,
        ]);
    }

    /**
     * 10 & 11 & 12 & 13. Synthetic student submission, rubric grading with 78/100 score, feedback & grade release
     */
    public function test_complete_rubric_grading_workflow_with_78_score(): void
    {
        // 1. Setup course, assignment & rubric
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV Component Installation',
            'code' => 'TVET-SPV-GRADE-01',
            'slug' => 'solar-pv-grade-01',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $rubric = Rubric::create([
            'user_id' => $this->teacherA->id,
            'course_id' => $course->id,
            'title' => 'Grading Rubric',
            'total_points' => 100,
            'is_active' => true,
            'version' => 1,
        ]);

        // 5 Criteria (totaling 100), graded to achieve exactly 78 / 100:
        // C1: 16/20, C2: 16/20, C3: 15/20, C4: 16/20, C5: 15/20 -> 16+16+15+16+15 = 78
        $crit1 = RubricCriterion::create(['rubric_id' => $rubric->id, 'title' => 'Safety & PPE Compliance', 'max_points' => 20, 'order_index' => 1]);
        $lvl1 = RubricLevel::create(['rubric_criterion_id' => $crit1->id, 'title' => 'Proficient', 'points' => 16, 'descriptor' => 'Compliant PPE', 'order_index' => 1]);

        $crit2 = RubricCriterion::create(['rubric_id' => $rubric->id, 'title' => 'Solar Panel Azimuth & Tilt Angle', 'max_points' => 20, 'order_index' => 2]);
        $lvl2 = RubricLevel::create(['rubric_criterion_id' => $crit2->id, 'title' => 'Proficient', 'points' => 16, 'descriptor' => 'Correct tilt 15 degrees', 'order_index' => 1]);

        $crit3 = RubricCriterion::create(['rubric_id' => $rubric->id, 'title' => 'Combiner Box & DC Fusing', 'max_points' => 20, 'order_index' => 3]);
        $lvl3 = RubricLevel::create(['rubric_criterion_id' => $crit3->id, 'title' => 'Satisfactory', 'points' => 15, 'descriptor' => 'Adequate fusing', 'order_index' => 1]);

        $crit4 = RubricCriterion::create(['rubric_id' => $rubric->id, 'title' => 'Cable Sizing & Crimping', 'max_points' => 20, 'order_index' => 4]);
        $lvl4 = RubricLevel::create(['rubric_criterion_id' => $crit4->id, 'title' => 'Proficient', 'points' => 16, 'descriptor' => 'Proper 25mm2 crimp', 'order_index' => 1]);

        $crit5 = RubricCriterion::create(['rubric_id' => $rubric->id, 'title' => 'System Commissioning Test', 'max_points' => 20, 'order_index' => 5]);
        $lvl5 = RubricLevel::create(['rubric_criterion_id' => $crit5->id, 'title' => 'Satisfactory', 'points' => 15, 'descriptor' => 'Voc verified', 'order_index' => 1]);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'rubric_id' => $rubric->id,
            'title' => 'Hands-on Solar PV Practical Installation Test',
            'instructions' => 'Complete the practical test on the workbench.',
            'max_score' => 100,
            'submission_type' => 'online_text',
            'accepted_file_types' => '.pdf',
            'max_files' => 1,
            'max_file_size_mb' => 5,
            'late_submission_policy' => 'allowed',
            'due_date' => now()->addDays(5),
            'status' => 'published',
        ]);

        // Synthetic learner enrolled
        $syntheticStudent = User::factory()->create([
            'email' => 'practice.learner@sandbox.local',
            'name' => 'Musa Audu (Practice Trainee)',
            'is_synthetic' => true,
            'sandbox_owner_id' => $this->teacherA->id,
        ]);
        $syntheticStudent->assignRole('student');

        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $syntheticStudent->id,
            'role' => 'student',
            'status' => 'active',
        ]);

        // Student submits
        $submissionResponse = $this->actingAs($syntheticStudent)->post(route('submissions.store', $assignment), [
            'online_text' => 'Solar PV 1kW string installation completed with multi-meter Voc reading of 88.5V DC.',
        ]);
        $submissionResponse->assertRedirect();

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', $syntheticStudent->id)
            ->first();
        $this->assertNotNull($submission);
        $this->assertEquals('submitted', $submission->status);

        // Teacher grades submission using rubric (totaling 78)
        $gradeResponse = $this->actingAs($this->teacherA)->post(route('submissions.grade-rubric', $submission), [
            'rubric_id' => $rubric->id,
            'scores' => [
                $crit1->id => ['level_id' => $lvl1->id, 'points' => 16, 'comment' => 'Great PPE compliance'],
                $crit2->id => ['level_id' => $lvl2->id, 'points' => 16, 'comment' => 'Accurate orientation'],
                $crit3->id => ['level_id' => $lvl3->id, 'points' => 15, 'comment' => 'Minor cable labeling gap'],
                $crit4->id => ['level_id' => $lvl4->id, 'points' => 16, 'comment' => 'Proper crimping'],
                $crit5->id => ['level_id' => $lvl5->id, 'points' => 15, 'comment' => 'Good commissioning test'],
            ],
            'general_feedback' => 'Commendable craftsmanship on PV string wiring and DC crimping. Pay closer attention to cable labeling in the combiner box.',
            'is_released' => false,
        ]);

        $gradeResponse->assertRedirect();

        // Verify calculated grade is precisely 78 / 100
        $grade = Grade::where('assignment_id', $assignment->id)->where('user_id', $syntheticStudent->id)->first();
        $this->assertNotNull($grade);
        $this->assertEquals(78.00, (float) $grade->obtained_score);
        $this->assertFalse((bool) $grade->is_released);

        // Teacher releases grade
        $releaseResponse = $this->actingAs($this->teacherA)->post(route('grades.toggle-release', $grade));
        $releaseResponse->assertRedirect();

        $grade->refresh();
        $this->assertTrue((bool) $grade->is_released);
    }

    /**
     * 14. Teacher can export CSV gradebook with CSV formula-injection protection
     */
    public function test_teacher_can_export_csv_gradebook_with_formula_injection_escaping(): void
    {
        $course = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Solar PV CSV Export Test',
            'code' => 'TVET-SPV-CSV',
            'slug' => 'solar-pv-csv',
            'difficulty_level' => 'intermediate',
            'visibility' => 'public',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Inspection Assignment',
            'instructions' => 'Inspection instructions',
            'max_score' => 100,
            'status' => 'published',
        ]);

        // Trainee with potentially dangerous formula injection in name
        $maliciousStudent = User::factory()->create([
            'name' => '=CMD|"/C calc"!A0',
            'email' => '+attacker@test.com',
            'is_synthetic' => true,
            'sandbox_owner_id' => $this->teacherA->id,
        ]);
        $maliciousStudent->assignRole('student');

        Grade::create([
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'user_id' => $maliciousStudent->id,
            'graded_by' => $this->teacherA->id,
            'max_score' => 100,
            'obtained_score' => 85,
            'percentage' => 85.00,
            'feedback' => '@SUM(1+1)*cmd',
            'is_released' => true,
        ]);

        $exportResponse = $this->actingAs($this->teacherA)->get(route('gradebook.export-csv', $course));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csvOutput = $exportResponse->streamedContent();

        // Ensure formula characters are escaped with leading single quote
        $this->assertStringContainsString("'=CMD|\"\"/C calc\"\"!A0", $csvOutput);
        $this->assertStringContainsString("'+attacker@test.com", $csvOutput);
        $this->assertStringContainsString("'@SUM(1+1)*cmd", $csvOutput);
    }

    /**
     * 15. Strict access isolation: Teacher B cannot view, edit, or tamper with Teacher A's course or grades
     */
    public function test_strict_access_isolation_between_teachers(): void
    {
        $courseA = Course::create([
            'trade_category_id' => $this->trade->id,
            'user_id' => $this->teacherA->id,
            'title' => 'Teacher A Private Course',
            'code' => 'TVET-SPV-A',
            'slug' => 'teacher-a-private-course',
            'difficulty_level' => 'intermediate',
            'visibility' => 'private',
            'status' => 'published',
            'is_practice' => true,
        ]);

        $assignmentA = Assignment::create([
            'course_id' => $courseA->id,
            'title' => 'Private Assignment A',
            'instructions' => 'Private task',
            'max_score' => 100,
            'status' => 'published',
        ]);

        // Teacher B attempts to view course builder of Teacher A -> 403
        $builderResponse = $this->actingAs($this->teacherB)->get(route('courses.builder', $courseA));
        $builderResponse->assertStatus(403);

        // Teacher B attempts to export gradebook of Teacher A -> 403
        $exportResponse = $this->actingAs($this->teacherB)->get(route('gradebook.export-csv', $courseA));
        $exportResponse->assertStatus(403);

        // Teacher B attempts to delete assignment of Teacher A -> 403
        $deleteResponse = $this->actingAs($this->teacherB)->delete(route('assignments.destroy', $assignmentA));
        $deleteResponse->assertStatus(403);
    }

    /**
     * 16. Facilitator can view and verify Day 8 training practice
     */
    public function test_facilitator_can_verify_day8_practice_completion(): void
    {
        // Teacher A creates progress record
        $progress = Day8PracticeProgress::create([
            'user_id' => $this->teacherA->id,
            'step_1_course_created' => true,
            'step_2_topic_labeled' => true,
            'step_3_objective_saved' => true,
            'step_4_resource_attached' => true,
            'step_5_assignment_created' => true,
            'step_6_deadline_configured' => true,
            'step_7_rubric_activated' => true,
            'step_8_preview_viewed' => true,
            'step_9_sample_graded' => true,
            'step_10_report_exported' => true,
        ]);

        $verifyResponse = $this->actingAs($this->facilitator)->post(route('day8.verify', $progress), [
            'feedback' => 'Excellent work on Solar PV course setup and rubric grading demonstration.',
            'is_approved' => true,
        ]);

        $verifyResponse->assertRedirect();

        $progress->refresh();
        $this->assertTrue((bool) $progress->is_verified_by_facilitator);
        $this->assertEquals($this->facilitator->id, $progress->verified_by);
        $this->assertNotNull($progress->completed_at);
    }

    /**
     * 17. Teacher portfolio creation across 9 TVET categories
     */
    public function test_teacher_portfolio_creation_and_facilitator_visibility(): void
    {
        $itemResponse = $this->actingAs($this->teacherA)->post(route('portfolio.items.store'), [
            'category' => 'lesson_documents',
            'title' => '12-Week Scheme of Work for Solar PV Installation',
            'description' => 'Comprehensive weekly breakdown aligned with National Skills Qualification Framework (NSQF).',
        ]);
        $itemResponse->assertRedirect();

        $this->assertDatabaseHas('portfolio_items', [
            'category' => 'lesson_documents',
            'title' => '12-Week Scheme of Work for Solar PV Installation',
        ]);
    }
}
