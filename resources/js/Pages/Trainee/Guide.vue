<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import PageActivityIndicator from '@/Components/PageActivityIndicator.vue';
import {
    BookOpen,
    Eye,
    EyeOff,
    Sun,
    Moon,
    LayoutDashboard,
    FileCheck2,
    Award,
    HelpCircle,
    Search,
    CheckCircle2,
    Wand2,
    ArrowRight,
    ShieldCheck,
    Download,
    ExternalLink,
    ChevronRight,
    Briefcase,
    Sparkles,
    User,
    UserPlus,
    Users,
    Trash2,
    RotateCcw,
    AlertTriangle,
    Layers,
    Compass,
    Wrench,
    Settings2,
    FileText,
    Printer,
    GraduationCap,
    SlidersHorizontal,
    CheckSquare,
    Flame
} from 'lucide-vue-next';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const user = computed(() => page.props.auth?.user);
const userRole = computed(() => {
    if (!user.value) return 'guest';
    const roles = page.props.auth?.roles || [];
    return roles[0] || user.value.role || 'trainee';
});

const searchQuery = ref('');
const activeCategory = ref('all'); // all, admin, teacher, pedagogy, facilitator, trainee, security
const activeSectionId = ref('admin-users');

// Interactive demo state for trainees to test password visibility right in the guide!
const demoPassword = ref('TraineeSecret2026!');

const categories = [
    { id: 'all', label: 'All Guides', icon: BookOpen },
    { id: 'admin', label: 'Administrators', icon: ShieldCheck, role: 'admin' },
    { id: 'teacher', label: 'TVET Teachers', icon: Briefcase, role: 'teacher' },
    { id: 'pedagogy', label: 'Pedagogy Studio (Days 9-10)', icon: Sparkles, role: 'teacher' },
    { id: 'facilitator', label: 'Facilitators', icon: Award, role: 'facilitator' },
    { id: 'trainee', label: 'Trainees & Students', icon: GraduationCap, role: 'student' },
    { id: 'security', label: 'Security & FAQs', icon: HelpCircle }
];

