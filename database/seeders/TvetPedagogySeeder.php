<?php

namespace Database\Seeders;

use App\Models\CompetencyLesson;
use App\Models\InclusiveLessonAdaptation;
use App\Models\TradeCategory;
use App\Models\TvetOccupationalStandard;
use App\Models\User;
use Illuminate\Database\Seeder;

class TvetPedagogySeeder extends Seeder
{
    /**
     * Run the database seeds for TVET Pedagogy Standards and 8 Trade Templates.
     */
    public function run(): void
    {
        $superAdmin = User::role('super_admin')->first() ?? User::first();
        $adminId = $superAdmin ? $superAdmin->id : null;

        // 1. Approved Occupational Standards (NBTE Nigeria & Instructor-Created Examples)
        $standardsData = [
            [
                'slug' => 'solar-pv-installation-and-maintenance',
                'code' => 'NBTE/NOS/SLR-01/2023',
                'title' => 'Install, Test, and Commission Photovoltaic Systems',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 2.0',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Select and wear correct personal protective equipment (PPE: insulated gloves, safety boots, high-vis vest).', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Verify solar module open circuit voltage (Voc) and short circuit current (Isc) using calibrated multimeter within ±2% tolerance.', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Crimp MC4 solar connectors using calibrated ratcheting crimper ensuring zero copper strand shear and pull-test resistance > 310N.', 'is_critical_safety' => true],
                    ['order' => 4, 'criterion' => 'Wire DC combiner box with appropriate DC-rated circuit breakers (1000V DC) and surge protection device (SPD).', 'is_critical_safety' => true],
                    ['order' => 5, 'criterion' => 'Perform polarity verification before terminating DC leads into hybrid solar inverter.', 'is_critical_safety' => true],
                ],
            ],
            [
                'slug' => 'electrical-works',
                'code' => 'NBTE/NOS/ELE-02/2023',
                'title' => 'Domestic and Commercial Conduit Wiring and Distribution Board Installation',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 1.4',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Perform Lockout/Tagout (LOTO) and prove circuit dead using an approved voltage tester before commencing work.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Bend 20mm PVC conduit using spring bender to accurate 90° angles without bore distortion or heat bubbling.', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Route single-core PVC insulated copper conductors adhering to standard color coding (Brown/Live, Blue/Neutral, Green-Yellow/Earth).', 'is_critical_safety' => true],
                    ['order' => 4, 'criterion' => 'Terminate cables into residual current device (RCD) and miniature circuit breakers (MCBs) with manufacturer-specified torque (2.0 Nm).', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Conduct insulation resistance testing at 500V DC achieving > 1.0 MΩ reading between live conductors and earth.', 'is_critical_safety' => true],
                ],
            ],
            [
                'slug' => 'welding-and-fabrication',
                'code' => 'NBTE/NOS/WLD-03/2023',
                'title' => 'Shielded Metal Arc Welding (SMAW) 3G Vertical-Up Position Joint Fabrication',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 2.1',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Don welding helmet with shade 10-12 filter, flame-retardant leather apron, and gauntlet gloves.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Prepare 10mm structural steel plates with 30° bevel angle, 2mm root face, and 2.5mm root gap aligned with tack welds.', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Set welding current correctly for E7018 low-hydrogen electrodes (90A-110A DC+).', 'is_critical_safety' => false],
                    ['order' => 4, 'criterion' => 'Deposit root, hot, fill, and cap passes in vertical-up progression with complete penetration and zero undercut exceeding 0.5mm.', 'is_critical_safety' => true],
                    ['order' => 5, 'criterion' => 'Inspect finished weld visually for porosity, slag inclusions, and profile uniformity.', 'is_critical_safety' => false],
                ],
            ],
            [
                'slug' => 'agriculture',
                'code' => 'NBTE/NOS/AGR-04/2023',
                'title' => 'Micro-Irrigation Installation and Climate-Smart Vegetable Nursery Production',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 1.2',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Sanitize nursery trays and prepare potting substrate (coco peat, decomposed rice husk, sterilized topsoil 2:1:1).', 'is_critical_safety' => false],
                    ['order' => 2, 'criterion' => 'Wear chemical-resistant gloves, eye goggles, and respiratory mask during organic fungicide application.', 'is_critical_safety' => true],
                    ['order' => 3, 'criterion' => 'Sow hybrid tomato/pepper seeds at uniform depth (5mm) and maintain substrate moisture at 75% field capacity.', 'is_critical_safety' => false],
                    ['order' => 4, 'criterion' => 'Assemble drip manifold, screen filter (120 mesh), pressure regulator, and lateral drip lines with zero emitter clogs.', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Measure emitter flow rate uniformity coefficient achieving > 90% distribution efficiency across 50m beds.', 'is_critical_safety' => false],
                ],
            ],
            [
                'slug' => 'catering-services',
                'code' => 'NBTE/NOS/CAT-05/2023',
                'title' => 'Commercial Kitchen Sanitation, Food Safety (HACCP) and Culinary Production',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 2.0',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Adhere strictly to personal hygiene rules: clean chef uniform, hairnet, short nails, handwashing protocol.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Prevent cross-contamination by using color-coded chopping boards (Red: raw meat, Blue: seafood, Green: vegetables).', 'is_critical_safety' => true],
                    ['order' => 3, 'criterion' => 'Monitor and log critical food holding temperatures: cold storage (< 4°C) and hot holding (> 63°C).', 'is_critical_safety' => true],
                    ['order' => 4, 'criterion' => 'Execute classic knife cuts (julienne, brunoise, paysanne) with consistent dimensional precision.', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Calculate standard food portion yields and plating presentations adhering to nutritional balance standards.', 'is_critical_safety' => false],
                ],
            ],
            [
                'slug' => 'carpentry-and-joinery',
                'code' => 'NBTE/NOS/CRP-06/2023',
                'title' => 'Timber Joinery, Mortise-and-Tenon Framework and Roof Truss Assembly',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 1.8',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Verify machine guards, riving knife, and push-stick before operating circular saw or surface planer.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Mark out timber joints accurately using try square, marking gauge, and mortise gauge within ±0.5mm.', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Cut mortise and tenon joint with crisp shoulders, snug friction fit, and zero split grain.', 'is_critical_safety' => false],
                    ['order' => 4, 'criterion' => 'Apply PVA wood adhesive and clamp framework square checking diagonal measurements for parity.', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Finish timber surfaces through progressive sanding (grit 80 to 220) and apply eco-friendly wood preservative.', 'is_critical_safety' => false],
                ],
            ],
            [
                'slug' => 'automobile-repairs',
                'code' => 'NBTE/NOS/AUT-07/2023',
                'title' => 'Automotive Electronic Diagnostics and Hydraulic Brake System Overhaul',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 2.3',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Support vehicle on certified jack stands at rated chassis lifting points with wheel chocks secured.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Wear chemical safety goggles and nitrile gloves when inspecting and replacing DOT 4 brake fluid.', 'is_critical_safety' => true],
                    ['order' => 3, 'criterion' => 'Measure brake rotor thickness and runout using outside micrometer and dial indicator against OEM specs.', 'is_critical_safety' => false],
                    ['order' => 4, 'criterion' => 'Overhaul disc brake caliper, replacing dust boot, piston square-seal, and guide pin lubricant.', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Connect OBD-II diagnostic scanner, read live wheel speed sensor PID data, and clear ABS fault codes.', 'is_critical_safety' => false],
                ],
            ],
            [
                'slug' => 'garment-making',
                'code' => 'NBTE/NOS/GAR-08/2023',
                'title' => 'Pattern Drafting, Precision Cutting and Industrial Lockstitch Garment Construction',
                'source_authority' => 'NBTE (National Board for Technical Education) — National Occupational Standards Nigeria',
                'version' => '2023 / Rev 1.5',
                'is_official' => true,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Wear safety finger guard on high-speed industrial sewing machine and maintain upright ergonomic posture.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Draft basic bodice and sleeve sloper from client anatomical measurements with accurate seam allowances.', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Lay fabric on grain line, pin pattern pieces efficiently to optimize fabric utilization (> 85%), and cut cleanly.', 'is_critical_safety' => false],
                    ['order' => 4, 'criterion' => 'Set industrial lockstitch thread tension (needle and bobbin) producing balanced stitch (10-12 stitches/inch).', 'is_critical_safety' => false],
                    ['order' => 5, 'criterion' => 'Construct welt pocket or concealed zipper with flat seam pressing and zero puckering.', 'is_critical_safety' => false],
                ],
            ],
            // Instructor-Created Example standard to show pedagogical diversity
            [
                'slug' => 'solar-pv-installation-and-maintenance',
                'code' => 'IDEAS-HUB/BENUE/SLR-EXP-01',
                'title' => 'Off-Grid Inverter Battery Bank Balancing & Preventative Maintenance (Instructor Exemplar)',
                'source_authority' => 'Instructor-Created Teaching Standard Example (IDEAS-TVET Benue Hub)',
                'version' => '2024 / Pedagogical Example 1.0',
                'is_official' => false,
                'criteria' => [
                    ['order' => 1, 'criterion' => 'Wear acid-resistant apron, face shield, and rubber gloves before approaching flooded lead-acid battery banks.', 'is_critical_safety' => true],
                    ['order' => 2, 'criterion' => 'Check cell specific gravity using optical refractometer (target 1.265 at 25°C).', 'is_critical_safety' => false],
                    ['order' => 3, 'criterion' => 'Inspect battery interconnect cables and torque terminals to 11 Nm using insulated torque wrench.', 'is_critical_safety' => true],
                ],
            ],
        ];

        $standardModels = [];
        foreach ($standardsData as $data) {
            $trade = TradeCategory::where('slug', $data['slug'])->first();
            $standard = TvetOccupationalStandard::updateOrCreate(
                ['code' => $data['code']],
                [
                    'trade_category_id' => $trade ? $trade->id : null,
                    'title' => $data['title'],
                    'source_authority' => $data['source_authority'],
                    'version' => $data['version'],
                    'is_official' => $data['is_official'],
                    'performance_criteria' => $data['criteria'],
                    'created_by' => $adminId,
                    'is_active' => true,
                ]
            );
            $standardModels[$data['slug']] = $standard;
        }

        // 2. Comprehensive Competency-Based Lesson Templates for All 8 IDEAS-TVET Trades
        $templatesData = [
            // 1. Solar PV
            [
                'trade_slug' => 'solar-pv-installation-and-maintenance',
                'template_trade' => 'solar',
                'title' => 'Solar PV Array Combiner Box Termination and DC Disconnect Wiring',
                'occupational_task' => 'Terminate 4-string monocrystalline PV array into DC combiner box with 1000V DC surge protection and rotary isolator switch',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3 / Skilled Craftsperson',
                'learning_outcomes' => [
                    ['verb' => 'Identify', 'condition' => 'Given a wiring schematic and DC combiner enclosure', 'standard' => 'Correctly identify surge arresters, DC fuses, and busbar polarity with 100% accuracy', 'outcome_text' => 'Identify combiner box components and ensure polarity verification before conductor insertion.'],
                    ['verb' => 'Execute', 'condition' => 'Using calibrated ratcheting crimper and 4mm² solar cable', 'standard' => 'Crimp MC4 connectors satisfying pull-out tension test (> 310N) without bare copper strand exposure', 'outcome_text' => 'Fabricate reliable MC4 connections complying with IEC 62548 standards.'],
                    ['verb' => 'Diagnose', 'condition' => 'Using digital multimeter and clamp meter on an energized test array', 'standard' => 'Detect open-circuit voltage drop or reverse polarity within 5 minutes', 'outcome_text' => 'Diagnose and isolate string-level faults in a multi-string combiner box.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Safety Briefing & PPE Verification',
                        'instructor_action' => 'Demonstrate inspection of 1000V rated insulated hand tools, donning class 0 rubber gloves with leather protectors, and verifying zero open-circuit voltage on combiner terminal before touch.',
                        'safety_ppe' => 'Class 0 1000V insulated gloves, eye protection, safety boots, high-vis vest.',
                        'key_tolerance' => 'Zero bare skin contact with energized terminals. 100% adherence to LOTO.',
                        'media_resource' => 'Combiner Box Blueprint Diagram (A1 poster) + Physical 4-String Enclosure',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Cable Stripping & Ferrule Crimping',
                        'instructor_action' => 'Demonstrate stripping 7mm of UV-resistant double-insulated solar cable without nicking internal tinned copper strands. Insert into insulated bootlace ferrule and crimp using hex-die ratcheting tool.',
                        'safety_ppe' => 'Eye protection and cut-resistant gloves.',
                        'key_tolerance' => 'Stripping length 7mm ±0.5mm; 0 severed strands; ferrule flush with insulation collar.',
                        'media_resource' => 'Physical sample display showing correct crimp vs over-stripped failure.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Terminal Block Insertion & Torque Calibrated Tightening',
                        'instructor_action' => 'Insert positive and negative string conductors into designated fused disconnect holders. Demonstrate calibrated torque screwdriver set to 2.2 Nm until audible click.',
                        'safety_ppe' => 'Insulated screwdrivers (IEC 60900 compliant).',
                        'key_tolerance' => 'Torque: 2.2 Nm ±0.1 Nm. Cable tug test: no movement under 5kg manual pull.',
                        'media_resource' => 'Digital torque driver gauge display.',
                    ],
                    [
                        'step' => 4,
                        'name' => 'Surge Protection Device (SPD) Grounding & Polarity Check',
                        'instructor_action' => 'Connect 16mm² yellow/green earth bond conductor from combiner busbar to workshop earth ground rod. Measure earth impedance with ground clamp tester.',
                        'safety_ppe' => 'Protective safety goggles.',
                        'key_tolerance' => 'Earth loop impedance < 5.0 Ohms.',
                        'media_resource' => 'Video Micro-Demo: SPD operation under atmospheric overvoltage.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Learner pairs examine schematic and label conductor entry glands (String 1 to String 4 + / -).', 'check_point' => 'Instructor checks gland labeling before any cutting.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Each trainee strips and crimps 4 bootlace ferrules under instructor direct observation.', 'check_point' => 'Ferrule pull test conducted by instructor.', 'paired' => false],
                    ['step' => 3, 'activity' => 'Trainees terminate cables into DC fuse holders and torque to specification.', 'check_point' => 'Verification with torque indicator.', 'paired' => true],
                ],
                'independent_practice' => [
                    ['activity' => 'Complete full 4-string combiner box wiring from unstripped cables to final torque verification within 45 minutes.', 'time_minutes' => 45, 'success_metric' => 'Zero polarity errors, clean cable management, all terminals torqued to 2.2 Nm.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Solar Hybrid Clinic Power Pod Combiner Module',
                        'client_brief' => 'A primary healthcare clinic in Otukpo requires a weatherproof IP65 combiner box to aggregate 8kWp rooftop solar array with lightning protection.',
                        'deliverable' => 'Fully assembled, labeled, tested combiner box ready for field wall-mounting with QA sign-off sheet.',
                        'materials' => 'IP65 polycarbonate box, 1000V 15A gPV fuses, 2P 1000V DC SPD, 63A DC rotary switch, cable glands PG13.5.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Inverter displays Error 02 (DC Overvoltage/Reverse Polarity) upon closing combiner isolator.',
                        'diagnostic_procedure' => '1. Open main DC isolator. 2. Remove string fuses. 3. Test string voltage with multimeter red probe on positive and black on negative. 4. Identify string with negative voltage reading.',
                        'root_cause' => 'String 2 positive and negative cables cross-connected at roof junction.',
                        'rectification' => 'Tag, swap, and re-terminate String 2 leads. Retest Voc before closing disconnect switch.',
                        'safety_rule' => 'Never open DC fuses while under load; always open rotary isolator switch first to avoid DC electric arc flash.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Site deployment with Benue State Rural Electrification Agency contractor.',
                        'apprentice_role' => 'Junior Solar Technician responsible for combiner box fabrication and pre-commissioning testing.',
                        'logbook_signoff' => 'Combiner Pre-Commissioning Quality & Safety Checklist signed by Certified COREN Engineer.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Adherence to electrical safety & PPE', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Mandatory'],
                    ['criterion' => 'Conductor stripping and ferrule crimp quality', 'evidence_type' => 'Product inspection', 'is_safety_critical' => false, 'passing_score' => '≥ 85%'],
                    ['criterion' => 'Proper torque specification compliance', 'evidence_type' => 'Calibrated gauge test', 'is_safety_critical' => true, 'passing_score' => '2.2 Nm ±0.1 Nm'],
                    ['criterion' => 'Fault diagnosis speed and methodical procedure', 'evidence_type' => 'Practical fault test', 'is_safety_critical' => false, 'passing_score' => 'Resolved within 10 mins'],
                ],
                // Day 10 Inclusive Adaptation data
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Safety Briefing & Motivation', 'duration_min' => 15, 'teacher_activity' => 'Review LOTO, arc flash hazards, show finished clinic installation photo', 'learner_activity' => 'Inspect personal PPE, repeat 3 safety non-negotiables'],
                        ['phase' => 'Instructor 4-Step Demonstration', 'duration_min' => 25, 'teacher_activity' => 'Demonstrate stripping, crimping, torqueing at normal pace then step-by-step with magnified camera feed', 'learner_activity' => 'Follow pictorial job sheet, take notes, ask clarifying questions'],
                        ['phase' => 'Guided Scaffolded Practice', 'duration_min' => 40, 'teacher_activity' => 'Circulate through paired benches, verify ferrule crimps with pull gauge', 'learner_activity' => 'Execute paired crimping and gland installation with buddy cross-checking'],
                        ['phase' => 'Differentiated Practical Lab', 'duration_min' => 75, 'teacher_activity' => 'Assign tiered challenge briefs, provide assistive jigs, coach advanced troubleshooting', 'learner_activity' => 'Execute assigned tier task independently or in lead/apprentice role'],
                        ['phase' => 'Tool Return, Clean-up & Debrief', 'duration_min' => 25, 'teacher_activity' => 'Verify inventory board, facilitate reflective feedback and celebrate female apprentice achievements', 'learner_activity' => 'Clean workstation, return insulated tools, submit self-assessment ticket'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [
                            ['task' => 'Fabricate single-string DC disconnect box using color-coded tactile guide and step-by-step pictorial card', 'support' => 'Paired with intermediate peer; wire lengths pre-measured; torque tool pre-set.'],
                        ],
                        'intermediate' => [
                            ['task' => 'Wire complete 4-string combiner box adhering to blueprint schematic within 50 minutes with zero supervision.'],
                        ],
                        'advanced' => [
                            ['task' => 'Wire 4-string combiner box with integrated remote string monitoring shunt, plus diagnose an injected blind reverse-polarity fault.', 'challenge' => 'Act as Quality Assurance Lead checking beginner installations for terminal torque.'],
                        ],
                    ],
                    'udl_engagement' => [
                        'Highlight direct livelihood link to rural clinic solar electrification contracts in Benue.',
                        'Offer learners choice between wall-mount or rack-mount combiner enclosure configurations.',
                        'Gamify terminal torque verification with zero-defect quality badges.',
                    ],
                    'udl_representation' => [
                        'Dual-coded pictorial schematic with high-contrast color badges and tactile embossed terminal numbers.',
                        'High-definition video loop projected at workshop station showing close-up ferrule crimp technique.',
                        'Bilingual technical glossaries (English & Tiv/Idoma vernacular technical analogies for electric current and voltage).',
                    ],
                    'udl_action_expression' => [
                        'Trainees demonstrate competence through physical hands-on termination and verbal walk-through.',
                        'Option to capture digital video explanation of safety checklist using workshop tablet.',
                        'Annotated digital photo logbook submission for portfolio evidence.',
                    ],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with mild motor tremor / reduced grip strength', 'adaptation' => 'Provide ratcheting pliers with extended ergonomic lever handles and benchtop clamping jig.', 'safety_rule_maintained' => 'Full torque spec (2.2 Nm) and electrical safety isolation are strictly preserved without compromise.'],
                        ['need' => 'Trainee with hearing impairment', 'adaptation' => 'Visual LED indicator light on torque screwdriver when target torque is reached; high-vis signal flags for workshop drills.', 'safety_rule_maintained' => 'Auditory sirens augmented with bright strobe lights.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => [
                        'Actively assign female trainees as Lead Commissioning Engineers and Quality Inspectors during group rotations.',
                        'Provide ergonomic hand tools suited for diverse hand sizes (e.g. lightweight composite crimpers).',
                        'Enforce zero-tolerance workshop code of conduct regarding gender stereotyping.',
                    ],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 35,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Cable Prep & Stripping Station', 'equipment' => 'Automatic strippers, cable cutters, ferrule crimpers', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Combiner Enclosure Assembly Station', 'equipment' => 'DIN rail cutters, drill press, screwdriver set, torque driver', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Electrical Termination & Torque Station', 'equipment' => '1000V insulated toolkits, terminal blocks, DC fuses', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Testing, Commissioning & QA Audit Station', 'equipment' => 'Digital multimeters, insulation tester (Megger), earth clamp', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => [
                        'Master emergency power shut-off button clearly marked and unobstructed.',
                        'CO2 fire extinguisher and sand bucket situated within 5 meters of test array.',
                        'Mandatory buddy check verification before closing any DC isolator.',
                    ],
                ],
            ],

            // 2. Electrical Works
            [
                'trade_slug' => 'electrical-works',
                'template_trade' => 'electrical',
                'title' => 'Surface Conduit Bending, Saddle Fixing and Single-Phase Consumer Unit Wiring',
                'occupational_task' => 'Install 20mm PVC surface conduit run with 90° bends and wire 4-way consumer unit with RCD and MCBs',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3',
                'learning_outcomes' => [
                    ['verb' => 'Perform', 'condition' => 'Using internal bending spring and 20mm heavy-gauge PVC conduit', 'standard' => 'Produce 90° bend and double set (offset) without wrinkling or throat collapse', 'outcome_text' => 'Fabricate accurate conduit bends adhering to minimum bend radius regulations.'],
                    ['verb' => 'Install', 'condition' => 'On masonry test board using spirit level and rawl plugs', 'standard' => 'Fix conduit saddles at maximum 800mm intervals with horizontal/vertical alignment within 2mm', 'outcome_text' => 'Secure conduit runs and consumer unit enclosures rigidly.'],
                    ['verb' => 'Test', 'condition' => 'Using insulation resistance tester and continuity tester', 'standard' => 'Achieve > 1.0 MΩ insulation resistance and < 0.5 Ω earth continuity', 'outcome_text' => 'Conduct regulatory dead testing prior to circuit energization.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Conduit Bending Demonstration',
                        'instructor_action' => 'Demonstrate inserting internal bending spring with draw wire into 20mm conduit. Form progressive bend over knee applying steady friction heat.',
                        'safety_ppe' => 'Eye protection, leather work gloves, steel-toe boots.',
                        'key_tolerance' => 'Bend angle exactly 90° checked with try square; internal diameter reduction < 5%.',
                        'media_resource' => 'Conduit fabrication guide poster.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Saddle Fixing & Spirit Level Alignment',
                        'instructor_action' => 'Demonstrate chalk line marking, hammer drill pilot hole boring, inserting plastic anchors, and fastening saddle clamps.',
                        'safety_ppe' => 'Hearing protection, dust mask, safety goggles.',
                        'key_tolerance' => 'Saddle spacing 600mm-800mm; plumb deviation < 1mm per meter.',
                        'media_resource' => 'Laser level projection demonstration.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Cable Drawing & Consumer Unit Termination',
                        'instructor_action' => 'Pull 2.5mm² live, neutral, and 1.5mm² CPC through conduit using nylon draw tape. Terminate to main 63A RCD and 20A radial MCB.',
                        'safety_ppe' => 'Safety shoes, insulated pliers.',
                        'key_tolerance' => 'Conductor copper cleanly seated in terminal cage with zero stray strands outside.',
                        'media_resource' => 'Cutaway consumer unit display model.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees practice bending cold conduit test pieces using springs.', 'check_point' => 'Instructor measures angle and throat integrity.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees draw conductors through completed conduit runs in pairs.', 'check_point' => 'Check for cable sheath friction damage.', 'paired' => true],
                ],
                'independent_practice' => [
                    ['activity' => 'Construct two-way lighting circuit in conduit and terminate into distribution board.', 'time_minutes' => 60, 'success_metric' => 'Circuits fully functional, tests pass regulatory thresholds.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Vocational Workshop Machinery Sub-Panel Installation',
                        'client_brief' => 'Install surface conduit sub-main feeder from workshop distribution board to new wood lathe station.',
                        'deliverable' => 'Certified sub-panel with emergency mushroom stop switch and circuit chart.',
                        'materials' => '20mm conduit, saddles, 3x4mm² PVC singles, 32A TP&N isolator.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'RCD trips instantly upon raising radial socket circuit breaker.',
                        'diagnostic_procedure' => '1. Disconnect all loads. 2. Isolate circuit. 3. Test insulation resistance between neutral and earth using 500V DC tester. 4. Identify clamped/nicked neutral in conduit saddle.',
                        'root_cause' => 'Saddle mounting screw pierced neutral conductor sheath inside conduit.',
                        'rectification' => 'Withdraw damaged conductor, deburr conduit interior, draw new cable, re-test.',
                        'safety_rule' => 'Always verify circuit is electrically dead before unscrewing accessory plates.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Apprenticeship placement on Makurdi modern market shop rewiring project.',
                        'apprentice_role' => 'Assistant Electrician installing surface containment and wiring sub-meters.',
                        'logbook_signoff' => 'NEMSA (Nigerian Electricity Management Services Agency) inspection log.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Lockout/Tagout & Safe Isolation Protocol', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Pass'],
                    ['criterion' => 'Conduit bend accuracy (90° and sets)', 'evidence_type' => 'Gauge inspection', 'is_safety_critical' => false, 'passing_score' => '±2° tolerance'],
                    ['criterion' => 'Dead test readings (Continuity & Insulation)', 'evidence_type' => 'Instrument reading', 'is_safety_critical' => true, 'passing_score' => '> 1.0 MΩ'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Safety Briefing & LOTO', 'duration_min' => 15, 'teacher_activity' => 'Review safe isolation and shock prevention', 'learner_activity' => 'Verify multi-meter calibration on proving unit'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate conduit bending over knee with spring', 'learner_activity' => 'Mirror technique with practice off-cuts'],
                        ['phase' => 'Workshop Practical Rotation', 'duration_min' => 80, 'teacher_activity' => 'Facilitate rotation across bending, fixing, wiring, and testing stations', 'learner_activity' => 'Rotate through tasks in allocated teams'],
                        ['phase' => 'Debrief & Clean-up', 'duration_min' => 25, 'teacher_activity' => 'Review dead test verification certificates', 'learner_activity' => 'File results in learner logbook'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Bend straight conduit and single 90° elbow with pre-marked bending guide template', 'support' => 'Bench-mounted bending former and peer partner assistance.']],
                        'intermediate' => [['task' => 'Fabricate compound 90° bend with offset saddle pass-over accurately on wall board.']],
                        'advanced' => [['task' => 'Design and install 3-point conduit junction with two-way switching and intermediate cross-over switch.', 'challenge' => 'Act as Lead Inspector verifying insulation test readings for cohort.']],
                    ],
                    'udl_engagement' => ['Real-world market stall electrification design context.', 'Badge rewards for perfect right-angle conduit runs.'],
                    'udl_representation' => ['Tactile bending jig with color-coded angle notches.', 'Bilingual step cards in English and Hausa/Tiv.'],
                    'udl_action_expression' => ['Demonstrate through physical installation or digital interactive circuit schematic simulation.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with low vision', 'adaptation' => 'High-contrast yellow marking tape on spirit level bubble and magnified digital multimeter display.', 'safety_rule_maintained' => 'Proving dead with certified tester is mandatory.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Encourage balanced gender pairs during heavy conduit pulling.', 'Spotlight successful female electrical contractors.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Conduit Bending', 'equipment' => 'Bending springs, vices, hacksaws', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Channel & Board Fixing', 'equipment' => 'Hammer drills, levels, rawl plugs', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Cable Pulling & Termination', 'equipment' => 'Draw tape, cable drums, cutters, screwdrivers', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Dead Testing & Certification', 'equipment' => 'Insulation testers, continuity meters, proving units', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Main circuit breaker padlocked open during dead testing phase.', 'Rubber insulated floor mats at all electrical test boards.'],
                ],
            ],

            // 3. Welding and Fabrication
            [
                'trade_slug' => 'welding-and-fabrication',
                'template_trade' => 'welding',
                'title' => 'SMAW Butt Joint Preparation, Root Pass and Fill in 3G Vertical-Up Position',
                'occupational_task' => 'Weld 10mm low carbon steel plates in 3G vertical-up position using E7018 electrodes complying with visual acceptance standards',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3 / Certified Welder',
                'learning_outcomes' => [
                    ['verb' => 'Prepare', 'condition' => 'Using angle grinder with 6mm grinding disc', 'standard' => 'Bevel plate edges to 30° with 2mm root face and zero gouging', 'outcome_text' => 'Prepare weld joint geometry according to WPS (Welding Procedure Specification).'],
                    ['verb' => 'Execute', 'condition' => 'Using inverter arc welder set to 95A and E7018 electrode', 'standard' => 'Deposit root pass with continuous keyhole penetration and zero cold-lap', 'outcome_text' => 'Execute vertical-up root pass with complete joint penetration.'],
                    ['verb' => 'Inspect', 'condition' => 'Using welding fillet/butt gauge and magnifying lens', 'standard' => 'Assess cap pass width, reinforcement height (1-3mm), and absence of undercut', 'outcome_text' => 'Perform visual weld inspection according to ISO 5817 Level C.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'WPS Review & Joint Beveling',
                        'instructor_action' => 'Review Welding Procedure Specification. Demonstrate safe grinder handling with full face shield to grind 30° bevels.',
                        'safety_ppe' => 'Full face grinding shield, ear defenders, leather welding jacket, heavy welding gloves.',
                        'key_tolerance' => 'Bevel angle: 30° ±2.5°; root face: 2mm ±0.5mm.',
                        'media_resource' => 'WPS chart and machined coupon specimens.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Tack Welding & Fit-up Alignment',
                        'instructor_action' => 'Fit plates with 2.5mm spacer wire for root gap. Place 15mm bridge tacks at both ends. Deslag and inspect tacks.',
                        'safety_ppe' => 'Auto-darkening welding helmet (shade 11).',
                        'key_tolerance' => 'Root gap: 2.5mm ±0.5mm; plate misalignment < 1.0mm.',
                        'media_resource' => 'Fit-up inspection gauge.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Vertical-Up Root Pass Execution',
                        'instructor_action' => 'Demonstrate striking arc, establishing molten pool, maintaining slight weaving action across root face while observing keyhole.',
                        'safety_ppe' => 'Welding respirator, leather sleeves and spats.',
                        'key_tolerance' => 'Electrode travel angle: 5°-10° push angle; arc length: 2-3mm.',
                        'media_resource' => 'Video Micro-Demo: Keyhole formation under high-speed welding camera.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees grind and tack their test coupon plates under instructor supervision.', 'check_point' => 'Instructor checks root gap with feeler gauge.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees run simulated dry run without arc to practice body posture and travel speed.', 'check_point' => 'Instructor checks elbow positioning.', 'paired' => false],
                ],
                'independent_practice' => [
                    ['activity' => 'Weld complete 3G joint (root, fill, cap) on 10mm steel coupon within 60 minutes.', 'time_minutes' => 60, 'success_metric' => 'Pass visual inspection and 180° guided root bend test.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Agricultural Tractor Trailer Chassis H-Frame Bracket',
                        'client_brief' => 'Fabricate heavy-duty towing hitch bracket assembly for local cassava farmer cooperative.',
                        'deliverable' => 'Fully welded structural steel bracket with certified visual inspection report.',
                        'materials' => '10mm mild steel plate, E7018 electrodes, primer coating.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Excessive undercut exceeding 1.0mm along toe of weld cap in vertical-up run.',
                        'diagnostic_procedure' => '1. Clean weld bead with chipping hammer and wire brush. 2. Measure depth of groove at weld toe with weld depth gauge. 3. Assess welder travel speed and arc voltage.',
                        'root_cause' => 'Welding current too high and failure to pause at side walls during weave progression.',
                        'rectification' => 'Reduce current from 115A to 95A. Instruct welder to pause 1 second at each side wall to allow filler metal fill.',
                        'safety_rule' => 'Never weld on sealed containers or without active fume extraction in operation.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Apprenticeship at Makurdi River Basin Structural Engineering Yard.',
                        'apprentice_role' => 'Structural Welder Trainee working under AWS D1.1 certified supervisor.',
                        'logbook_signoff' => 'Welder Qualification Test Record (WQTR) logbook entry.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'PPE, Ventilation and Hot Work Safety Compliance', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Pass'],
                    ['criterion' => 'Root pass penetration and absence of burn-through', 'evidence_type' => 'Visual inspection', 'is_safety_critical' => true, 'passing_score' => 'Continuous root'],
                    ['criterion' => 'Weld profile uniformity and reinforcement height', 'evidence_type' => 'Gauge measurement', 'is_safety_critical' => false, 'passing_score' => '1mm - 3mm max'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Hot Work Safety & WPS Briefing', 'duration_min' => 15, 'teacher_activity' => 'Review hot work permits, fire extinguisher checks, fume extraction', 'learner_activity' => 'Don welding gear, check auto-darkening battery'],
                        ['phase' => 'Modeling & Arc Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate vertical weave with helmet camera link to screen', 'learner_activity' => 'Observe puddle dynamics on monitor'],
                        ['phase' => 'Hands-On Welding Lab', 'duration_min' => 85, 'teacher_activity' => 'Individual coaching in welding booths, adjust current settings', 'learner_activity' => 'Weld test coupons, deslag and self-inspect with gauge'],
                        ['phase' => 'Destructive Bend Test & Debrief', 'duration_min' => 25, 'teacher_activity' => 'Operate hydraulic bend tester on learner coupons', 'learner_activity' => 'Observe grain flow and evaluate ductile performance'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Weld horizontal fillet tee joint (2F) on 6mm plate to develop arc length control', 'support' => 'Electrode guide rest jig to stabilize hand movement.']],
                        'intermediate' => [['task' => 'Weld 10mm butt joint in 3G vertical-up position with 3-pass sequence.']],
                        'advanced' => [['task' => 'Weld 3G open root butt joint with backing bar removed, plus perform dye-penetrant NDT test on peers’ welds.', 'challenge' => 'Act as Quality Control Inspector documenting defects.']],
                    ],
                    'udl_engagement' => ['Highlight high commercial demand for certified 3G/4G pipeline and structural welders in Nigeria.'],
                    'udl_representation' => ['Magnified real-time video feed of the weld puddle projected on external monitor.', 'Tactile weld profile specimens showing acceptable vs rejected beads.'],
                    'udl_action_expression' => ['Demonstrate through practical weld coupon or recorded video commentary of visual inspection.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with respiratory sensitivity / mild asthma', 'adaptation' => 'Equip with powered air-purifying respirator (PAPR) with HEPA filtration; assign booth closest to high-velocity extraction duct.', 'safety_rule_maintained' => 'Zero smoke exposure allowed; full PPE required.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Ensure flame-retardant welding jackets in female proportions (sizes XS to L).', 'Celebrate women in welding role models.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Edge Prep & Grinding', 'equipment' => 'Bench grinders, angle grinders, bevel guides', 'group_capacity' => 3],
                            ['station_id' => 2, 'name' => 'Welding Booths 1-4', 'equipment' => 'Inverter welders, fume extractors, chipping hammers', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Visual Inspection & Gauging', 'equipment' => 'Bridge cam gauges, magnifying lights, calipers', 'group_capacity' => 3],
                            ['station_id' => 4, 'name' => 'Hydraulic Bend Testing', 'equipment' => 'Coupon cutter, hydraulic press, polishing wheel', 'group_capacity' => 2],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Heavy-duty fire blanket and ABC dry powder extinguisher at each welding bay.', 'Non-flammable welding curtains enclosing each booth.'],
                ],
            ],

            // 4. Agriculture
            [
                'trade_slug' => 'agriculture',
                'template_trade' => 'agriculture',
                'title' => 'Drip Irrigation Manifold Setup, Flow Uniformity Calibration and Fertigation Dosing',
                'occupational_task' => 'Assemble drip micro-irrigation system with venturi injector and calibrate emitter discharge uniformity across 0.5 hectare vegetable plot',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3',
                'learning_outcomes' => [
                    ['verb' => 'Assemble', 'condition' => 'Using 32mm HDPE main line, punch tool, and 16mm drip lateral lines with integrated emitters', 'standard' => 'Install 6 lateral runs with leak-free compression fittings and end-plugs', 'outcome_text' => 'Assemble pressurized micro-irrigation network.'],
                    ['verb' => 'Calibrate', 'condition' => 'Using catch-cans, measuring cylinder, and stopwatch across 12 sampling points', 'standard' => 'Calculate Christiansen Uniformity Coefficient (Cu) achieving > 88%', 'outcome_text' => 'Evaluate distribution uniformity of irrigation water.'],
                    ['verb' => 'Calculate', 'condition' => 'Given soil moisture deficit and crop water requirement for Solanaceae family', 'standard' => 'Determine correct operating pump runtime (minutes) and soluble NPK dosage', 'outcome_text' => 'Formulate precision fertigation schedule.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Filter & Venturi Injector Installation',
                        'instructor_action' => 'Demonstrate assembling disc screen filter (120 mesh) with pressure gauges on inlet and outlet. Install bypass venturi fertigation unit.',
                        'safety_ppe' => 'Rubber work boots, nitrile gloves, sun protection.',
                        'key_tolerance' => 'Pressure differential across clean filter < 0.2 bar.',
                        'media_resource' => 'Micro-irrigation headworks schematic diagram.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Punching & Lateral Line Laying',
                        'instructor_action' => 'Demonstrate specialized hole punch tool to bore clean 16mm takeoff holes into 32mm HDPE header without ragged edges. Insert grommets and start connectors.',
                        'safety_ppe' => 'Work gloves.',
                        'key_tolerance' => 'Hole angle exactly 90° perpendicular; grommet seal watertight under 1.5 bar.',
                        'media_resource' => 'Physical samples of good vs leaking takeoff fittings.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Catch-Can Flow Uniformity Test',
                        'instructor_action' => 'Place catch-cans beneath emitters at head, middle, and tail of 3 lateral lines. Run system for 15 minutes. Measure volume in graduated cylinder.',
                        'safety_ppe' => 'Safety glasses.',
                        'key_tolerance' => 'Emitter flow rate: 2.0 L/hr ±0.15 L/hr.',
                        'media_resource' => 'Flow calibration calculation spreadsheet/board.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees lay out drip laterals on raised nursery beds and punch takeoffs.', 'check_point' => 'Instructor checks punch tool perpendicularity.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Run water through lines to flush debris before plugging ends.', 'check_point' => 'Confirm all sediment flushed.', 'paired' => true],
                ],
                'independent_practice' => [
                    ['activity' => 'Conduct 12-point catch-can audit, measure volumes, and calculate distribution uniformity.', 'time_minutes' => 45, 'success_metric' => 'Uniformity coefficient Cu > 88%.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Community High-Tunnel Tomato Drip Installation',
                        'client_brief' => 'Design and install automated drip irrigation for a 200m² greenhouse producing export-grade tomatoes in Makurdi.',
                        'deliverable' => 'Functional irrigation headworks with automated battery timer, calibrated fertigation injector, and operating logbook.',
                        'materials' => 'Solar DC surface pump, 500L tank, 32mm HDPE pipe, 16mm inline drip tape, disc filter.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Emitters along the last 15 meters of lateral 4 show zero water discharge while head emitters flow normally.',
                        'diagnostic_procedure' => '1. Inspect pressure gauge at headworks (1.5 bar). 2. Check lateral for kinking. 3. Open lateral end-cap into bucket to inspect for silt/algae clogs.',
                        'root_cause' => 'Algae and fine silt bypass due to damaged filter o-ring, clogging low-pressure end emitters.',
                        'rectification' => 'Replace filter element o-ring, backwash filter, flush lateral with 0.5% organic acid solution, reinstall end-cap.',
                        'safety_rule' => 'Wear chemical goggles and rubber apron when handling acid wash or fertilizer concentrates.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Field operation at Benue Agricultural Development Corporation demonstration farm.',
                        'apprentice_role' => 'Irrigation Agronomy Technician in charge of fertigation and water auditing.',
                        'logbook_signoff' => 'Commercial Farm Manager daily water application log.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Chemical Handling & Water Pressure Safety Protocol', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100%'],
                    ['criterion' => 'Takeoff punching and fitting connection integrity (zero leaks)', 'evidence_type' => 'Pressure test', 'is_safety_critical' => false, 'passing_score' => 'Zero leaks at 1.5 bar'],
                    ['criterion' => 'Accuracy of Flow Uniformity calculation (Cu %)', 'evidence_type' => 'Data sheet review', 'is_safety_critical' => false, 'passing_score' => 'Within ±2% of true value'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Field Safety & Water Scarcity Context', 'duration_min' => 15, 'teacher_activity' => 'Review climate-smart irrigation advantages, safety with pressurized fittings', 'learner_activity' => 'Examine drip components, identify filter types'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate pipe punching, grommet seating, and catch-can layout', 'learner_activity' => 'Practice punching technique on pipe scrap'],
                        ['phase' => 'Field Practical', 'duration_min' => 80, 'teacher_activity' => 'Supervise bed layout, monitor catch-can measurements', 'learner_activity' => 'Assemble lateral lines, run water, collect flow volumes'],
                        ['phase' => 'Data Analysis & Debrief', 'duration_min' => 25, 'teacher_activity' => 'Lead step-by-step formula walk-through on chalkboard and tablets', 'learner_activity' => 'Compute distribution efficiency in pairs'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Assemble single 10m drip lateral with pre-punched pipe and conduct 3-point flow check', 'support' => 'Visual catch-can volume chart with colored fill levels.']],
                        'intermediate' => [['task' => 'Punch and assemble 4 lateral lines, calculate Cu % across 8 catch-cans.']],
                        'advanced' => [['task' => 'Calibrate venturi injector suction rate with dye test, adjust injection ratio to match soil nitrate test.', 'challenge' => 'Troubleshoot simulated emitter blockage.']],
                    ],
                    'udl_engagement' => ['Connect lesson to food security and drought resilience in North-Central Nigeria.'],
                    'udl_representation' => ['Color-coded graduated cylinders with large embossed milliliter markings.'],
                    'udl_action_expression' => ['Submit paper calculation sheet or input data into simple smartphone irrigation calculator app.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with physical mobility challenge', 'adaptation' => 'Elevated tabletop raised bed demonstration tank; assigned data logging and pressure gauge monitoring role.', 'safety_rule_maintained' => 'Full chemical safety handling guidelines maintained.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Encourage women farmers as leaders in precision irrigation technology adoption.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Headworks & Filter Assembly', 'equipment' => 'Valves, disc filters, pressure gauges', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Pipe Laying & Punching', 'equipment' => 'HDPE pipes, punches, grommets, laterals', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Catch-Can Sampling', 'equipment' => 'Catch cans, stopwatches, graduated cylinders', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Fertigation Dosing Calculation', 'equipment' => 'Venturi injector, hydrometers, formula boards', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Eye wash station accessible near fertilizer mixing area.', 'Slip-resistant footwear enforced on wet field beds.'],
                ],
            ],

            // 5. Catering Services
            [
                'trade_slug' => 'catering-services',
                'template_trade' => 'catering',
                'title' => 'HACCP-Compliant Commercial Food Prep, Temperature Control and Balanced Plating',
                'occupational_task' => 'Execute high-hygiene food production of a three-course traditional and continental banquet menu with critical temperature logging',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3',
                'learning_outcomes' => [
                    ['verb' => 'Enforce', 'condition' => 'In a commercial teaching kitchen', 'standard' => 'Maintain 100% compliance with personal hygiene and color-coded cross-contamination prevention rules', 'outcome_text' => 'Enforce food safety and sanitation protocols.'],
                    ['verb' => 'Execute', 'condition' => 'Using French chef knife and sharpening steel', 'standard' => 'Perform precise knife cuts (julienne 2mmx2mmx40mm, brunoise 2mm cubes) with uniform sizing', 'outcome_text' => 'Execute professional culinary knife skills.'],
                    ['verb' => 'Monitor', 'condition' => 'Using calibrated probe thermometer', 'standard' => 'Verify poultry/meat internal cooking temp reaches ≥ 74°C and holding temp ≥ 63°C', 'outcome_text' => 'Log critical control point (CCP) temperatures.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Sanitation, Handwashing & Thermometer Calibration',
                        'instructor_action' => 'Demonstrate 6-step 20-second handwashing protocol. Calibrate digital probe thermometer using ice slurry (0.0°C ±0.5°C) and boiling water (100.0°C).',
                        'safety_ppe' => 'Clean chef uniform, apron, hairnet/chef hat, non-slip kitchen safety shoes.',
                        'key_tolerance' => 'Thermometer calibration within ±0.5°C.',
                        'media_resource' => 'HACCP 7-principles wall chart and calibration logbook.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Classical Knife Cuts & Vegetable Mise en Place',
                        'instructor_action' => 'Demonstrate claw grip technique to protect fingertips. Cut carrots and bell peppers into julienne and brunoise on green sanitized chopping board.',
                        'safety_ppe' => 'Cut-resistant safety glove on non-knife hand during initial practice.',
                        'key_tolerance' => 'Julienne cut dimensions: 2mm x 2mm x 40mm within 0.5mm variance.',
                        'media_resource' => 'Visual dimensional knife-cut template board.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Poultry Roasting & Critical Core Temperature Logging',
                        'instructor_action' => 'Insert sanitized probe thermometer into thickest part of chicken breast avoiding bone. Log temperature reaching 75°C for at least 15 seconds.',
                        'safety_ppe' => 'Oven mitts, heat-resistant apron.',
                        'key_tolerance' => 'Core temp ≥ 74°C. Zero pink meat juices.',
                        'media_resource' => 'Digital temperature logger display.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees calibrate their probe thermometers and log results.', 'check_point' => 'Instructor checks ice point reading.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees produce 50g of brunoise vegetables with claw grip technique.', 'check_point' => 'Instructor inspects uniformity with grading template.', 'paired' => false],
                ],
                'independent_practice' => [
                    ['activity' => 'Prepare, cook, and plate balanced meal with logged CCP temperatures within 75 minutes.', 'time_minutes' => 75, 'success_metric' => 'Food safety standards 100% met, taste and presentation rated ≥ 85%.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'State Banquet Luncheon Catering Service',
                        'client_brief' => 'Prepare and serve 50-cover VIP luncheon for visiting education commissioners featuring Benue indigenous ingredients (sesame/beniseed, yam, catfish).',
                        'deliverable' => 'Full production schedule, costed recipe cards, allergen matrix, and temperature logs.',
                        'materials' => 'Indigenous fresh produce, catering chafing dishes, probe thermometers, display platters.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Hot holding buffet soup temperature drops to 52°C after 45 minutes on service line.',
                        'diagnostic_procedure' => '1. Check water pan level in chafing dish. 2. Verify sterno burner output. 3. Assess time elapsed in temperature danger zone (5°C - 63°C).',
                        'root_cause' => 'Chafing dish sterno fuel extinguished due to air draft from ceiling fan.',
                        'rectification' => 'If in danger zone < 2 hours, rapid reheat on kitchen stove to ≥ 75°C before returning to replenished burner; if > 2 hours, discard immediately.',
                        'safety_rule' => 'Never leave perishable food in danger zone for more than 2 hours. Food safety takes precedence over cost.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Kitchen placement at Benue Hotels & Conference Center.',
                        'apprentice_role' => 'Commis Chef in hot kitchen section responsible for temperature logs and mise en place.',
                        'logbook_signoff' => 'Executive Chef daily hygiene and production signoff.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Personal Hygiene & Cross-Contamination Avoidance', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Mandatory'],
                    ['criterion' => 'Knife technique safety (claw grip, stable cutting board)', 'evidence_type' => 'Observation', 'is_safety_critical' => true, 'passing_score' => 'Zero blade accidents'],
                    ['criterion' => 'Cooking & Holding temperature accuracy (CCP log)', 'evidence_type' => 'Log audit', 'is_safety_critical' => true, 'passing_score' => '≥ 74°C cooking / ≥ 63°C holding'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Hygiene Inspection & Allergen Briefing', 'duration_min' => 15, 'teacher_activity' => 'Conduct uniform check, review allergen protocols (nuts, gluten, dairy)', 'learner_activity' => 'Handwash, log temperature probe check'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate knife skills and temperature probing at main station', 'learner_activity' => 'Follow along with chef notes, inspect cuts'],
                        ['phase' => 'Kitchen Practical Lab', 'duration_min' => 85, 'teacher_activity' => 'Monitor kitchen sections, check sanitation buckets', 'learner_activity' => 'Cook and plate assigned dishes, log CCP temperatures'],
                        ['phase' => 'Sensory Evaluation & Clean-down', 'duration_min' => 25, 'teacher_activity' => 'Conduct blind taste panel and review kitchen sanitation grade', 'learner_activity' => 'Evaluate peer dishes, scrub and sanitize surfaces'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Perform vegetable prep with knife safety guide block and prepare cold salad plate with vinaigrette', 'support' => 'Cut-resistant safety glove and pre-portioned ingredients.']],
                        'intermediate' => [['task' => 'Execute pan-seared chicken supreme with jus and roasted root vegetables with temperature log.']],
                        'advanced' => [['task' => 'Manage buffet station service timing, conduct CCP audit on 4 hot holding units, supervise junior apprentices.', 'challenge' => 'Act as Kitchen Quality Assessor.']],
                    ],
                    'udl_engagement' => ['Incorporate indigenous Benue culinary pride into contemporary menu styling.'],
                    'udl_representation' => ['Pictorial recipe cards with icon-based allergen flags and temperature targets.'],
                    'udl_action_expression' => ['Demonstrate through live plate tasting or digital food photography styling portfolio.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with severe food allergy (e.g. peanut/shellfish)', 'adaptation' => 'Dedicated allergen-free prep zone with segregated purple cutting board and utensils.', 'safety_rule_maintained' => 'Zero risk of anaphylaxis; food safety protocols maintained.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Promote equal access to culinary leadership and kitchen management roles regardless of gender.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Sanitation & Cold Larder', 'equipment' => 'Sinks, color-coded boards, scales, slicers', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Hot Line Range & Ovens', 'equipment' => 'Gas ranges, convection ovens, extraction hoods', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Temperature Probing & QA', 'equipment' => 'Digital thermometers, calibration logs, timers', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Plating & Service Station', 'equipment' => 'Chafing dishes, warmers, garnish tools', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Class K wet chemical fire extinguisher installed next to deep fryers and ranges.', 'Non-slip kitchen rubber floor grids throughout cook line.'],
                ],
            ],

            // 6. Carpentry and Joinery
            [
                'trade_slug' => 'carpentry-and-joinery',
                'template_trade' => 'carpentry',
                'title' => 'Haunched Mortise and Tenon Joint Fabrication and Timber Framing',
                'occupational_task' => 'Mark out, cut, fit, and glue a traditional haunched mortise-and-tenon joint in hardwood timber according to technical drawing',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3',
                'learning_outcomes' => [
                    ['verb' => 'Mark Out', 'condition' => 'Using marking gauge, mortise gauge, and try square on dressed timber stock', 'standard' => 'Lay out mortise and tenon dimensions within ±0.5mm with clear knifed lines', 'outcome_text' => 'Mark out joinery joints with geometric precision.'],
                    ['verb' => 'Chisel & Saw', 'condition' => 'Using tenon saw and bevel-edged wood chisels (12mm and 19mm)', 'standard' => 'Cut clean tenon cheeks and chop mortise with perpendicular sidewalls and zero blowout', 'outcome_text' => 'Execute hand tool wood joint machining.'],
                    ['verb' => 'Assemble', 'condition' => 'Using sash clamps, wood glue, and framing square', 'standard' => 'Assemble joint with tight shoulder fit (gap < 0.2mm) and square internal angles (90° ±0.5°)', 'outcome_text' => 'Clamp and assemble timber framework square.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Timber Face Side & Edge Selection & Marking',
                        'instructor_action' => 'Demonstrate selecting best faces for face side and face edge. Mark standard traditional carpenter face marks (pencil loop and caret). Set mortise gauge to 1/3 timber thickness.',
                        'safety_ppe' => 'Safety glasses, dust mask, safety boots.',
                        'key_tolerance' => 'Mortise width: exactly 1/3 of timber thickness (e.g. 15mm for 45mm stock).',
                        'media_resource' => 'Timber joint cutaway visual aid.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Tenon Sawing Technique',
                        'instructor_action' => 'Demonstrate clamping stock at 45° in bench vice. Saw down waste side of line, starting kerf with thumb guide. Saw tenon cheeks and shoulders.',
                        'safety_ppe' => 'Eye protection.',
                        'key_tolerance' => 'Saw cut split pencil line cleanly without touching finished shoulder.',
                        'media_resource' => 'Handsaw angle guide jig.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Mortise Chopping & Waste Removal',
                        'instructor_action' => 'Demonstrate chopping mortise starting from center working outward. Lever waste out with mortise chisel held perpendicular.',
                        'safety_ppe' => 'Hearing protection, safety goggles.',
                        'key_tolerance' => 'Mortise depth: 5mm deeper than tenon length for glue pocket.',
                        'media_resource' => 'Chisel sharpening and hollow ground bevel display.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees mark out joint on test hardwood blocks.', 'check_point' => 'Instructor verifies mortise gauge spurs before sawing.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees cut tenon cheeks under instructor supervision.', 'check_point' => 'Check kerf position on waste side.', 'paired' => false],
                ],
                'independent_practice' => [
                    ['activity' => 'Complete haunched mortise and tenon joint, clamp, and measure diagonal squareness within 60 minutes.', 'time_minutes' => 60, 'success_metric' => 'Gapless shoulder fit, internal angle 90° ±0.5°.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Solid Teak School Classroom Desk Frame',
                        'client_brief' => 'Construct heavy-duty dual-student classroom desk frame using sustainably harvested Benue teak and mortise-tenon joinery.',
                        'deliverable' => 'Rigid timber sub-frame ready for laminate top with smooth sanded finish.',
                        'materials' => 'Dressed teak timber 50x50mm, waterproof PVA adhesive, sanding paper.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Joint exhibits 1.5mm gap on one shoulder when dry fitted, pulling the frame out of square.',
                        'diagnostic_procedure' => '1. Disassemble joint. 2. Place try square on tenon shoulder. 3. Check for out-of-square shoulder saw cut.',
                        'root_cause' => 'Tenon shoulder was sawn at a slight angle rather than strictly perpendicular to face edge.',
                        'rectification' => 'Pare the high side of shoulder carefully with sharp wide chisel backed against a clamped square guide block.',
                        'safety_rule' => 'Always keep both hands behind the chisel cutting edge; never hold workpiece in palm while chiseling.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Apprenticeship at Modern Furniture Factory, Makurdi.',
                        'apprentice_role' => 'Joiner Apprentice fabricating custom hardwood door and window frames.',
                        'logbook_signoff' => 'Senior Joinery Foreman dimensional check signoff.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Chisel and Hand Tool Safety Practice (hands behind blade)', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100%'],
                    ['criterion' => 'Shoulder fit tight and gap-free (< 0.5mm)', 'evidence_type' => 'Feeler gauge', 'is_safety_critical' => false, 'passing_score' => '≤ 0.5mm gap'],
                    ['criterion' => 'Squareness of assembled joint (90° check)', 'evidence_type' => 'Try square test', 'is_safety_critical' => false, 'passing_score' => '90° ±0.5°'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Safety & Timber Anatomy Briefing', 'duration_min' => 15, 'teacher_activity' => 'Review chisel safety, grain direction, hand positions', 'learner_activity' => 'Inspect tools, verify chisel sharpness'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate marking out and tenon cutting', 'learner_activity' => 'Follow step-by-step job card'],
                        ['phase' => 'Bench Practical Lab', 'duration_min' => 85, 'teacher_activity' => 'Circulate benches, assist with clamping and chisel paring', 'learner_activity' => 'Mark, saw, chisel, and fit joint'],
                        ['phase' => 'Inspection & Clean-up', 'duration_min' => 25, 'teacher_activity' => 'Evaluate joints with feeler gauge, facilitate peer assessment', 'learner_activity' => 'Sweep wood shavings, oil chisels, return tools'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Cut through mortise-and-tenon on soft pine wood with pre-cut shoulder guide block', 'support' => 'Magnetic saw guide jig to ensure 90° square kerf.']],
                        'intermediate' => [['task' => 'Fabricate haunched mortise-and-tenon in hardwood without guides.']],
                        'advanced' => [['task' => 'Fabricate drawbored pinned mortise and tenon joint with wooden dowel doweling.', 'challenge' => 'Act as Quality Inspector.']],
                    ],
                    'udl_engagement' => ['Highlight heritage value of indigenous Benue timber crafts.'],
                    'udl_representation' => ['Embossed tactile marking gauges and color-coded measurement rulers.'],
                    'udl_action_expression' => ['Demonstrate through physical joint artifact or video step guide.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with short stature or wheel-assisted mobility', 'adaptation' => 'Height-adjustable workbench fitted with quick-release wood vice at 75cm working height.', 'safety_rule_maintained' => 'Zero compromise on protective footwear and tool safety.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Encourage women woodworkers; ensure lightweight, ergonomic joinery planes.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Stock Preparation & Marking', 'equipment' => 'Gauges, squares, marking knives', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Tenon Sawing Station', 'equipment' => 'Tenon saws, bench vices, magnetic guides', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Mortise Chopping Station', 'equipment' => 'Mortise chisels, wooden mallets, hold-downs', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Assembly & Clamping Station', 'equipment' => 'Sash clamps, try squares, PVA glue, feeler gauges', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Central dust extraction and air filtration active in joinery workshop.', 'Chisels stored in protective wooden racks with guards on blades.'],
                ],
            ],

            // 7. Automobile Repairs
            [
                'trade_slug' => 'automobile-repairs',
                'template_trade' => 'automobile',
                'title' => 'Hydraulic Disc Brake Caliper Overhaul, Pad Replacement and System Bleeding',
                'occupational_task' => 'Inspect, overhaul, replace brake pads, and bleed hydraulic brake circuit on a passenger vehicle according to OEM service manual',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3 / Certified Auto Mechanic',
                'learning_outcomes' => [
                    ['verb' => 'Inspect', 'condition' => 'Using vernier caliper, micrometer, and dial indicator', 'standard' => 'Measure brake disc thickness and lateral runout against OEM discard limit within 0.01mm', 'outcome_text' => 'Inspect braking components and determine serviceability.'],
                    ['verb' => 'Overhaul', 'condition' => 'Using caliper piston rewind tool and brake cleaner', 'standard' => 'Replace piston square seal, dust boot, and lubricate slide pins with high-temp silicone grease', 'outcome_text' => 'Overhaul hydraulic floating caliper assembly.'],
                    ['verb' => 'Bleed', 'condition' => 'Using pressure bleeder and DOT 4 brake fluid', 'standard' => 'Purge all air bubbles from circuit achieving firm brake pedal with zero spongy travel', 'outcome_text' => 'Bleed hydraulic brake lines following manufacturer sequence.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Vehicle Lifting, Chassis Support & Wheel Removal',
                        'instructor_action' => 'Demonstrate positioning hydraulic trolley jack on rated front chassis crossmember. Position two mechanical locking jack stands on pinch welds. Lower vehicle gently onto stands and shake vehicle to verify rigidity.',
                        'safety_ppe' => 'Impact-resistant safety goggles, nitrile mechanic gloves, steel-cap work boots.',
                        'key_tolerance' => '100% verified mechanical lock on jack stands before bodily undercarriage entry.',
                        'media_resource' => 'Chassis lifting points chart & safety warning poster.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Caliper Removal & Piston Seal Replacement',
                        'instructor_action' => 'Clamp flexible brake hose with rounded tool. Remove caliper slide pin bolts. Extract piston using low-pressure compressed air into wooden block. Replace square-cut rubber seal.',
                        'safety_ppe' => 'Chemical eye protection, gloves, shop apron.',
                        'key_tolerance' => 'Piston cylinder bore: zero score marks or corrosion pitting.',
                        'media_resource' => 'Cutaway brake caliper model.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Hydraulic Line Bleeding Sequence',
                        'instructor_action' => 'Fill master cylinder with fresh DOT 4 fluid. Connect clear bleed hose to furthest caliper bleeder valve submerged in fluid. Pump pedal or apply 1.0 bar pressure bleeder until clear bubble-free fluid flows.',
                        'safety_ppe' => 'Eye protection, fluid drain pan.',
                        'key_tolerance' => 'Zero air bubbles in discharge tube; pedal travel < 25mm to firm resistance.',
                        'media_resource' => 'Bleeding sequence schematic diagram.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees check vehicle jacking and place safety stands.', 'check_point' => 'Instructor physically verifies stand engagement.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees measure disc thickness and runout with dial gauge.', 'check_point' => 'Verify reading against OEM manual.', 'paired' => true],
                ],
                'independent_practice' => [
                    ['activity' => 'Overhaul brake caliper, install pads, bleed line, and verify pedal firmness within 60 minutes.', 'time_minutes' => 60, 'success_metric' => 'Zero fluid leaks, firm pedal, disc runout < 0.05mm.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Public Transport Minibus Pre-Inspection Brake Overhaul',
                        'client_brief' => 'Overhaul braking system on commercial Toyota HiAce commuter bus serving the Makurdi-Gboko route before annual road safety certification.',
                        'deliverable' => 'Certified brake overhaul worksheet with rotor thickness, pad thickness, and fluid boiling point test printout.',
                        'materials' => 'Brake pads, DOT 4 fluid, seal rebuild kit, brake cleaner spray.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Brake pedal feels soft and spongy after pad replacement; sinks slowly to floorboard under steady foot pressure.',
                        'diagnostic_procedure' => '1. Check master cylinder fluid level. 2. Inspect all 4 caliper bleed screws and hose joints for leaks. 3. Perform diagnostic check for internal master cylinder piston seal bypass.',
                        'root_cause' => 'Air trapped in secondary ABS hydraulic modulator circuit or master cylinder primary seal leak.',
                        'rectification' => 'Connect OBD-II scanner and execute automated ABS bleed routine, followed by two-person pressure bleed.',
                        'safety_rule' => 'Never release a vehicle for road test with an unverified or spongy brake pedal.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Automotive Technical Internship at Authorized Dealership Workshop.',
                        'apprentice_role' => 'Apprentice Service Technician conducting multi-point brake and steering inspections.',
                        'logbook_signoff' => 'Service Manager quality verification ticket.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Vehicle Jacking & Physical Stand Security (Chassis Safety)', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Mandatory'],
                    ['criterion' => 'Chemical handling of caustic brake fluid (PPE & spill control)', 'evidence_type' => 'Observation', 'is_safety_critical' => true, 'passing_score' => '100% Pass'],
                    ['criterion' => 'Brake disc runout and thickness measurement accuracy', 'evidence_type' => 'Micrometer reading', 'is_safety_critical' => false, 'passing_score' => 'Within ±0.01mm'],
                    ['criterion' => 'Brake pedal firmness & absence of hydraulic air bubbles', 'evidence_type' => 'Functional test', 'is_safety_critical' => true, 'passing_score' => 'Firm resistance'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Automotive Safety & Jacking Protocols', 'duration_min' => 15, 'teacher_activity' => 'Demonstrate jack stand safety, discuss brake fluid chemical hazards', 'learner_activity' => 'Inspect PPE, locate chassis lifting points'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate caliper overhaul and dial gauge runout measurement', 'learner_activity' => 'Follow OEM step sequence on tablets'],
                        ['phase' => 'Vehicle Bay Practical', 'duration_min' => 85, 'teacher_activity' => 'Coach groups across lifting, measurement, overhaul, and bleeding', 'learner_activity' => 'Perform brake servicing in bay teams'],
                        ['phase' => 'Road Safety Verification & Debrief', 'duration_min' => 25, 'teacher_activity' => 'Conduct brake pedal pressure test on all trainee vehicles', 'learner_activity' => 'Sign off pre-trip safety checklist'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Inspect brake pads and measure disc thickness using digital caliper on standalone bench trainer', 'support' => 'Bench-top brake cutaway model with pre-labeled bleed nipples.']],
                        'intermediate' => [['task' => 'Perform complete brake pad replacement and caliper overhaul on vehicle lifting bay.']],
                        'advanced' => [['task' => 'Connect OBD-II scanner, trigger ABS solenoid bleed sequence, diagnose uneven pad wear pattern.', 'challenge' => 'Act as Quality Assurance Lead.']],
                    ],
                    'udl_engagement' => ['Highlight direct passenger safety impact of professional automotive technicians.'],
                    'udl_representation' => ['Color-coded hydraulic flowcharts and digital OEM technical schematics.'],
                    'udl_action_expression' => ['Demonstrate through physical servicing or digital diagnostic scan log.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with color vision deficiency', 'adaptation' => 'Labeled text on brake fluid test strips rather than relying on color alone.', 'safety_rule_maintained' => 'Zero compromise on brake fluid boiling point standards.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Ensure welcoming, harassment-free automotive workshop bay environment for female mechanics.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Vehicle Lifting & Disc Measurement', 'equipment' => 'Jacks, stands, micrometers, dial gauges', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Caliper Disassembly & Seal Overhaul', 'equipment' => 'Rewind tools, seal picks, silicone grease', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Hydraulic Bleeding & Fluid Flush', 'equipment' => 'Pressure bleeder, DOT 4 fluid, catch jars', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Diagnostic Scanner & ABS Test', 'equipment' => 'OBD-II diagnostic tablets, battery chargers', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Emergency eyewash station located next to fluid storage.', 'Brake fluid spill containment kit and absorbent granules.'],
                ],
            ],

            // 8. Garment Making
            [
                'trade_slug' => 'garment-making',
                'template_trade' => 'garment',
                'title' => 'Pattern Drafting, Precision Cutting and Industrial Lockstitch Garment Construction',
                'occupational_task' => 'Draft personalized bodice block, layout pattern on fabric grainline, cut, and construct tailored shirt with concealed zipper and welt pocket',
                'duration_minutes' => 180,
                'target_level' => 'NSQF Level 3 / Skilled Fashion Designer',
                'learning_outcomes' => [
                    ['verb' => 'Draft', 'condition' => 'Using tailor measuring tape, L-square, and French curve on pattern paper', 'standard' => 'Draft front and back bodice pattern with accurate ease allowances (5cm chest ease)', 'outcome_text' => 'Draft tailored garment patterns from individual body measurements.'],
                    ['verb' => 'Cut', 'condition' => 'Using rotary fabric shears on flat cutting table', 'standard' => 'Align pattern along warp grainline with fabric utilization efficiency > 85%', 'outcome_text' => 'Lay and cut fabric with zero distortion or off-grain warp deviation.'],
                    ['verb' => 'Stitch', 'condition' => 'Using high-speed industrial lockstitch machine', 'standard' => 'Sew seams at uniform 12 stitches per inch with balanced thread tension and flat pressing', 'outcome_text' => 'Operate industrial sewing machinery achieving professional seam finishes.'],
                ],
                'demonstration_plan' => [
                    [
                        'step' => 1,
                        'name' => 'Body Measurement Taking & Pattern Drafting',
                        'instructor_action' => 'Demonstrate taking precise anatomical measurements (bust/chest, waist, nape-to-waist, armscye). Draft basic sloper on pattern card with seam allowance.',
                        'safety_ppe' => 'Comfortable ergonomic shoes, tape measure.',
                        'key_tolerance' => 'Pattern symmetry checked by folding along center front/back within 1mm.',
                        'media_resource' => 'Anatomical measurement chart & transparent grading rulers.',
                    ],
                    [
                        'step' => 2,
                        'name' => 'Fabric Lay, Grainline Alignment & Precision Cutting',
                        'instructor_action' => 'Demonstrate finding true fabric grain by pulling thread. Pin pattern pieces parallel to selvedge. Cut cleanly with sharp tailor shears resting blade on table.',
                        'safety_ppe' => 'Finger safety ring guard on fabric cutting table.',
                        'key_tolerance' => 'Grainline deviation < 1 degree; cutting exactly on perimeter line.',
                        'media_resource' => 'Fabric layout efficiency diagram.',
                    ],
                    [
                        'step' => 3,
                        'name' => 'Industrial Sewing Machine Threading & Tension Tuning',
                        'instructor_action' => 'Demonstrate threading industrial machine from thread stand through tension disks, take-up lever, to needle eye (left to right). Tune bobbin tension.',
                        'safety_ppe' => 'Hair tied back, no loose jewelry, safety needle guard installed.',
                        'key_tolerance' => 'Balanced stitch (lock centered inside fabric ply with zero looping).',
                        'media_resource' => 'Stitch tension defect troubleshooting board.',
                    ],
                ],
                'guided_practice' => [
                    ['step' => 1, 'activity' => 'Trainees draft half-scale bodice pattern block.', 'check_point' => 'Instructor checks armhole curve with French curve.', 'paired' => true],
                    ['step' => 2, 'activity' => 'Trainees thread industrial lockstitch machines and stitch sample swatches.', 'check_point' => 'Inspect seam tension under magnifying loupe.', 'paired' => false],
                ],
                'independent_practice' => [
                    ['activity' => 'Construct finished tailored shirt bodice with collar and set-in sleeve within 75 minutes.', 'time_minutes' => 75, 'success_metric' => 'Clean seams, flat pressing, accurate fit on mannequin.'],
                ],
                'project_based_tasks' => [
                    [
                        'project_name' => 'Modernized Benue Traditional Angya / A\'nger Fabric Collection',
                        'client_brief' => 'Create a contemporary 3-piece corporate collection integrating traditional handwoven black-and-white Tiv A\'nger textile.',
                        'deliverable' => 'Finished tailored jacket and trousers with cost breakdown and marketing specification sheet.',
                        'materials' => 'Traditional A\'nger handwoven fabric, cotton lining, fusible interfacing, zippers.',
                    ],
                ],
                'fault_diagnosis_scenarios' => [
                    [
                        'fault_symptom' => 'Seam puckering along lengthwise straight stitch seam; fabric ripples when pressed flat.',
                        'diagnostic_procedure' => '1. Inspect needle point for burrs. 2. Check upper thread tension dial. 3. Verify stitch length setting (too short causes needle perforation bunching).',
                        'root_cause' => 'Upper thread tension excessively tight and dull ballpoint needle used on woven cotton.',
                        'rectification' => 'Replace with sharp size 14/90 needle, loosen upper tension nut by half-turn, and increase stitch length to 2.5mm.',
                        'safety_rule' => 'Always turn off motor switch before changing machine needles or threading.',
                    ],
                ],
                'work_based_learning' => [
                    [
                        'industry_context' => 'Apprenticeship at Commercial Garment Manufacturing Hub, Makurdi.',
                        'apprentice_role' => 'Sample Maker and Industrial Machine Operator.',
                        'logbook_signoff' => 'Head Pattern Maker quality assurance stamp.',
                    ],
                ],
                'assessment_criteria' => [
                    ['criterion' => 'Machine Safety, Guard Usage & Hair/Jewelry Restraint', 'evidence_type' => 'Direct observation', 'is_safety_critical' => true, 'passing_score' => '100% Mandatory'],
                    ['criterion' => 'Fabric grainline alignment and pattern cutting accuracy', 'evidence_type' => 'Cut piece inspection', 'is_safety_critical' => false, 'passing_score' => '±1mm tolerance'],
                    ['criterion' => 'Stitch tension balance and uniform seam finish', 'evidence_type' => 'Sample inspection', 'is_safety_critical' => false, 'passing_score' => 'Zero puckering or loops'],
                ],
                'inclusive_adaptation' => [
                    'structured_plan' => [
                        ['phase' => 'Safety, Ergonomics & Design Motivation', 'duration_min' => 15, 'teacher_activity' => 'Review posture, needle safety, display contemporary cultural garments', 'learner_activity' => 'Set up workstation, adjust chair height'],
                        ['phase' => 'Demonstration', 'duration_min' => 30, 'teacher_activity' => 'Demonstrate pattern drafting and needle threading on overhead camera', 'learner_activity' => 'Follow along with pattern paper and rulers'],
                        ['phase' => 'Garment Construction Lab', 'duration_min' => 85, 'teacher_activity' => 'Assist with machine speed regulation, coach tricky seam joins', 'learner_activity' => 'Cut, pin, stitch, and press garment sections'],
                        ['phase' => 'Mannequin Fitting & Debrief', 'duration_min' => 25, 'teacher_activity' => 'Lead garment fit evaluation on dress forms with peer feedback', 'learner_activity' => 'Present finished garment piece on dress form'],
                    ],
                    'differentiated_activities' => [
                        'beginner' => [['task' => 'Stitch straight and curved seam samples with magnetic edge guide, construct tote bag with French seams', 'support' => 'Speed-limiting servo motor controller set to low RPM.']],
                        'intermediate' => [['task' => 'Draft and construct tailored bodice with concealed zipper and set-in collar.']],
                        'advanced' => [['task' => 'Draft complex raglan sleeve variation, sew welt pockets, conduct quality audit on production run.', 'challenge' => 'Act as Quality Assurance Lead.']],
                    ],
                    'udl_engagement' => ['Celebrate Benue cultural identity through modern fashion design integration.'],
                    'udl_representation' => ['Tactile step cards with fabric swatches illustrating each stage of seam construction.'],
                    'udl_action_expression' => ['Demonstrate through physical garment construction or digital fashion illustration sketch.'],
                    'learner_accommodations' => [
                        ['need' => 'Trainee with lower extremity mobility limitation', 'adaptation' => 'Convert industrial sewing machine to hand-operated push-button micro-switch instead of foot treadle.', 'safety_rule_maintained' => 'Zero compromise on finger guards and electrical earthing.'],
                    ],
                    'safety_standards_maintained' => true,
                    'gender_responsive_strategies' => ['Actively recruit and support male trainees in garment construction and fashion design.'],
                    'workshop_rotation_schedule' => [
                        'intervals_minutes' => 30,
                        'stations' => [
                            ['station_id' => 1, 'name' => 'Pattern Drafting & Grading', 'equipment' => 'Drafting tables, curves, tracing wheels', 'group_capacity' => 4],
                            ['station_id' => 2, 'name' => 'Fabric Laying & Shearing', 'equipment' => 'Cutting tables, shears, pattern weights', 'group_capacity' => 4],
                            ['station_id' => 3, 'name' => 'Industrial Machine Sewing', 'equipment' => 'Lockstitch machines, overlockers, edge guides', 'group_capacity' => 4],
                            ['station_id' => 4, 'name' => 'Steam Pressing & Fitting', 'equipment' => 'Vacuum steam irons, tailor hams, mannequins', 'group_capacity' => 4],
                        ],
                    ],
                    'workshop_safety_arrangements' => ['Emergency stop bars under industrial sewing tables.', 'Steam iron auto-shutoff safety sensors.'],
                ],
            ],
        ];

        foreach ($templatesData as $tpl) {
            $trade = TradeCategory::where('slug', $tpl['trade_slug'])->first();
            $standard = $standardModels[$tpl['trade_slug']] ?? null;

            $lesson = CompetencyLesson::updateOrCreate(
                [
                    'template_trade' => $tpl['template_trade'],
                    'is_template' => true,
                ],
                [
                    'user_id' => $adminId,
                    'trade_category_id' => $trade ? $trade->id : null,
                    'occupational_standard_id' => $standard ? $standard->id : null,
                    'title' => $tpl['title'],
                    'occupational_task' => $tpl['occupational_task'],
                    'duration_minutes' => $tpl['duration_minutes'],
                    'target_level' => $tpl['target_level'],
                    'learning_outcomes' => $tpl['learning_outcomes'],
                    'demonstration_plan' => $tpl['demonstration_plan'],
                    'guided_practice' => $tpl['guided_practice'],
                    'independent_practice' => $tpl['independent_practice'],
                    'project_based_tasks' => $tpl['project_based_tasks'],
                    'fault_diagnosis_scenarios' => $tpl['fault_diagnosis_scenarios'],
                    'work_based_learning' => $tpl['work_based_learning'],
                    'assessment_criteria' => $tpl['assessment_criteria'],
                    'status' => 'published',
                    'version' => 1,
                ]
            );

            // Create or update Inclusive Lesson Adaptation for template
            if (! empty($tpl['inclusive_adaptation'])) {
                $adData = $tpl['inclusive_adaptation'];
                InclusiveLessonAdaptation::updateOrCreate(
                    [
                        'competency_lesson_id' => $lesson->id,
                    ],
                    [
                        'user_id' => $adminId,
                        'structured_plan' => $adData['structured_plan'] ?? null,
                        'differentiated_activities' => $adData['differentiated_activities'] ?? null,
                        'udl_engagement' => $adData['udl_engagement'] ?? null,
                        'udl_representation' => $adData['udl_representation'] ?? null,
                        'udl_action_expression' => $adData['udl_action_expression'] ?? null,
                        'learner_accommodations' => $adData['learner_accommodations'] ?? null,
                        'safety_standards_maintained' => $adData['safety_standards_maintained'] ?? true,
                        'gender_responsive_strategies' => $adData['gender_responsive_strategies'] ?? null,
                        'workshop_rotation_schedule' => $adData['workshop_rotation_schedule'] ?? null,
                        'workshop_safety_arrangements' => $adData['workshop_safety_arrangements'] ?? null,
                        'status' => 'approved',
                    ]
                );
            }
        }
    }
}
