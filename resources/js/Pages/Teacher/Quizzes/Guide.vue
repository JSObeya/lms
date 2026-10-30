<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    CheckSquare,
    BookOpen,
    HelpCircle,
    BarChart3,
    UploadCloud,
    Download,
    Printer,
    FileSpreadsheet,
    Shield,
    Users,
    Clock,
    Award,
    CheckCircle2,
    ArrowRight,
    Sparkles,
    Settings,
    Layers,
    FileText,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    isEvaluator: {
        type: Boolean,
        default: false,
    },
});

const activeTab = ref('candidates'); // 'candidates', 'evaluators', 'admins'
</script>

<template>
    <Head title="MCQ Examination Module User & Administrator Guide" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-500/20">
                        <BookOpen class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            MCQ Examination Engine &amp; Question Bank Guide
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Comprehensive operational manual for Candidates, Facilitator Evaluators, and System Administrators
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition cursor-pointer"
                    >
                        <Printer class="w-4 h-4 text-slate-600 dark:text-slate-400" />
                        <span>Print Guide</span>
                    </button>

                    <Link
                        :href="route('quizzes.index')"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold transition shadow-xs"
                    >
                        <CheckSquare class="w-4 h-4" />
                        <span>Available Exams</span>
                    </Link>

                    <Link
                        v-if="isEvaluator"
                        :href="route('admin.quizzes.questions.index')"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition shadow-xs"
                    >
                        <HelpCircle class="w-4 h-4" />
                        <span>Question Bank</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-8">
            <!-- Role Selector Navigation Tabs -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-2 flex flex-wrap items-center gap-2">
                <button
                    @click="activeTab = 'candidates'"
                    class="flex-1 min-w-[200px] py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 cursor-pointer"
                    :class="activeTab === 'candidates'
                        ? 'bg-amber-500 text-slate-950 shadow-md'
                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <CheckSquare class="w-4 h-4" />
                    <span>1. Candidates &amp; Teachers Guide</span>
                </button>

                <button
                    @click="activeTab = 'evaluators'"
                    class="flex-1 min-w-[200px] py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 cursor-pointer"
                    :class="activeTab === 'evaluators'
                        ? 'bg-purple-600 text-white shadow-md'
                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <BarChart3 class="w-4 h-4" />
                    <span>2. Evaluators &amp; Facilitators Guide</span>
                </button>

                <button
                    @click="activeTab = 'admins'"
                    class="flex-1 min-w-[200px] py-3 px-4 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 cursor-pointer"
                    :class="activeTab === 'admins'
                        ? 'bg-teal-600 text-white shadow-md'
                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <Settings class="w-4 h-4" />
                    <span>3. Platform Administrators Guide</span>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 1: CANDIDATES & TEACHERS GUIDE                       -->
            <!-- ======================================================== -->
            <div v-if="activeTab === 'candidates'" class="space-y-6">
                <!-- Overview Hero -->
                <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-br from-amber-500/10 via-slate-900/40 to-slate-900/60 border border-amber-500/30 space-y-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold">
                        <Award class="w-4 h-4" />
                        <span>Candidate Assessment Workflow</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white">
                        How to Take and Complete Competency MCQ Examinations
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                        The TVET MCQ Assessment Engine allows TVET instructors, teachers, and trainees to validate their practical, pedagogical, and safety competencies. Every attempt is automatically graded, timestamped, and stored in your profile with printable evaluation slips.
                    </p>
                </div>

                <!-- 4 Step Guide -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center">
                            1
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Select Your Examination</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Navigate to <strong class="text-amber-600 dark:text-amber-400">MCQ Quiz</strong> in the top menu. Review the available examinations, including their trade categories (e.g. <em>Solar PV, Electrical, Pedagogy</em>), total questions, and time limit. Click <strong>"Start Exam Now"</strong> to begin.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center">
                            2
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Timed Exam Environment</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            A live countdown timer is displayed in the header. You can jump directly to any question using the question grid drawer. Your selected options (A, B, C, or D) are saved immediately in your browser session as you progress.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center">
                            3
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Review &amp; Final Submission</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Before submitting, the bottom summary alerts you to any unanswered questions. Once satisfied, click <strong>"Submit Examination"</strong>. If the timer reaches zero, your answers are automatically submitted on your behalf.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center">
                            4
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Instant Grading &amp; Evaluation Slip</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Upon submission, results are calculated instantly. You receive a score breakdown, percentage, pass/fail status, performance grade (<em>Distinction, Merit, Credit, Pass</em>), category strengths, and an official printable evaluation slip.
                        </p>
                    </div>
                </div>

                <!-- Grading Benchmarks Callout -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">National TVET Performance Grading Bands</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center text-xs">
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-500/30">
                            <div class="font-black text-emerald-600 dark:text-emerald-400 text-sm">85% – 100%</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5">Distinction</div>
                        </div>
                        <div class="p-3 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-500/30">
                            <div class="font-black text-teal-600 dark:text-teal-400 text-sm">70% – 84%</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5">Merit</div>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-500/30">
                            <div class="font-black text-blue-600 dark:text-blue-400 text-sm">60% – 69%</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5">Credit</div>
                        </div>
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-500/30">
                            <div class="font-black text-amber-600 dark:text-amber-400 text-sm">50% – 59%</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5">Pass</div>
                        </div>
                        <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-500/30 col-span-2 sm:col-span-1">
                            <div class="font-black text-rose-600 dark:text-rose-400 text-sm">&lt; 50%</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300 mt-0.5">Needs Improvement</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 2: EVALUATORS & FACILITATORS GUIDE                   -->
            <!-- ======================================================== -->
            <div v-if="activeTab === 'evaluators'" class="space-y-6">
                <!-- Overview Hero -->
                <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-br from-purple-900/20 via-slate-900/40 to-slate-900/60 border border-purple-500/30 space-y-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-lg bg-purple-500/20 text-purple-300 text-xs font-bold">
                        <BarChart3 class="w-4 h-4" />
                        <span>Facilitator &amp; Evaluator Intelligence</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white">
                        Analyzing Results, Collective Evaluation &amp; Batch Printing
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                        Evaluators and lead facilitators have full visibility into live teacher submissions across all participating technical colleges and state vocational boards. You can filter, export datasets, and batch-print evaluation certificates.
                    </p>
                </div>

                <!-- Facilitator Capabilities Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2.5">
                        <div class="p-2 w-fit rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                            <Layers class="w-5 h-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Multi-Dimensional Filtering</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Filter candidate submissions simultaneously by <strong>State</strong> (e.g. Benue, Kano, Lagos), <strong>Technical College / Institution</strong>, <strong>Pass/Fail Status</strong>, and <strong>Search query</strong> (teacher name, email, phone).
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2.5">
                        <div class="p-2 w-fit rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                            <FileSpreadsheet class="w-5 h-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Institutional CSV Export</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Export the active filtered dataset to CSV with one click. The export includes Candidate Name, Institution, State, Trade Specialization, Score, Percentage, Grade, and Date.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2.5">
                        <div class="p-2 w-fit rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                            <Printer class="w-5 h-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Dual Printing Formats</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Select candidates using the table checkboxes to trigger either:
                            <strong>1. Collective Master Scorecard</strong> (compact multi-candidate audit sheet) or
                            <strong>2. Batch Individual Slips</strong> (page-break individual evaluation certificates).
                        </p>
                    </div>
                </div>

                <!-- Step-by-Step Batch Print Instructions -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <Printer class="w-5 h-5 text-purple-500" />
                        <span>Step-by-Step: Printing Selected Teacher Results</span>
                    </h4>
                    <ol class="space-y-3 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Navigate to <strong class="text-purple-600 dark:text-purple-400">MCQ &amp; Exams &rarr; Quiz Analytics</strong> in the navigation bar.</li>
                        <li>Use the filter dropdowns to narrow down by college or pass status.</li>
                        <li>Check the box next to specific candidates, or click the master checkbox in the header row to select all filtered teachers.</li>
                        <li>A purple floating action bar will slide in showing <em>"Selected (N) Candidate Submissions"</em>.</li>
                        <li>Click <strong>"Batch Print Individual Slips"</strong> for separate certificates, or <strong>"Print Collective Scorecard"</strong> for a consolidated institutional report.</li>
                        <li>Your browser's native print dialogue will automatically open with clean, styled typography and pagination.</li>
                    </ol>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 3: PLATFORM ADMINISTRATORS GUIDE                     -->
            <!-- ======================================================== -->
            <div v-if="activeTab === 'admins'" class="space-y-6">
                <!-- Overview Hero -->
                <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-br from-teal-900/20 via-slate-900/40 to-slate-900/60 border border-teal-500/30 space-y-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-lg bg-teal-500/20 text-teal-300 text-xs font-bold">
                        <Settings class="w-4 h-4" />
                        <span>Exam Architecture &amp; Ingestion Management</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white">
                        Creating Examinations &amp; Managing Question Banks via Bulk CSV
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                        Administrators can create unlimited new assessments for any trade discipline (Solar, Welding, Electrical, Automotive, CBT Pedagogy), assign custom passing benchmarks, and rapidly ingest questions manually or via CSV spreadsheets.
                    </p>
                </div>

                <!-- Admin Action Guide -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Creating New Exam -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="flex items-center space-x-2 text-teal-600 dark:text-teal-400 font-bold text-sm">
                            <Sparkles class="w-5 h-5" />
                            <span>Creating a New Examination</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Click <strong class="text-teal-600 dark:text-teal-400">"+ Create New Exam"</strong> in the Exams Hub. Provide:
                        </p>
                        <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1 list-disc list-inside">
                            <li><strong>Title:</strong> Descriptive exam name (e.g. <em>Solar PV Systems Final Assessment</em>).</li>
                            <li><strong>Category:</strong> Select from 20+ standard categories or type a custom trade domain.</li>
                            <li><strong>Time Limit:</strong> Allocated duration in minutes (5 to 300 minutes).</li>
                            <li><strong>Pass Mark:</strong> Target passing percentage (e.g. 60%).</li>
                            <li><strong>Active Status:</strong> Keep unchecked for draft preparation; check to publish.</li>
                        </ul>
                    </div>

                    <!-- Bulk CSV Upload -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="flex items-center space-x-2 text-teal-600 dark:text-teal-400 font-bold text-sm">
                            <UploadCloud class="w-5 h-5" />
                            <span>Bulk CSV Question Ingestion</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Inside the Question Bank, click <strong>"Bulk CSV Upload"</strong> to import dozens or hundreds of questions at once:
                        </p>
                        <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1 list-disc list-inside">
                            <li><strong>Append Mode:</strong> Adds new questions to existing ones without touching current items.</li>
                            <li><strong>Replace Mode:</strong> Clears current questions in that exam and replaces them with the CSV batch.</li>
                            <li><strong>Auto-Sequencing:</strong> Question numbers are automatically re-indexed from 1 to N.</li>
                        </ul>
                    </div>
                </div>

                <!-- CSV Template Specification & Download -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <FileSpreadsheet class="w-5 h-5 text-teal-500" />
                                <span>CSV Format Specification &amp; Sample Template</span>
                            </h4>
                            <p class="text-xs text-slate-500">Ensure your CSV header matches exactly in lowercase or uppercase</p>
                        </div>
                        <a
                            :href="route('admin.quizzes.questions.sample-csv')"
                            class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-sm inline-flex items-center space-x-1.5 shrink-0"
                        >
                            <Download class="w-4 h-4" />
                            <span>Download Standard CSV Template</span>
                        </a>
                    </div>

                    <!-- Column Spec Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase font-bold text-[10px]">
                                <tr>
                                    <th class="p-2.5">Column Header</th>
                                    <th class="p-2.5">Required?</th>
                                    <th class="p-2.5">Valid Values</th>
                                    <th class="p-2.5">Example</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]">
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">category</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">Trade or pedagogy domain</td>
                                    <td class="p-2.5">Solar PV Installation</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">question_text</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">Full stem of the MCQ question</td>
                                    <td class="p-2.5">What is the function of an inverter?</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">option_a</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">First choice text</td>
                                    <td class="p-2.5">Converts DC to AC power</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">option_b</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">Second choice text</td>
                                    <td class="p-2.5">Stores excess charge</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">option_c</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">Third choice text</td>
                                    <td class="p-2.5">Steps down utility voltage</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">option_d</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans">Fourth choice text</td>
                                    <td class="p-2.5">Regulates battery temperature</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">correct_option</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Yes</span></td>
                                    <td class="p-2.5 font-sans"><code class="font-mono font-bold">a, b, c, d</code> (or A, B, C, D)</td>
                                    <td class="p-2.5">a</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">explanation</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Optional</span></td>
                                    <td class="p-2.5 font-sans">Rationale shown after grading</td>
                                    <td class="p-2.5">Inverters invert Direct Current to Alternating Current.</td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 font-bold text-teal-600 dark:text-teal-400">points</td>
                                    <td class="p-2.5 font-sans"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Optional</span></td>
                                    <td class="p-2.5 font-sans">Integer points (default 1)</td>
                                    <td class="p-2.5">1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>