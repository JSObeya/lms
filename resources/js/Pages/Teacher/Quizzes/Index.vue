<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    CheckSquare,
    Clock,
    Award,
    CheckCircle2,
    XCircle,
    ArrowRight,
    BarChart3,
    HelpCircle,
    BookOpen,
    Users,
    FileText,
    TrendingUp,
    Shield
} from 'lucide-vue-next';

const props = defineProps({
    quizzes: {
        type: Array,
        required: true,
    },
    isEvaluator: {
        type: Boolean,
        default: false,
    },
    evaluatorStats: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <Head title="MCQ Competency Assessment - TVET Technical Teachers" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-500/20">
                        <CheckSquare class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            MCQ Competency Assessment Engine
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            National TVET Technical Teacher Pedagogy &amp; Practical Competency Evaluation
                        </p>
                    </div>
                </div>

                <div v-if="isEvaluator" class="flex items-center space-x-2">
                    <Link
                        :href="route('admin.quizzes.analytics')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-sm transition"
                    >
                        <BarChart3 class="w-4 h-4" />
                        <span>Teacher Results Analytics &amp; Printing</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
            <!-- Evaluator Quick Stats Banner (Visible to Admins / Facilitators) -->
            <div
                v-if="isEvaluator && evaluatorStats"
                class="p-5 rounded-2xl bg-gradient-to-r from-purple-900/20 via-slate-900/40 to-slate-900/60 border border-purple-500/30 backdrop-blur-sm"
            >
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-purple-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-purple-500/20 text-purple-300 rounded-xl">
                            <TrendingUp class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Institutional Evaluation Overview</h3>
                            <p class="text-xs text-slate-400">Real-time teacher submission progress across all participating colleges</p>
                        </div>
                    </div>
                    <Link
                        :href="route('admin.quizzes.analytics')"
                        class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center space-x-1"
                    >
                        <span>Open Evaluation Dashboard</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                    <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                        <span class="text-xs text-slate-400">Total Submissions</span>
                        <p class="text-xl font-bold text-white mt-0.5">{{ evaluatorStats.total_submissions }}</p>
                    </div>
                    <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                        <span class="text-xs text-slate-400">Assessed Teachers</span>
                        <p class="text-xl font-bold text-teal-400 mt-0.5">{{ evaluatorStats.unique_teachers }}</p>
                    </div>
                    <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                        <span class="text-xs text-slate-400">Average Score</span>
                        <p class="text-xl font-bold text-amber-400 mt-0.5">{{ evaluatorStats.avg_percentage }}%</p>
                    </div>
                    <div class="p-3 bg-white/5 rounded-xl border border-white/5">
                        <span class="text-xs text-slate-400">Pass Rate</span>
                        <p class="text-xl font-bold text-emerald-400 mt-0.5">{{ evaluatorStats.pass_rate }}%</p>
                    </div>
                </div>
            </div>

            <!-- Quizzes Grid -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">
                        Available Competency Assessments
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Use your active LMS login credentials to take and record assessments
                    </span>
                </div>

                <div v-for="quiz in quizzes" :key="quiz.id" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:border-amber-500/40 transition">
                    <div class="p-6 md:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-3 max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg uppercase tracking-wider bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                    {{ quiz.category }}
                                </span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-500/20 flex items-center space-x-1">
                                    <HelpCircle class="w-3.5 h-3.5" />
                                    <span>{{ quiz.total_questions }} MCQs</span>
                                </span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20 flex items-center space-x-1">
                                    <Clock class="w-3.5 h-3.5" />
                                    <span>{{ quiz.time_limit_minutes }} Minutes</span>
                                </span>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    Pass Mark: {{ quiz.passing_percentage }}%
                                </span>
                            </div>

                            <h4 class="text-lg md:text-xl font-bold text-slate-900 dark:text-white leading-snug">
                                {{ quiz.title }}
                            </h4>

                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                {{ quiz.description }}
                            </p>

                            <!-- Past Attempt Status Banner -->
                            <div v-if="quiz.latest_attempt" class="pt-2 flex flex-wrap items-center gap-3 text-xs">
                                <div
                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl font-semibold"
                                    :class="quiz.latest_attempt.passed
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30'
                                        : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-500/30'"
                                >
                                    <CheckCircle2 v-if="quiz.latest_attempt.passed" class="w-4 h-4 text-emerald-500" />
                                    <XCircle v-else class="w-4 h-4 text-rose-500" />
                                    <span>
                                        Latest Result: {{ quiz.latest_attempt.score }}/{{ quiz.latest_attempt.max_score }}
                                        ({{ quiz.latest_attempt.percentage }}%) — {{ quiz.latest_attempt.grade }}
                                    </span>
                                </div>

                                <Link
                                    :href="route('quizzes.attempt', quiz.latest_attempt.id)"
                                    class="text-teal-600 dark:text-teal-400 hover:underline font-bold"
                                >
                                    View Past Breakdown &amp; Printable Report →
                                </Link>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 lg:w-56">
                            <Link
                                :href="route('quizzes.show', quiz.id)"
                                class="w-full inline-flex items-center justify-center space-x-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md hover:shadow-lg transition text-center"
                            >
                                <CheckSquare class="w-4 h-4" />
                                <span>{{ quiz.user_attempts_count > 0 ? 'Retake 50 MCQs' : 'Start 50 MCQs Now' }}</span>
                            </Link>

                            <Link
                                v-if="quiz.latest_attempt"
                                :href="route('quizzes.attempt.print', quiz.latest_attempt.id)"
                                class="w-full inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs border border-slate-200 dark:border-slate-700 transition"
                            >
                                <FileText class="w-3.5 h-3.5 text-teal-500" />
                                <span>Print Evaluation Slip</span>
                            </Link>
                        </div>
                    </div>

                    <!-- 5 Modules Pill Footer -->
                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold text-slate-700 dark:text-slate-300">Curriculum Scope:</span>
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">1. CBT &amp; Standards</span>
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">2. 4-Step Lesson Planning</span>
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">3. Workshop Safety &amp; 5S</span>
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">4. Rubrics &amp; Assessment</span>
                        <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">5. Digital &amp; Inclusive TVET</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
