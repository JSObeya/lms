<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Wand2,
    CheckCircle2,
    Circle,
    ArrowRight,
    Users,
    BookOpen,
    FolderPlus,
    Target,
    FileText,
    ClipboardList,
    Clock,
    Award,
    Eye,
    CheckCheck,
    Download,
    Sparkles,
    ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
    progress: Object,
    practice_students: Array,
    course: Object,
});

const formGenerate = useForm({});
const generateLearner = () => {
    formGenerate.post(route('day8.generate-practice-learner'), {
        preserveScroll: true,
    });
};

const steps = computed(() => [
    {
        number: 1,
        title: 'Create a Practice Course',
        instruction: 'Create an independent practice course in your designated TVET trade specialization with title, code, and summary.',
        expected: 'A practice course record exists with unique code and category.',
        completed: Boolean(props.progress?.step_1_course_created),
        icon: BookOpen,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: props.course ? 'View Course Builder' : 'Create Course Now',
    },
    {
        number: 2,
        title: 'Label a Topic / Section',
        instruction: 'Organize your syllabus into structured instructional topics or practical workshop units.',
        expected: 'At least one named topic/section exists in your course structure.',
        completed: Boolean(props.progress?.step_2_topic_labeled),
        icon: FolderPlus,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: 'Manage Sections in Builder',
    },
    {
        number: 3,
        title: 'Write a Measurable Learning Objective',
        instruction: 'Formulate an ABCD (Audience, Behavior, Condition, Degree) measurable objective using bloom taxonomy verbs.',
        expected: 'A measurable objective with clear action verb, condition, and performance standard.',
        completed: Boolean(props.progress?.step_3_objective_saved),
        icon: Target,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: 'Add Objective in Builder',
    },
    {
        number: 4,
        title: 'Add a PDF or Educational URL',
        instruction: 'Upload a technical reference PDF or attach an approved external URL/worksheet for trainees.',
        expected: 'At least one verified learning resource attached to a course section.',
        completed: Boolean(props.progress?.step_4_resource_attached),
        icon: FileText,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: 'Attach Resource in Builder',
    },
    {
        number: 5,
        title: 'Create an Assignment',
        instruction: 'Design a practical assessment task detailing tools, tasks, submission requirements, and instructions.',
        expected: 'A published assignment configured with max points and instructions.',
        completed: Boolean(props.progress?.step_5_assignment_created),
        icon: ClipboardList,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: 'Configure Assignment',
    },
    {
        number: 6,
        title: 'Configure Deadlines and Timezone',
        instruction: 'Set realistic submission opening date, due date, cut-off date, and West Africa Time (WAT) timezone.',
        expected: 'Due date and cut-off timestamps validated on the assignment.',
        completed: Boolean(props.progress?.step_6_deadline_configured),
        icon: Clock,
        actionUrl: props.course ? route('courses.builder', props.course.id) : route('courses.create'),
        actionText: 'Verify Deadlines',
    },
    {
        number: 7,
        title: 'Define a Grading Rubric',
        instruction: 'Build a multi-level assessment rubric with measurable criteria (e.g., Component ID, Safety, Checklist).',
        expected: 'An activated rubric with criteria, scoring levels, and total points.',
        completed: Boolean(props.progress?.step_7_rubric_activated),
        icon: Award,
        actionUrl: route('rubrics.index'),
        actionText: 'Design Rubric',
    },
    {
        number: 8,
        title: 'Preview the Learner View',
        instruction: 'Switch to the dedicated student preview mode to inspect how trainees experience your published resources.',
        expected: 'Learner preview opened with editing controls hidden and student navigation displayed.',
        completed: Boolean(props.progress?.step_8_preview_viewed),
        icon: Eye,
        actionUrl: props.course ? route('courses.preview', props.course.id) : route('courses.create'),
        actionText: 'Preview as Student',
    },
    {
        number: 9,
        title: 'Grade a Sample Submission',
        instruction: 'Evaluate a synthetic practice learner submission using your configured rubric and award points.',
        expected: 'A graded submission with criterion-level scores, rubric calculation, and feedback.',
        completed: Boolean(props.progress?.step_9_sample_graded),
        icon: CheckCheck,
        actionUrl: props.course ? route('gradebook.show', props.course.id) : route('dashboard'),
        actionText: 'Grade in Gradebook',
    },
    {
        number: 10,
        title: 'Export the Grade Report',
        instruction: 'Generate and download an official CSV/Excel course grade report with formula-injection escaping.',
        expected: 'Exported gradebook report with student marks, percentages, and timestamps.',
        completed: Boolean(props.progress?.step_10_report_exported),
        icon: Download,
        actionUrl: props.course ? route('gradebook.export-csv', props.course.id) : route('dashboard'),
        actionText: 'Export CSV Grade Report',
    },
]);