const sections = [
    // ==========================================
    // 1. ADMINISTRATORS GUIDE
    // ==========================================
    {
        id: 'admin-users',
        category: 'admin',
        roleBadge: 'Administrator',
        badgeColor: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        icon: Users,
        title: 'User Administration, Inline Editing & Role Management',
        summary: 'Comprehensive management of student, teacher, facilitator, and admin accounts, soft delete safeguards, and bulk role conversions.',
        quickLink: { name: 'Go to User Administration', route: 'admin.users.index' },
        steps: [
            {
                heading: 'Step 1: Navigating to User Administration',
                description: 'Log in with Administrator credentials and select "Users" from the top navigation bar or navigate to /admin/users. The system displays all registered users with real-time status counters.'
            },
            {
                heading: 'Step 2: Role and Status Filtering',
                description: 'Use the quick role pills (All, Super Admin, Admin, Facilitator, Teacher, Student) and the Trashed filter dropdown ("Active Only", "Trashed Only", "All Users") to isolate specific user cohorts.'
            },
            {
                heading: 'Step 3: Quick Search & Flexible Pagination',
                description: 'Type any trainee or teacher name, email address, phone number, or technical college name into the search bar. Adjust page length between 15, 25, 50, and 100 rows per page.'
            },
            {
                heading: 'Step 4: Inline Name Editing & Edit User Modal',
                description: 'Click directly on any user\'s name in the table to open the Edit User modal. You can update their full name, email, role, phone number, institution (e.g. GSTC Makurdi), trade specialization, and active/disabled account status.'
            },
            {
                heading: 'Step 5: Bulk Role Conversions (e.g. Students to Teachers)',
                description: 'Select individual checkboxes or click "Select All" in the table header. The Bulk Actions bar will slide in. Click "Convert Role" to instantly change all selected student profiles into certified teachers or facilitators without recreating their accounts.'
            },
            {
                heading: 'Step 6: Soft Deletion, Restoration, and Force Delete Safeguards',
                description: 'Click the Trash icon on any user row or use bulk selection to soft delete accounts. Soft-deleted accounts cannot log in but retain their submitted assignments and coursework intact. Filter by "Trashed Only" to restore them with a single click, or permanently purge them if required.'
            }
        ]
    },
    {
        id: 'admin-audit',
        category: 'admin',
        roleBadge: 'Administrator',
        badgeColor: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        icon: ShieldCheck,
        title: 'System Audit Trail & Component Oversight',
        summary: 'Complete accountability logging for administrative actions, security tracking, IP address recording, and component statistics.',
        quickLink: { name: 'Go to System Audit Trail', route: 'admin.audit-logs.index' },
        steps: [
            {
                heading: 'Step 1: Accessing the Audit Log System',
                description: 'Click "System Audit" from the top bar or /admin/audit-logs. Every user creation, inline edit, role change, soft delete, and bulk operation is immutably logged with timestamps.'
            },
            {
                heading: 'Step 2: Inspecting Action Diff Logs',
                description: 'Click "View Details" on any audit row to inspect the JSON diff showing exactly what changed (e.g., role changed from "student" to "teacher", email updated, or user restored).'
            },
            {
                heading: 'Step 3: Network & Security Metadata',
                description: 'Each entry records the actor\'s IP address, browser user-agent, and session footprint to detect unauthorized access or policy violations.'
            },
            {
                heading: 'Step 4: Component Oversight & System Health',
                description: 'Review active TVET trade categories (Solar PV, Electrical, Welding, Automobile, etc.), active courses, enrolled trainees, and system storage status.'
            }
        ]
    },
    {
        id: 'admin-bulk-import',
        category: 'admin',
        roleBadge: 'Administrator',
        badgeColor: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        icon: Download,
        title: 'Bulk TVET Teachers & Trainees CSV Onboarding',
        summary: 'Seamlessly onboard hundreds of teachers and learners across Benue State technical colleges via standard spreadsheet CSV files.',
        steps: [
            {
                heading: 'Step 1: Prepare the Standard CSV File',
                description: 'Prepare a UTF-8 CSV with the columns: First Name, Last Name, Email, Phone, Institution, State, Trade Specialization. Avoid spaces in headers.'
            },
            {
                heading: 'Step 2: Process Bulk Import in User Management',
                description: 'In /admin/users, locate the "Bulk Import" card. Select your CSV file and click "Upload & Validate". The system verifies email uniqueness and skips duplicate rows.'
            },
            {
                heading: 'Step 3: Automatic Role & Credentials Assignment',
                description: 'New accounts automatically receive default secure passwords, the designated role, and appear immediately in the Teacher Roster and Audit Trail.'
            }
        ]
    },

    // ==========================================
    // 2. TVET PEDAGOGY STUDIO (DAYS 9 & 10)
    // ==========================================
    {
        id: 'pedagogy-studio',
        category: 'pedagogy',
        roleBadge: 'TVET Pedagogy',
        badgeColor: 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        icon: Sparkles,
        title: 'TVET Pedagogy Studio Hub & Milestone Tracking',
        summary: 'Central command for competency-based TVET instructional design, official NBTE standard mapping, and Days 9–10 milestone completion.',
        quickLink: { name: 'Launch TVET Pedagogy Studio', route: 'pedagogy.studio' },
        steps: [
            {
                heading: 'Step 1: Open the Pedagogy Studio Hub',
                description: 'Access /pedagogy/studio from the "TVET Pedagogy Studio" menu dropdown in the top navbar. The dashboard provides milestone progress bars for Day 9 (Competency-Based) and Day 10 (Inclusive Workshop Design).'
            },
            {
                heading: 'Step 2: Inspecting Official NBTE Occupational Standards',
                description: 'Scroll to the Occupational Standards database table. Filter official Nigerian NBTE standards across Solar PV, Electrical, Welding, Automobile, Carpentry, Catering, Garment, and Agriculture to review authorized performance criteria.'
            },
            {
                heading: 'Step 3: Studio Quick Access Tools',
                description: 'Launch directly into the Competency Lesson Builder, Inclusive Lesson Designer, Interactive 4-Step & Project Planner, Workshop Equipment Planner, or the Day 9/10 Guided Wizards.'
            }
        ]
    },
    {
        id: 'pedagogy-builder',
        category: 'pedagogy',
        roleBadge: 'Day 9 Focus',
        badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        icon: Compass,
        title: 'Competency-Based Lesson Builder (4-Step Demo & PBL)',
        summary: 'Guided lesson builder covering trade selection, ABCD learning outcomes, the 4-step demonstration method, guided/independent practice, and fault diagnosis.',
        quickLink: { name: 'Open Competency Lesson Builder', route: 'pedagogy.lessons.builder' },
        steps: [
            {
                heading: 'Step 1: Select Trade & Occupational Standard',
                description: 'Choose your vocational trade category, link the official NBTE occupational standard, define the practical task (e.g., "Install and Commission 48V Off-Grid Solar PV System"), set duration in minutes, and target level (Beginner/Intermediate/Advanced).'
            },
            {
                heading: 'Step 2: Define ABCD Measurable Outcomes',
                description: 'Formulate learning outcomes using the 4-part ABCD model: Audience (Trainee), Behavior (Action verb), Condition (Tools/environment), and Degree (Quality tolerance benchmark).'
            },
            {
                heading: 'Step 3: Plan the 4-Step Instructor Demonstration',
                description: 'Structure workshop demonstration: Step 1 (Instructor Shows & Tells), Step 2 (Instructor Shows, Learner Tells), Step 3 (Learner Shows, Instructor Tells), Step 4 (Learner Shows & Tells independently).'
            },
            {
                heading: 'Step 4: Configure Guided & Independent Practice',
                description: 'Specify workstation arrangements, required hand tools, PPE checklists, step-by-step guidance prompts, and mastery benchmarks.'
            },
            {
                heading: 'Step 5: Project-Based Learning (PBL) & Fault Diagnosis',
                description: 'Formulate driving questions, milestones, and deliverables. Add real-world fault diagnosis scenarios specifying symptoms, diagnostic procedures, corrective actions, and critical safety rules.'
            },
            {
                heading: 'Step 6: Work-Based Learning (WBL) & Assessment Criteria',
                description: 'Add workplace simulation expectations, employer logbook evidence, and multi-method assessment criteria (Direct Observation, Product Inspection, Oral Questioning, Underpinning Knowledge).'
            },
            {
                heading: 'Step 7: Versioning, Duplication & Print Export',
                description: 'Save as Draft or Publish. Duplicate existing lessons to create variations for different classes. Click "Export / Print" to generate a clean, official A4 printable lesson document.'
            }
        ]
    },
    {
        id: 'pedagogy-inclusive',
        category: 'pedagogy',
        roleBadge: 'Day 10 Focus',
        badgeColor: 'bg-teal-500/20 text-teal-300 border-teal-500/30',
        icon: Wand2,
        title: 'Inclusive Lesson Designer (UDL, Differentiation & Workshop Safety)',
        summary: 'Universal Design for Learning (UDL), 3-tier differentiated tasks, non-negotiable safety preservation, gender equity, and equipment rotation matrices.',
        quickLink: { name: 'Open Inclusive Lesson Designer', route: 'pedagogy.inclusive-designer.index' },
        steps: [
            {
                heading: 'Step 1: Select Competency Lesson to Adapt',
                description: 'Open /pedagogy/inclusive-designer and choose any published or draft competency lesson from your portfolio to attach an inclusive adaptation.'
            },
            {
                heading: 'Step 2: 3-Tier Differentiated Practical Activities',
                description: 'Structure 3 distinct tiers: Tier 1 (Foundation/Beginner with pictorial schematics and step-by-step cue cards), Tier 2 (Standard Core Competency), Tier 3 (Advanced Extension with troubleshooting challenges and optimization tasks).'
            },
            {
                heading: 'Step 3: Apply Universal Design for Learning (UDL)',
                description: 'Implement the 3 core UDL pillars: Multiple Means of Engagement (real-world interest, choice of project), Multiple Means of Representation (visual diagrams, physical samples, audio-visual demos), Multiple Means of Action & Expression (hands-on build, oral explanation, digital diagram).'
            },
            {
                heading: 'Step 4: Mandatory Safety Preservation Check',
                description: 'Verify and check the Non-Negotiable Safety Preservation toggle. Accommodations must adapt the instructional method without lowering occupational safety standards, PPE compliance, or workshop hazard protocols.'
            },
            {
                heading: 'Step 5: Gender-Responsive & Inclusive Workshop Culture',
                description: 'Implement strategies that eliminate gender stereotypes, encourage female leadership in technical trades (e.g., Welding, Solar PV, Electrical), and mandate supportive peer-pairing.'
            },
            {
                heading: 'Step 6: Workshop Equipment Rotation & Shift Matrix',
                description: 'Configure station rotation schedules so that limited workshop tools (e.g. inverter testers, multimeters, hydraulic cutters) are shared equitably across trainee subgroups without crowding or idle time.'
            },
            {
                heading: 'Step 7: Submit for Facilitator Rubric Assessment',
                description: 'Click "Submit for Facilitator Review" to queue your completed lesson and inclusive adaptation into the official review registry for formal grading.'
            }
        ]
    },
    {
        id: 'pedagogy-wizards',
        category: 'pedagogy',
        roleBadge: 'Guided Learning',
        badgeColor: 'bg-purple-500/20 text-purple-300 border-purple-500/30',
        icon: Wand2,
        title: 'Day 9 & Day 10 Guided Interactive Wizards',
        summary: 'Step-by-step interactive wizards that walk teachers through competency lesson drafting and inclusive design adaptation with automated validation.',
        quickLink: { name: 'Open Day 9 Wizard', route: 'pedagogy.day9-wizard' },
        steps: [
            {
                heading: 'Day 9 Wizard (Competency-Based TVET Pedagogy)',
                description: 'Navigate to /pedagogy/day9-wizard. Complete the 5 interactive wizard stages: 1. Trade & Standards, 2. ABCD Outcomes, 3. 4-Step Demonstration, 4. Guided & Independent Practice, 5. PBL & Review. Once finished, click "Verify & Save to Portfolio".'
            },
            {
                heading: 'Day 10 Wizard (Inclusive Workshop & UDL Design)',
                description: 'Navigate to /pedagogy/day10-wizard. Complete the 4 inclusive stages: 1. 3-Tier Differentiation, 2. UDL Strategy Matrix, 3. Non-Negotiable Safety Compliance, 4. Equipment Sharing Rotation. Verify and link to your portfolio.'
            }
        ]
    },

    // ==========================================
    // 3. FACILITATORS GUIDE
    // ==========================================
    {
        id: 'facilitator-reviews',
        category: 'facilitator',
        roleBadge: 'Facilitator & Assessor',
        badgeColor: 'bg-purple-500/20 text-purple-300 border-purple-500/30',
        icon: Award,
        title: 'Facilitator Pedagogy Review Queue & 5-Dimension Rubric Grading',
        summary: 'Assess submitted teacher lesson plans and inclusive adaptations using the standardized 5-dimension 100-point TVET rubric.',
        quickLink: { name: 'Open Facilitator Review Queue', route: 'facilitator.pedagogy-reviews.index' },
        steps: [
            {
                heading: 'Step 1: Accessing the Pedagogy Review Queue',
                description: 'Log in as a Facilitator or Admin and click "Facilitator Reviews" in the top bar (/facilitator/pedagogy-reviews). The queue lists all submitted lesson plans awaiting formal appraisal.'
            },
            {
                heading: 'Step 2: Inspecting Teacher Lesson Artifacts',
                description: 'Click "Evaluate Lesson" to open the teacher\'s complete competency plan, ABCD objectives, 4-step demo plan, fault diagnosis scenarios, UDL strategies, and equipment rotation matrix.'
            },
            {
                heading: 'Step 3: Scoring Across the 5 Rubric Dimensions (20 pts each)',
                description: 'Score each criterion from Unsatisfactory (0-5) to Exemplary (16-20): 1. Standard Alignment & ABCD Objectives, 2. 4-Step Demonstration & Practice Structure, 3. Fault Diagnosis & Industry Simulation, 4. UDL & Differentiated Inclusion, 5. Assessment Rigor & Workshop Equipment Matrix.'
            },
            {
                heading: 'Step 4: Providing Written Assessor Feedback & Verdict',
                description: 'Enter qualitative feedback highlighting technical strengths and required revisions. Choose "Approve Lesson Plan" (status becomes Approved) or "Request Revisions" (status returns to Revisions Requested with comments).'
            }
        ]
    },

    // ==========================================
    // 4. TVET TEACHERS GUIDE
    // ==========================================
    {
        id: 'teacher-courses',
        category: 'teacher',
        roleBadge: 'Teacher / Instructor',
        badgeColor: 'bg-blue-500/20 text-blue-300 border-blue-500/30',
        icon: Briefcase,
        title: 'Visual Course Builder & Modular Curriculum Management',
        summary: 'Create vocational courses, attach modular sections, ABCD objectives, technical PDF resources, and external regulatory URLs.',
        quickLink: { name: 'Go to Courses', route: 'courses.index' },
        steps: [
            {
                heading: 'Step 1: Create a Course in Your Trade',
                description: 'In your Teacher Dashboard, click "Create New Course". Select your trade category (e.g. Solar PV Installation), enter course code (e.g. TVET-SPV-01), title, and set visibility to Draft or Published.'
            },
            {
                heading: 'Step 2: Add Modular Sections & Topics',
                description: 'In the Visual Course Builder, click "+ Add Section". Organize technical content sequentially (e.g. "Section 1: Solar Fundamentals & Safety", "Section 2: DC Combiner Wiring").'
            },
            {
                heading: 'Step 3: Attach Technical PDFs & Educational URLs',
                description: 'Upload workshop manuals, schematics, and equipment data sheets (.pdf, .docx). Link official NBTE, NAPTIN, or NEMSA guidelines via external URLs.'
            },
            {
                heading: 'Step 4: Preview as Learner',
                description: 'Click "Preview as Learner" at any time to verify exactly how enrolled trainees will view your syllabus, resources, and assignment prompts.'
            }
        ]
    },
    {
        id: 'teacher-rubrics',
        category: 'teacher',
        roleBadge: 'Teacher / Instructor',
        badgeColor: 'bg-blue-500/20 text-blue-300 border-blue-500/30',
        icon: Award,
        title: 'Competency Rubrics & Transparent Assessment',
        summary: 'Design 100-point performance rubrics, configure assignment submission cut-offs, and export formula-safe CSV gradebooks.',
        quickLink: { name: 'Manage Rubrics', route: 'rubrics.index' },
        steps: [
            {
                heading: 'Step 1: Create a 100-Point Rubric',
                description: 'Go to "Rubrics" and click "+ New Rubric". Add 4-5 weighted criteria (e.g. Workshop Safety & PPE [20 pts], Component Mounting [20 pts], Wiring Integrity [20 pts], Cable Crimping [20 pts], System Testing [20 pts]).'
            },
            {
                heading: 'Step 2: Attach Rubric to an Assignment',
                description: 'In your assignment settings, link your rubric. Trainees will see the exact performance criteria and level descriptors before submitting their work.'
            },
            {
                heading: 'Step 3: Grade Submissions with Single-Click Rubric Levels',
                description: 'Open trainee submissions and click the corresponding rubric cells (Exemplary, Proficient, Satisfactory, Unsatisfactory). Total scores calculate automatically.'
            },
            {
                heading: 'Step 4: Export Formula-Safe CSV Grade Report',
                description: 'Open the Course Gradebook and click "Export CSV". The generated file is sanitized against CSV formula injection and includes all trainee names, scores, and assessor remarks.'
            }
        ]
    },

    // ==========================================
    // 5. TRAINEES & STUDENTS GUIDE
    // ==========================================
    {
        id: 'trainee-auth',
        category: 'trainee',
        roleBadge: 'Trainee / Learner',
        badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        icon: ShieldCheck,
        title: 'Trainee Self-Registration, Sign-In & Password Eye Toggle',
        summary: 'How to create an account, log in, reveal/hide passwords, and protect credentials in shared computer workshops.',
        steps: [
            {
                heading: 'Step 1: Self-Registration',
                description: 'On https://lms.gregicthub.com, click "Register" in the top bar. Enter your full legal name, official email address, choose a secure password (minimum 8 characters), and confirm.'
            },
            {
                heading: 'Step 2: Sign In to Your Trainee Portal',
                description: 'Click "Sign In", enter your email address and password. If your technical instructor already pre-registered you, use your assigned institutional credentials.'
            },
            {
                heading: 'Step 3: Show / Hide Password Eye Toggle',
                description: 'Click or tap the Eye icon inside the password field to reveal your typed characters. This prevents typo errors on mobile screens. Always re-mask your password before continuing!'
            }
        ]
    },
    {
        id: 'trainee-learning',
        category: 'trainee',
        roleBadge: 'Trainee / Learner',
        badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        icon: LayoutDashboard,
        title: 'Trainee Dashboard, Submitting Assignments & Rubric Feedback',
        summary: 'Navigating trade modules, uploading practical workshop photos or reports, and reviewing instructor rubric scores.',
        steps: [
            {
                heading: 'Step 1: Accessing Your Dashboard & Enrolled Courses',
                description: 'Your Trainee Dashboard lists all enrolled trade courses, upcoming practical assignment deadlines, and current grade averages.'
            },
            {
                heading: 'Step 2: Downloading Technical Resources & Schematics',
                description: 'Click on any course topic to access instructional videos, safety documents, and workshop wiring schematics uploaded by your instructor.'
            },
            {
                heading: 'Step 3: Submitting Practical Work Before Deadlines',
                description: 'Open the assignment page, read the instructions, and submit your work via file upload (.pdf, .jpg, .png, .docx) or online text notes. Note the Due Date and Cut-Off time.'
            },
            {
                heading: 'Step 4: Inspecting Your Graded Rubric',
                description: 'Once graded by your instructor, view the detailed rubric breakdown to see how many points were earned across Safety, Accuracy, Craftsmanship, and Operation.'
            }
        ]
    },

    // ==========================================
    // 6. SECURITY & UI THEMES
    // ==========================================
    {
        id: 'security-themes',
        category: 'security',
        roleBadge: 'System Feature',
        badgeColor: 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        icon: Sun,
        title: 'Dark & Light Theme Switching and Loading Indicators',
        summary: 'Optimize screen readability for bright workshop conditions or evening study, and understand the page activity loading spinner.',
        steps: [
            {
                heading: 'Light & Dark Mode Switcher',
                description: 'Click the Sun / Moon toggle icon in the top navigation bar. Light mode provides high contrast for daytime workshop viewing; Dark mode (#020617) reduces eye strain during evening planning sessions. Your preference is automatically remembered.'
            },
            {
                heading: 'Page Load & Inertia Activity Spinner Icon',
                description: 'Whenever you navigate between pages, filter tables, or submit forms, an animated rolling dual-ring spinner icon and glowing top progress line automatically display in the top-right and center to confirm background page activity.'
            },
            {
                heading: 'Bypass Maintenance Protocol',
                description: 'During scheduled maintenance updates, administrators can access the system using the official deployment secret bypass key: /?secret=greg-deploy-bypass-2026.'
            }
        ]
    }
];

