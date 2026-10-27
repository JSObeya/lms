<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSection;
use App\Models\Day8PracticeProgress;
use App\Models\Grade;
use App\Models\LearningObjective;
use App\Models\Lesson;
use App\Models\Resource;
use App\Models\Rubric;
use App\Models\RubricAssessment;
use App\Models\RubricAssessmentItem;
use App\Models\RubricCriterion;
use App\Models\RubricLevel;
use App\Models\TradeCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LmsDemonstrationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users
        $defaultPassword = Hash::make('Password123!');

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gregicthub.com'],
            [
                'name' => 'Greg Obeya (Lead Architect)',
                'first_name' => 'Greg',
                'last_name' => 'Obeya',
                'username' => 'gregadmin',
                'phone' => '+2348030000001',
                'institution' => 'Greg & Co. ICT Ltd',
                'state' => 'Benue',
                'trade_specialization' => 'Information & Communication Technology',
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // LMS Administrator
        $lmsAdmin = User::firstOrCreate(
            ['email' => 'lmsadmin@gregicthub.com'],
            [
                'name' => 'Kember Michael',
                'first_name' => 'Michael',
                'last_name' => 'Kember',
                'username' => 'mkember',
                'phone' => '+2348030000002',
                'institution' => 'Greg ICT Hub Makurdi',
                'state' => 'Benue',
                'trade_specialization' => 'General Technical Education',
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $lmsAdmin->syncRoles(['lms_admin']);

        // Facilitator / Assessor
        $facilitator = User::firstOrCreate(
            ['email' => 'facilitator@gregicthub.com'],
            [
                'name' => 'Engr. Patrick Agbo',
                'first_name' => 'Patrick',
                'last_name' => 'Agbo',
                'username' => 'pagbo',
                'phone' => '+2348030000003',
                'institution' => 'IDEAS TVET Master Trainer Board',
                'state' => 'Benue',
                'trade_specialization' => 'Solar PV Installation and Maintenance',
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $facilitator->syncRoles(['facilitator']);

        // Teacher (Primary Demonstration Trainee)
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@gregicthub.com'],
            [
                'name' => 'Engr. J.S. Obeya',
                'first_name' => 'John',
                'last_name' => 'Obeya',
                'username' => 'jsobeya',
                'phone' => '+2348030000004',
                'institution' => 'Government Technical College, Makurdi',
                'state' => 'Benue',
                'trade_specialization' => 'Solar PV Installation and Maintenance',
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $teacher->syncRoles(['teacher']);

        // Student (Institutional Learner)
        $student = User::firstOrCreate(
            ['email' => 'student@gregicthub.com'],
            [
                'name' => 'Aondover Terlumun',
                'first_name' => 'Aondover',
                'last_name' => 'Terlumun',
                'username' => 'terlumun',
                'phone' => '+2348030000005',
                'institution' => 'GTC Makurdi - TVET Trainee',
                'state' => 'Benue',
                'trade_specialization' => 'Solar PV Installation and Maintenance',
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $student->syncRoles(['student']);

        // Synthetic Practice Student (Sandboxed under Teacher)
        $practiceStudent = User::firstOrCreate(
            ['email' => 'practice.student1@sandbox.local'],
            [
                'name' => '[Practice Learner] Dooshima Iorwuese',
                'first_name' => 'Dooshima',
                'last_name' => 'Iorwuese',
                'username' => 'practice_learner_01',
                'phone' => null,
                'institution' => 'Sandbox Training Environment',
                'state' => 'Benue',
                'trade_specialization' => 'Solar PV Installation and Maintenance',
                'account_status' => 'active',
                'is_synthetic' => true,
                'sandbox_owner_id' => $teacher->id,
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );
        $practiceStudent->syncRoles(['student']);

        // 2. Primary Demonstration Course: Solar PV Installation — Practical Training
        $solarCategory = TradeCategory::where('slug', 'solar-pv-installation-and-maintenance')->first();

        $course = Course::firstOrCreate(
            ['code' => 'TVET-SPV-01'],
            [
                'title' => 'Solar PV Installation — Practical Training',
                'slug' => 'solar-pv-installation-practical-training',
                'trade_category_id' => $solarCategory->id,
                'user_id' => $teacher->id,
                'summary' => 'Comprehensive hands-on course covering solar PV fundamentals, component inspection, inverter sizing, and safety standards.',
                'description' => 'This course is tailored for IDEAS-TVET trainees to master solar photovoltaic installation, testing, and maintenance according to Nigerian industrial standards.',
                'duration' => '40 Hours / 4 Weeks',
                'difficulty_level' => 'intermediate',
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->addDays(25)->toDateString(),
                'enrollment_type' => 'open',
                'visibility' => 'public',
                'status' => 'published',
                'is_practice' => true, // Mode A Demonstration
            ]
        );

        // Enroll students
        CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $student->id],
            ['role' => 'student', 'status' => 'active', 'progress_percentage' => 45]
        );
        CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $practiceStudent->id],
            ['role' => 'student', 'status' => 'active', 'progress_percentage' => 70]
        );

        // 3. Topics / Sections
        $section1 = CourseSection::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Solar PV Fundamentals and Safety'],
            [
                'description' => 'Core concepts of photovoltaic energy conversion, major balance-of-system components, and mandatory workshop hazard controls.',
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        $section2 = CourseSection::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Array Mounting & DC Wiring Techniques'],
            [
                'description' => 'Roof mounts, grounding clamps, MC4 crimping, string calculation, and combiner box installation.',
                'order_index' => 2,
                'is_published' => true,
            ]
        );

        // 4. Lessons
        $lesson1 = Lesson::firstOrCreate(
            ['course_section_id' => $section1->id, 'title' => 'Component Identification and Safety Inspection'],
            [
                'course_id' => $course->id,
                'content' => 'Photovoltaic (PV) systems consist of solar modules, charge controllers, inverters, disconnect switches, and energy storage battery banks. In this lesson, technicians learn to visually inspect components for defects before installation.',
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        // 5. Measurable Learning Objective
        LearningObjective::firstOrCreate(
            ['course_id' => $course->id, 'complete_text' => 'Given a supervised PV training rig or approved component reference material, correctly identify at least eight of ten PV components and explain their functions.'],
            [
                'course_section_id' => $section1->id,
                'lesson_id' => $lesson1->id,
                'action_verb' => 'Identify & Explain',
                'condition' => 'Given a supervised PV training rig or approved component reference material',
                'performance_standard' => 'correctly identify at least 8 of 10 major components and explain their functions without critical safety errors',
                'bloom_level' => 'apply',
                'order_index' => 1,
            ]
        );

        // 6. Resources (PDF Reference and Educational URL)
        Resource::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Solar PV Components Technical Reference Manual'],
            [
                'course_section_id' => $section1->id,
                'lesson_id' => $lesson1->id,
                'resource_type' => 'pdf',
                'file_name' => 'solar_pv_component_guide.pdf',
                'file_path' => 'resources/solar_pv_component_guide.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 2450000,
                'is_downloadable' => true,
                'order_index' => 1,
                'description' => 'Comprehensive technical guide detailing module specs, charge controller MPPT algorithms, and inverter protection features.',
            ]
        );

        Resource::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Renewable Energy World — Solar PV Safety Guide'],
            [
                'course_section_id' => $section1->id,
                'lesson_id' => $lesson1->id,
                'resource_type' => 'url',
                'external_url' => 'https://www.energy.gov/eere/solar/solar-photovoltaic-technology-basics',
                'is_downloadable' => false,
                'order_index' => 2,
                'description' => 'Authoritative technical article explaining PV electrical isolation and DC safety protocols.',
            ]
        );

        // 7. Assessment Rubric
        $rubric = Rubric::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Solar PV Practical Assessment Rubric'],
            [
                'user_id' => $teacher->id,
                'description' => 'Standardized TVET assessment rubric for evaluating PV component identification, technical explanations, and safety compliance.',
                'total_points' => 100.00,
                'is_active' => true,
                'version' => 1,
            ]
        );

        // Rubric Criteria (Total: 100)
        // Criterion 1: Component identification (30)
        $crit1 = RubricCriterion::firstOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Component Identification'],
            ['description' => 'Ability to physically identify key components of the solar PV system.', 'max_points' => 30.00, 'order_index' => 1]
        );
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit1->id, 'title' => 'Exemplary'], ['descriptor' => 'Identifies 9-10 components correctly with zero hesitation.', 'points' => 30.00, 'order_index' => 1]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit1->id, 'title' => 'Proficient'], ['descriptor' => 'Identifies 7-8 components correctly.', 'points' => 24.00, 'order_index' => 2]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit1->id, 'title' => 'Developing'], ['descriptor' => 'Identifies 5-6 components correctly with guidance.', 'points' => 15.00, 'order_index' => 3]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit1->id, 'title' => 'Novice'], ['descriptor' => 'Identifies fewer than 5 components.', 'points' => 0.00, 'order_index' => 4]);

        // Criterion 2: Functional explanations (25)
        $crit2 = RubricCriterion::firstOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Functional Explanations'],
            ['description' => 'Clear technical explanation of each component purpose in the energy conversion loop.', 'max_points' => 25.00, 'order_index' => 2]
        );
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit2->id, 'title' => 'Exemplary'], ['descriptor' => 'Explains voltage regulation, MPPT, DC/AC inversion comprehensively.', 'points' => 25.00, 'order_index' => 1]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit2->id, 'title' => 'Proficient'], ['descriptor' => 'Explains general functions accurately with minor technical gaps.', 'points' => 20.00, 'order_index' => 2]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit2->id, 'title' => 'Developing'], ['descriptor' => 'Basic understanding with noticeable confusion on controller vs inverter.', 'points' => 12.00, 'order_index' => 3]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit2->id, 'title' => 'Novice'], ['descriptor' => 'Unable to state basic component functions.', 'points' => 0.00, 'order_index' => 4]);

        // Criterion 3: Safety hazard identification (20)
        $crit3 = RubricCriterion::firstOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Safety Hazard Identification'],
            ['description' => 'Spotting DC arcing risks, battery chemical hazards, and proper PPE selection.', 'max_points' => 20.00, 'order_index' => 3]
        );
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit3->id, 'title' => 'Exemplary'], ['descriptor' => 'All electrical and chemical hazards identified with mitigation steps.', 'points' => 20.00, 'order_index' => 1]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit3->id, 'title' => 'Proficient'], ['descriptor' => 'Primary hazards identified; slight omission in ventilation requirements.', 'points' => 16.00, 'order_index' => 2]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit3->id, 'title' => 'Developing'], ['descriptor' => 'Misses DC disconnect protocol.', 'points' => 8.00, 'order_index' => 3]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit3->id, 'title' => 'Novice'], ['descriptor' => 'Complete disregard of critical safety hazards.', 'points' => 0.00, 'order_index' => 4]);

        // Criterion 4: Practical inspection checklist (15)
        $crit4 = RubricCriterion::firstOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Practical Inspection Checklist'],
            ['description' => 'Meticulous completion of the 10-point workshop inspection protocol.', 'max_points' => 15.00, 'order_index' => 4]
        );
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit4->id, 'title' => 'Exemplary'], ['descriptor' => 'Checklist completed with accurate multimeter voltage readings.', 'points' => 15.00, 'order_index' => 1]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit4->id, 'title' => 'Proficient'], ['descriptor' => 'Completed with 1 minor data recording error.', 'points' => 11.00, 'order_index' => 2]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit4->id, 'title' => 'Developing'], ['descriptor' => 'Incomplete checklist entries.', 'points' => 6.00, 'order_index' => 3]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit4->id, 'title' => 'Novice'], ['descriptor' => 'Did not use checklist.', 'points' => 0.00, 'order_index' => 4]);

        // Criterion 5: Report presentation (10)
        $crit5 = RubricCriterion::firstOrCreate(
            ['rubric_id' => $rubric->id, 'title' => 'Report Presentation'],
            ['description' => 'Structure, technical vocabulary, clarity, and schematic diagrams.', 'max_points' => 10.00, 'order_index' => 5]
        );
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit5->id, 'title' => 'Exemplary'], ['descriptor' => 'Professional report with clean wiring sketches and terminology.', 'points' => 10.00, 'order_index' => 1]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit5->id, 'title' => 'Proficient'], ['descriptor' => 'Clear report with minor formatting flaws.', 'points' => 7.00, 'order_index' => 2]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit5->id, 'title' => 'Developing'], ['descriptor' => 'Disorganized findings.', 'points' => 4.00, 'order_index' => 3]);
        RubricLevel::firstOrCreate(['rubric_criterion_id' => $crit5->id, 'title' => 'Novice'], ['descriptor' => 'Unreadable or missing sections.', 'points' => 0.00, 'order_index' => 4]);

        // 8. Assignment: Solar PV Components and Safety Inspection
        $assignment = Assignment::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Solar PV Components and Safety Inspection'],
            [
                'course_section_id' => $section1->id,
                'lesson_id' => $lesson1->id,
                'rubric_id' => $rubric->id,
                'instructions' => 'Conduct a thorough inspection of the training rig. Document 10 key components, explain the function of the MPPT charge controller vs inverter, and complete the safety hazard checklist. Upload your report in PDF format or enter your findings online.',
                'max_score' => 100.00,
                'allow_submissions_from' => now()->subDays(3),
                'due_date' => now()->addDays(7),
                'cut_off_date' => now()->addDays(10),
                'timezone' => 'Africa/Lagos',
                'submission_type' => 'file_and_text',
                'accepted_file_types' => 'pdf,docx,jpg,png',
                'max_files' => 2,
                'max_file_size_mb' => 10,
                'late_submission_policy' => 'allowed',
                'status' => 'published',
            ]
        );

        // 9. Practice Submission by Synthetic Student
        $submission = AssignmentSubmission::firstOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $practiceStudent->id],
            [
                'attempt_number' => 1,
                'status' => 'graded',
                'online_text' => "SOLAR PV INSPECTION REPORT\nCandidate: Dooshima Iorwuese\n\n1. Component Identification:\n- Identified 8 components: Monocrystalline PV module (300W), MPPT Charge Controller (40A), Pure Sine Wave Inverter (1.5kVA), Deep-Cycle Gel Battery (200Ah), DC Breaker (63A), Surge Protection Device, Combiner Box, and MC4 Connectors.\n\n2. Functionality Analysis:\n- MPPT Controller: Tracks the maximum power point on the I-V curve to deliver optimum current to the battery bank.\n- Inverter: Inverts 24V DC to 230V AC pure sinusoidal waveform for consumer appliances.\n\n3. Safety Protocol:\n- PPE worn: Insulated gloves (1000V rated), steel-toe boots, eye protection.\n- DC disconnect switched OFF prior to testing battery terminal voltages.\n- Multimeter read 25.4V on battery string.",
                'submitted_at' => now()->subDay(),
                'is_late' => false,
                'grade_released' => true,
                'final_score' => 78.00, // 78/100 as required in Section 27!
                'feedback' => 'Commendable inspection report. Good understanding of MPPT tracking. Ensure you double-check polarity markings on the DC surge protector during future practicals.',
                'graded_by' => $teacher->id,
                'graded_at' => now()->subHours(2),
            ]
        );

        // 10. Rubric Assessment Breakdown (Total = 24 + 20 + 16 + 11 + 7 = 78 marks!)
        $assessment = RubricAssessment::firstOrCreate(
            ['assignment_submission_id' => $submission->id],
            [
                'rubric_id' => $rubric->id,
                'assessed_by' => $teacher->id,
                'total_calculated_score' => 78.00,
                'general_feedback' => 'Scored 78/100. Accurate component recognition and thorough safety precaution adherence.',
                'is_released' => true,
            ]
        );

        // Items per criterion
        RubricAssessmentItem::firstOrCreate(
            ['rubric_assessment_id' => $assessment->id, 'rubric_criterion_id' => $crit1->id],
            ['awarded_points' => 24.00, 'assessor_comment' => 'Identified 8 components accurately.']
        );
        RubricAssessmentItem::firstOrCreate(
            ['rubric_assessment_id' => $assessment->id, 'rubric_criterion_id' => $crit2->id],
            ['awarded_points' => 20.00, 'assessor_comment' => 'Clear explanation of charge controller and inverter operations.']
        );
        RubricAssessmentItem::firstOrCreate(
            ['rubric_assessment_id' => $assessment->id, 'rubric_criterion_id' => $crit3->id],
            ['awarded_points' => 16.00, 'assessor_comment' => 'High awareness of DC shock hazards and correct PPE usage.']
        );
        RubricAssessmentItem::firstOrCreate(
            ['rubric_assessment_id' => $assessment->id, 'rubric_criterion_id' => $crit4->id],
            ['awarded_points' => 11.00, 'assessor_comment' => 'Checklist completed with accurate multimeter reading.']
        );
        RubricAssessmentItem::firstOrCreate(
            ['rubric_assessment_id' => $assessment->id, 'rubric_criterion_id' => $crit5->id],
            ['awarded_points' => 7.00, 'assessor_comment' => 'Good technical presentation and neat diagrams.']
        );

        // 11. Final Grade in Gradebook
        Grade::firstOrCreate(
            ['course_id' => $course->id, 'assignment_id' => $assignment->id, 'user_id' => $practiceStudent->id],
            [
                'max_score' => 100.00,
                'obtained_score' => 78.00,
                'percentage' => 78.00,
                'is_override' => false,
                'is_released' => true,
                'feedback' => 'Rubric-assessed score: 78/100.',
                'graded_by' => $teacher->id,
                'graded_at' => now()->subHours(2),
            ]
        );

        // 12. Initialize Day 8 Practical Wizard Progress for Teacher
        Day8PracticeProgress::firstOrCreate(
            ['user_id' => $teacher->id],
            [
                'course_id' => $course->id,
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
                'is_verified_by_facilitator' => true,
                'verified_by' => $facilitator->id,
                'facilitator_feedback' => 'Outstanding completion of Day 8 course creation and assessment practicals. Measurable objective and rubric criteria are aligned with TVET benchmarks.',
                'completed_at' => now(),
            ]
        );

        // Seed 7 other trade course placeholders as per Section 27
        $otherTrades = [
            ['title' => 'Domestic & Industrial Electrical Works', 'code' => 'TVET-ELE-01', 'category' => 'electrical-works'],
            ['title' => 'Oxy-Acetylene & Arc Welding Techniques', 'code' => 'TVET-WLD-01', 'category' => 'welding-and-fabrication'],
            ['title' => 'Commercial Poultry & Crop Production', 'code' => 'TVET-AGR-01', 'category' => 'agriculture'],
            ['title' => 'Culinary Arts & Food Hygiene Management', 'code' => 'TVET-CAT-01', 'category' => 'catering-services'],
            ['title' => 'Timber Construction & Furniture Joinery', 'code' => 'TVET-CRP-01', 'category' => 'carpentry-and-joinery'],
            ['title' => 'Automotive Engine Diagnostics & Servicing', 'code' => 'TVET-AUT-01', 'category' => 'automobile-repairs'],
            ['title' => 'Industrial Tailoring & Pattern Drafting', 'code' => 'TVET-GAR-01', 'category' => 'garment-making'],
        ];

        foreach ($otherTrades as $trade) {
            $cat = TradeCategory::where('slug', $trade['category'])->first();
            if ($cat) {
                Course::firstOrCreate(
                    ['code' => $trade['code']],
                    [
                        'title' => $trade['title'],
                        'slug' => Str::slug($trade['title']),
                        'trade_category_id' => $cat->id,
                        'user_id' => $teacher->id,
                        'summary' => 'Comprehensive technical training module for '.$cat->name.'.',
                        'description' => 'Hands-on practical training aligned with national occupational standards.',
                        'duration' => '30 Hours',
                        'difficulty_level' => 'intermediate',
                        'status' => 'published',
                        'is_practice' => false,
                    ]
                );
            }
        }
    }
}