const completedCount = computed(() => steps.value.filter(s => s.completed).length);
const percentage = computed(() => Math.round((completedCount.value / 10) * 100));
</script>

<template>
    <Head title="Day 8 TVET Practical Wizard — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-600 dark:text-amber-400 text-xs font-bold uppercase tracking-wider mb-2">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>IDEAS–TVET Practical Competency Assessment</span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Day 8 — LMS Course Creation &amp; Assessment Practice
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        A systematic 10-stage competency workflow demonstrating complete mastery of digital course delivery.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="/day8-guide/download-word"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 font-semibold text-sm transition shadow-sm"
                        title="Download official Day 8 Topic Guide document (.docx) to upload to your course"
                    >
                        <Download class="w-4 h-4" />
                        <span>Download Day 8 Topic Guide (.docx)</span>
                    </a>
                    <button
                        @click="generateLearner"
                        :disabled="formGenerate.processing"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 text-teal-400 border border-teal-500/40 hover:bg-slate-800 font-semibold text-sm transition shadow-sm"
                    >
                        <Users class="w-4 h-4" />
                        <span>Generate Sandbox Learner</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Progress Overview Card -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Overall Practical Verification Progress
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Completed {{ completedCount }} of 10 required TVET competencies
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black text-teal-600 dark:text-teal-400">{{ percentage }}%</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                <div
                    class="bg-gradient-to-r from-teal-500 via-blue-500 to-amber-500 h-full rounded-full transition-all duration-500"
                    :style="{ width: `${percentage}%` }"
                ></div>
            </div>

            <!-- Facilitator Verification Banner -->
            <div v-if="progress?.is_verified_by_facilitator" class="mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-start space-x-3">
                <ShieldCheck class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" />
                <div class="text-xs text-emerald-900 dark:text-emerald-200">
                    <div class="font-bold text-sm">Official Facilitator Sign-Off Completed</div>
                    <div class="mt-1">
                        Verified by <strong>{{ progress.verifier?.name || 'Assigned Facilitator' }}</strong> on {{ new Date(progress.completed_at).toLocaleDateString() }}.
                    </div>
                    <p v-if="progress.facilitator_feedback" class="mt-2 text-slate-700 dark:text-slate-300 italic">
                        "{{ progress.facilitator_feedback }}"
                    </p>
                </div>
            </div>
        </div>

        <!-- 10 Steps Timeline Grid -->
        <div class="space-y-4">
            <div
                v-for="step in steps"
                :key="step.number"
                :class="[
                    'p-5 rounded-2xl border transition shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6',
                    step.completed
                        ? 'bg-white dark:bg-slate-900 border-emerald-200 dark:border-emerald-900/50'
                        : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800'
                ]"
            >
                <div class="flex items-start space-x-4">
                    <!-- Status Icon -->
                    <div
                        :class="[
                            'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm',
                            step.completed
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
                                : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'
                        ]"
                    >
                        <CheckCircle2 v-if="step.completed" class="w-5 h-5" />
                        <span v-else>{{ step.number }}</span>
                    </div>

                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-base text-slate-900 dark:text-white">
                                Step {{ step.number }}: {{ step.title }}
                            </h3>
                            <span
                                :class="[
                                    'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full',
                                    step.completed
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300'
                                        : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                                ]"
                            >
                                {{ step.completed ? 'Completed' : 'Pending' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 max-w-2xl">
                            {{ step.instruction }}
                        </p>
                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-medium mt-1">
                            Expected Outcome: {{ step.expected }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0 self-end md:self-center">
                    <a
                        v-if="step.number === 4"
                        href="/day8-guide/download-word"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 border border-amber-500/30 transition shadow-sm"
                        title="Download official Day 8 topic Word guide (.docx) to attach to your course section"
                    >
                        <Download class="w-3.5 h-3.5" />
                        <span>Download Topic Guide (.docx)</span>
                    </a>
                    <a
                        :href="step.actionUrl"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm"
                        :class="step.completed
                            ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'
                            : 'bg-teal-600 hover:bg-teal-700 text-white'"
                    >
                        <span>{{ step.actionText }}</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