const filteredSections = computed(() => {
    let result = sections;

    // Filter by Category Tab
    if (activeCategory.value !== 'all') {
        result = result.filter(sec => sec.category === activeCategory.value);
    }

    // Filter by Search Query
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        result = result.filter(sec =>
            sec.title.toLowerCase().includes(q) ||
            sec.summary.toLowerCase().includes(q) ||
            sec.roleBadge.toLowerCase().includes(q) ||
            sec.steps.some(s => s.heading.toLowerCase().includes(q) || s.description.toLowerCase().includes(q))
        );
    }

    return result;
});

const currentSection = computed(() => {
    return sections.find(s => s.id === activeSectionId.value) || filteredSections.value[0] || sections[0];
});

const setSection = (id) => {
    activeSectionId.value = id;
};

const printGuide = () => {
    window.print();
};
</script>

<template>
    <Head title="User &amp; Admin Reference Guide — Greg ICT Hub LMS" />

    <!-- Rolling Page Activity Spinner Indicator -->
    <PageActivityIndicator />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-100 flex flex-col transition-colors duration-200 print:bg-white print:text-black">
        <!-- Sticky Top Navigation Header -->
        <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm print:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <Link :href="route('home')" class="focus:outline-none">
                        <ApplicationLogo size="sm" />
                    </Link>
                    <div class="hidden sm:block border-l border-slate-300 dark:border-slate-700 pl-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                            Operations Manual &amp; Reference Guide
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-2.5 sm:space-x-3">
                    <!-- Print Guide Button -->
                    <button
                        @click="printGuide"
                        type="button"
                        class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition flex items-center space-x-1.5"
                        title="Print or Save as PDF"
                    >
                        <Printer class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                        <span class="hidden md:inline">Print / PDF</span>
                    </button>

                    <!-- Theme Toggle -->
                    <ThemeToggle />

                    <!-- User / Auth Quick Link -->
                    <template v-if="isAuthenticated">
                        <Link
                            :href="route('dashboard')"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition flex items-center space-x-1.5"
                        >
                            <LayoutDashboard class="w-3.5 h-3.5" />
                            <span>My Dashboard</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition flex items-center space-x-1.5"
                        >
                            <span>Sign In</span>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Banner Section -->
        <div class="bg-gradient-to-b from-rose-950/20 via-slate-900 to-slate-950 text-white border-b border-rose-900/30 py-8 sm:py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden print:bg-white print:text-black print:py-2">
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold mb-3">
                            <Sparkles class="w-3.5 h-3.5 text-rose-400" />
                            <span>Greg &amp; Co. ICT Ltd • Benue State IDEAS-TVET LMS</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">
                            User &amp; Administrator Reference Guide
                        </h1>
                        <p class="text-sm sm:text-base text-slate-300 max-w-3xl mt-2 leading-relaxed">
                            Official step-by-step documentation for System Administrators, TVET Instructors, Facilitators, and Trainees. Covers User Management, TVET Pedagogy Studio (Days 9 &amp; 10), Visual Course Builder, and Safety Verification.
                        </p>
                    </div>

                    <!-- Instant Search Bar -->
                    <div class="w-full md:w-80 shrink-0 print:hidden">
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Search Manual &amp; Procedures</label>
                        <div class="relative">
                            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="e.g. bulk, rubric, UDL, delete, safety..."
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-xs focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition"
                            />
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-white"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Audience Role Filter Tabs -->
                <div class="mt-8 flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-none border-t border-slate-800/80 pt-4 print:hidden">
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        @click="activeCategory = cat.id"
                        :class="[
                            'px-3.5 py-2 rounded-xl text-xs font-semibold transition whitespace-nowrap flex items-center space-x-2',
                            activeCategory === cat.id
                                ? 'bg-rose-600 text-white shadow-lg shadow-rose-900/40 ring-1 ring-rose-400'
                                : 'bg-slate-800/70 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700/60'
                        ]"
                    >
                        <component :is="cat.icon" class="w-3.5 h-3.5" />
                        <span>{{ cat.label }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area: Split Sidebar + Content View -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left Navigation List (Desktop) -->
                <aside class="lg:col-span-4 space-y-3 print:hidden">
                    <div class="sticky top-24 space-y-3">
                        <div class="flex items-center justify-between px-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Documentation Modules ({{ filteredSections.length }})
                            </span>
                            <span v-if="searchQuery" class="text-[11px] text-rose-500 font-semibold">
                                Filtered
                            </span>
                        </div>

                        <div class="max-h-[70vh] overflow-y-auto space-y-2 pr-1">
                            <button
                                v-for="sec in filteredSections"
                                :key="sec.id"
                                @click="setSection(sec.id)"
                                :class="[
                                    'w-full text-left p-3.5 rounded-2xl transition border flex items-start space-x-3',
                                    activeSectionId === sec.id
                                        ? 'bg-white dark:bg-slate-900 border-rose-500/50 shadow-md ring-1 ring-rose-500/20'
                                        : 'bg-white/60 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800/80 hover:bg-white dark:hover:bg-slate-900'
                                ]"
                            >
                                <div
                                    :class="[
                                        'p-2 rounded-xl shrink-0 mt-0.5',
                                        activeSectionId === sec.id ? 'bg-rose-500/10 text-rose-500' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
                                    ]"
                                >
                                    <component :is="sec.icon" class="w-4 h-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full border', sec.badgeColor]">
                                            {{ sec.roleBadge }}
                                        </span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-1 truncate">
                                        {{ sec.title }}
                                    </h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">
                                        {{ sec.summary }}
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Right Detail Panel -->
                <div class="lg:col-span-8 space-y-6">
                    <article
                        v-if="currentSection"
                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6"
                    >
                        <!-- Section Header -->
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                                <span :class="['text-xs font-bold px-2.5 py-0.5 rounded-full border', currentSection.badgeColor]">
                                    {{ currentSection.roleBadge }}
                                </span>

                                <!-- Quick Jump Action Button -->
                                <Link
                                    v-if="currentSection.quickLink && isAuthenticated"
                                    :href="route(currentSection.quickLink.route)"
                                    class="inline-flex items-center space-x-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline"
                                >
                                    <span>{{ currentSection.quickLink.name }}</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>

                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                {{ currentSection.title }}
                            </h2>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                                {{ currentSection.summary }}
                            </p>
                        </div>

                        <!-- Step-by-Step Procedure Cards -->
                        <div class="space-y-4">
                            <div
                                v-for="(step, idx) in currentSection.steps"
                                :key="idx"
                                class="p-4 sm:p-5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80 space-y-2 hover:border-slate-300 dark:hover:border-slate-700 transition"
                            >
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-6 h-6 rounded-full bg-rose-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ idx + 1 }}
                                    </span>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ step.heading }}
                                    </h3>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 pl-8 leading-relaxed">
                                    {{ step.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Interactive Password Reveal Testing Box (if on Auth Section) -->
                        <div
                            v-if="currentSection.id === 'trainee-auth'"
                            class="p-5 rounded-2xl bg-gradient-to-br from-rose-950/30 to-slate-900 border border-rose-500/30 text-white space-y-3 mt-6"
                        >
                            <div class="flex items-center space-x-2 text-rose-400 font-bold text-xs uppercase tracking-wider">
                                <Sparkles class="w-4 h-4" />
                                <span>Try Password Eye Reveal Live</span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Test how the password eye toggle unmasks hidden characters. Tap the eye icon inside the input below to practice:
                            </p>
                            <div class="max-w-md">
                                <PasswordInput
                                    id="demo-password-guide"
                                    v-model="demoPassword"
                                    class="w-full text-sm"
                                />
                            </div>
                        </div>

                        <!-- Interactive Dark / Light Mode Testing Box (if on Themes Section) -->
                        <div
                            v-if="currentSection.id === 'security-themes'"
                            class="p-5 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-3 mt-6"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Live Theme Switcher Test</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Toggle themes right now to see the transition</p>
                                </div>
                                <ThemeToggle />
                            </div>
                        </div>
                    </article>

                    <!-- All Sections Print View (When printing to PDF, displays all sections sequentially) -->
                    <div class="hidden print:block space-y-8">
                        <div
                            v-for="sec in sections"
                            :key="sec.id"
                            class="border-b pb-6 space-y-3 page-break-inside-avoid"
                        >
                            <div class="text-xs font-bold uppercase text-rose-600">{{ sec.roleBadge }}</div>
                            <h2 class="text-lg font-bold">{{ sec.title }}</h2>
                            <p class="text-xs text-slate-600">{{ sec.summary }}</p>
                            <div class="space-y-2 mt-3">
                                <div v-for="(step, idx) in sec.steps" :key="idx" class="text-xs pl-4 border-l-2 border-slate-300">
                                    <div class="font-bold">{{ idx + 1 }}. {{ step.heading }}</div>
                                    <div class="text-slate-700 mt-0.5">{{ step.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 dark:text-slate-400 print:hidden">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <ApplicationLogo size="sm" />
                    <span>&copy; 2026 Greg &amp; Co. ICT Ltd, Makurdi, Benue State, Nigeria. All rights reserved.</span>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('home')" class="hover:underline">Home</Link>
                    <Link :href="route('login')" class="hover:underline">Login</Link>
                    <Link :href="route('register')" class="hover:underline">Register</Link>
                    <a href="mailto:support@gregicthub.com" class="hover:underline">support@gregicthub.com</a>
                </div>
            </div>
        </footer>
    </div>
</template>
