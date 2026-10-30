<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BookOpen,
    Wand2,
    Users,
    ClipboardCheck,
    Plus,
    ExternalLink,
    GraduationCap,
    CheckCircle2,
    Clock,
    Sparkles,
    Eye,
    ChevronRight,
    Table,
    Compass,
    Layers,
    Settings2,
    Briefcase,
    FileDown,
    Download,
    BookMarked,
    FileText
} from 'lucide-vue-next';

const props = defineProps({
    courses: Array,
    day8_progress: Object,
    pending_submissions: Array,
    practice_learners: Array,
    recent_documents: {
        type: Array,
        default: () => [],
    },
});

const formGenerate = useForm({});
const generateLearner = () => {
    formGenerate.post(route('day8.generate-practice-learner'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Teacher Dashboard — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Instructor Studio &amp; TVET Sandbox
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        IDEAS–TVET Technical &amp; Vocational Training Hub — Benue State
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('courses.create')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-sm transition"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create Course</span>
                    </Link>
                </div>
            </div>
        </template>

        <!-- Day 8 Practical Wizard Progress Banner -->
        <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-blue-950 to-teal-950 text-white shadow-xl border border-teal-500/20 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                <Wand2 class="w-64 h-64 text-teal-300" />
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-3 border border-amber-400/30">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>Day 8 Practical Assessment Exercise</span>
                    </div>
                    <h2 class="text-xl font-bold text-white">
                        LMS Course Creation &amp; Assessment Practice Wizard
                    </h2>
                    <p class="text-slate-300 text-sm mt-1">
                        Complete the 10-step guided practical requirement to verify your TVET digital competency, generate isolated sample submissions, evaluate with rubrics, and export grade reports.
                    </p>

                    <!-- Progress Bar -->
                    <div class="mt-4 flex items-center space-x-4">
                        <div class="flex-1 bg-slate-800/80 rounded-full h-3.5 p-0.5 overflow-hidden border border-slate-700">
                            <div
                                class="bg-gradient-to-r from-teal-400 to-amber-400 h-full rounded-full transition-all duration-500"
                                :style="{ width: `${day8_progress.percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-sm font-extrabold text-teal-300 whitespace-nowrap">
                            {{ day8_progress.completed_count }} / 10 Steps ({{ day8_progress.percentage }}%)
                        </span>
                    </div>

                    <div v-if="day8_progress.is_verified" class="mt-3 flex items-center space-x-2 text-xs text-emerald-400">
                        <CheckCircle2 class="w-4 h-4" />
                        <span>Verified &amp; approved by Master Facilitator</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <Link
                        :href="route('day8.wizard')"
                        class="inline-flex items-center justify-center space-x-2 px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-sm shadow-lg transition"
                    >
                        <Wand2 class="w-4 h-4" />
                        <span>Open Day 8 Wizard</span>
                        <ChevronRight class="w-4 h-4" />
                    </Link>

                    <button
                        @click="generateLearner"
                        :disabled="formGenerate.processing"
                        class="inline-flex items-center justify-center space-x-2 px-4 py-3 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-teal-300 border border-teal-500/40 font-semibold text-sm transition"
                    >
                        <Users class="w-4 h-4" />
                        <span>Generate Sample Learner</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <BookOpen class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ courses.length }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Total Courses Owned</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <Users class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ practice_learners.length }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Sandbox Test Learners</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <ClipboardCheck class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ pending_submissions.length }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Submissions Awaiting Grading</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <GraduationCap class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ day8_progress.percentage }}%</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">TVET Practice Competency</div>
                </div>
            </div>
        </div>

        <!-- TVET Pedagogy & Instructional Design Studio Quick Hub (Days 9 & 10) -->
        <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-white shadow-xl border border-rose-500/30 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 text-xs font-bold uppercase tracking-wider mb-2 border border-rose-500/30">
                    <Sparkles class="w-3.5 h-3.5" />
                    <span>Days 9 &amp; 10 TVET Pedagogy Studio</span>
                </div>
                <h3 class="text-xl font-bold text-white">Competency-Based Technical Lesson &amp; Inclusive Designer</h3>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                    Author practical technical lessons aligned with official NBTE standards, plan 4-step demonstrations, apply UDL principles, design 3-tier activities, and schedule workshop equipment rotations.
                </p>
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <Link
                        :href="route('pedagogy.studio')"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs transition shadow-sm"
                    >
                        <Layers class="w-3.5 h-3.5" />
                        <span>Studio Overview</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 font-semibold text-xs border border-amber-500/30 transition"
                    >
                        <Compass class="w-3.5 h-3.5" />
                        <span>Day 9 Lesson Builder</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.inclusive-designer.index')"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 font-semibold text-xs border border-rose-500/30 transition"
                    >
                        <Wand2 class="w-3.5 h-3.5" />
                        <span>Day 10 Inclusive Designer</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.workshop-planner')"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition"
                    >
                        <Settings2 class="w-3.5 h-3.5" />
                        <span>Workshop Planner</span>
                    </Link>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <Link
                    :href="route('portfolio.index')"
                    class="p-4 rounded-xl bg-slate-800/80 border border-slate-700 text-center text-xs hover:border-teal-500/50 transition w-full sm:w-auto"
                >
                    <Briefcase class="w-6 h-6 mx-auto text-teal-400 mb-1" />
                    <div class="font-bold text-white">Teaching Portfolio</div>
                    <div class="text-[10px] text-slate-400">View Verified Artifacts</div>
                </Link>
            </div>
        </div>

        <!-- Main Dashboard Split Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: My Courses -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>My Courses</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ courses.length }}
                        </span>
                    </h3>
                    <Link :href="route('courses.index')" class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-semibold flex items-center space-x-1">
                        <span>View All Catalogue</span>
                        <ChevronRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div v-if="courses.length === 0" class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <BookOpen class="w-12 h-12 mx-auto text-slate-400 mb-3" />
                    <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">No courses created yet</h4>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Begin Step 1 of your IDEAS-TVET training by creating your practice course in the studio.
                    </p>
                    <Link
                        :href="route('courses.create')"
                        class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700 transition"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create Practice Course</span>
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span
                                    :class="[
                                        'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full',
                                        course.is_practice
                                            ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                            : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                                    ]"
                                >
                                    {{ course.is_practice ? 'Mode A · Practice Sandbox' : 'Mode B · Institutional' }}
                                </span>

                                <span
                                    :class="[
                                        'text-[10px] font-semibold px-2 py-0.5 rounded-full',
                                        course.status === 'published'
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'
                                            : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                    ]"
                                >
                                    {{ course.status }}
                                </span>
                            </div>

                            <h4 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-2">
                                {{ course.title }}
                            </h4>
                            <div class="text-xs text-teal-600 dark:text-teal-400 font-medium mt-1">
                                {{ course.category?.name }} · {{ course.code }}
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2">
                                {{ course.summary || 'No summary provided.' }}
                            </p>

                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ course.sections_count || course.sections?.length || 0 }} Sections</span>
                                <span>{{ course.assignments_count || 0 }} Assignments</span>
                                <span>{{ course.students_count || 0 }} Learners</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            <Link
                                :href="route('courses.builder', course.id)"
                                class="flex-1 text-center py-2 px-3 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 hover:bg-teal-100 text-xs font-bold transition flex items-center justify-center space-x-1"
                            >
                                <Wand2 class="w-3.5 h-3.5" />
                                <span>Visual Builder</span>
                            </Link>

                            <Link
                                :href="route('courses.preview', course.id)"
                                class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 transition"
                                title="Preview as Student"
                            >
                                <Eye class="w-4 h-4" />
                            </Link>

                            <Link
                                :href="route('gradebook.show', course.id)"
                                class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 transition"
                                title="Course Gradebook"
                            >
                                <Table class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Pending Submissions & Sandbox Learners -->
            <div class="space-y-6">
                <!-- Official Admin Documents & PDFs for Teachers -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <FileDown class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                            <span>Official Documents &amp; PDFs</span>
                        </h3>
                        <Link
                            :href="route('teacher-documents.index')"
                            class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-semibold"
                        >
                            View All &rarr;
                        </Link>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                        Curriculum guidelines and manuals uploaded by admin for offline reference.
                    </p>

                    <!-- Quick Official Guide Downloads -->
                    <div class="space-y-2 mb-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <a
                            :href="route('user.guide.download-word')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 hover:bg-teal-100 dark:hover:bg-teal-900/40 transition group text-xs"
                            title="Download Full LMS User & Teacher Guide (Word .docx)"
                        >
                            <div class="flex items-center space-x-2 min-w-0">
                                <BookMarked class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" />
                                <span class="font-bold text-teal-950 dark:text-teal-200 truncate">Complete LMS User Guide (.docx)</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-teal-600 text-white font-semibold text-[11px] shrink-0 ml-2 shadow-xs group-hover:bg-teal-700">
                                <Download class="w-3 h-3 mr-1" />
                                Download
                            </span>
                        </a>

                        <a
                            :href="route('day8.guide.download-word')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition group text-xs"
                            title="Download Day 8 AI-Assisted Lesson Guide (Word .docx)"
                        >
                            <div class="flex items-center space-x-2 min-w-0">
                                <FileText class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
                                <span class="font-bold text-amber-950 dark:text-amber-200 truncate">Day 8 Practical Guide (.docx)</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-amber-600 text-white font-semibold text-[11px] shrink-0 ml-2 shadow-xs group-hover:bg-amber-700">
                                <Download class="w-3 h-3 mr-1" />
                                Download
                            </span>
                        </a>
                    </div>

                    <div v-if="!recent_documents || recent_documents.length === 0" class="text-center py-4 text-xs text-slate-400">
                        No documents published yet.
                    </div>

                    <div v-else class="space-y-2.5">
                        <div
                            v-for="doc in recent_documents"
                            :key="doc.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-2"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate" :title="doc.title">
                                    {{ doc.title }}
                                </div>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded font-bold uppercase text-[9px] bg-rose-500/15 text-rose-600 dark:text-rose-400">
                                        {{ doc.file_extension }}
                                    </span>
                                    <span>{{ doc.formatted_file_size }}</span>
                                    <span>&bull;</span>
                                    <span>{{ doc.category }}</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1 shrink-0">
                                <a
                                    :href="route('teacher-documents.preview', doc.id)"
                                    target="_blank"
                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition"
                                    title="Preview in browser"
                                >
                                    <Eye class="w-3.5 h-3.5" />
                                </a>
                                <a
                                    :href="route('teacher-documents.download', doc.id)"
                                    class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-bold shadow-xs transition"
                                    title="Download PDF directly to system"
                                >
                                    <Download class="w-3 h-3" />
                                    <span>Download PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <Link
                            :href="route('teacher-documents.index')"
                            class="w-full inline-flex items-center justify-center space-x-2 py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 transition"
                        >
                            <FileDown class="w-3.5 h-3.5 text-teal-500" />
                            <span>Browse All Teacher Documents &rarr;</span>
                        </Link>
                    </div>
                </div>

                <!-- Pending Submissions -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center justify-between mb-4">
                        <span class="flex items-center space-x-2">
                            <ClipboardCheck class="w-4 h-4 text-teal-600" />
                            <span>Pending Grading</span>
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-700 font-semibold">
                            {{ pending_submissions.length }}
                        </span>
                    </h3>

                    <div v-if="pending_submissions.length === 0" class="text-center py-6 text-xs text-slate-400">
                        No submissions awaiting grading.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="sub in pending_submissions"
                            :key="sub.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs"
                        >
                            <div class="font-bold text-slate-800 dark:text-slate-200">
                                {{ sub.student?.name }}
                            </div>
                            <div class="text-slate-500 mt-0.5">
                                {{ sub.assignment?.title }}
                            </div>
                            <div class="mt-2 flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700">
                                <span class="text-[10px] text-amber-600 font-medium">Ready for rubric</span>
                                <Link
                                    :href="route('assignments.show', sub.assignment_id)"
                                    class="text-teal-600 dark:text-teal-400 font-bold hover:underline"
                                >
                                    Grade Now &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Synthetic Practice Learners -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center justify-between mb-2">
                        <span class="flex items-center space-x-2">
                            <Users class="w-4 h-4 text-teal-600" />
                            <span>Isolated Sandbox Learners</span>
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ practice_learners.length }}
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Synthetic learners generated for your private sandbox to test submissions and rubric scoring.
                    </p>

                    <div v-if="practice_learners.length === 0" class="text-center py-4 text-xs text-slate-400">
                        No sandbox learners generated yet. Click above to create one.
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="learner in practice_learners"
                            :key="learner.id"
                            class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 text-xs flex items-center justify-between"
                        >
                            <div>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ learner.name }}</span>
                                <div class="text-[10px] text-slate-400">{{ learner.email }}</div>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                Synthetic
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
