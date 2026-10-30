<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    CheckCircle2,
    XCircle,
    Award,
    Printer,
    ArrowLeft,
    Clock,
    BookOpen,
    HelpCircle,
    Layers,
    User,
    Building2,
    MapPin,
    Wrench,
    CheckSquare,
    ChevronDown,
    ChevronUp,
    BarChart3
} from 'lucide-vue-next';

const props = defineProps({
    attempt: {
        type: Object,
        required: true,
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const filterReview = ref('all'); // 'all', 'incorrect', 'correct'
const expandedQuestions = ref(new Set());

const toggleExpand = (qId) => {
    if (expandedQuestions.value.has(qId)) {
        expandedQuestions.value.delete(qId);
    } else {
        expandedQuestions.value.add(qId);
    }
};

const expandAll = () => {
    props.attempt.answers.forEach(a => expandedQuestions.value.add(a.question_id));
};

const collapseAll = () => {
    expandedQuestions.value.clear();
};

const filteredAnswers = computed(() => {
    if (!props.attempt.answers) return [];
    if (filterReview.value === 'incorrect') {
        return props.attempt.answers.filter(a => !a.is_correct);
    }
    if (filterReview.value === 'correct') {
        return props.attempt.answers.filter(a => a.is_correct);
    }
    return props.attempt.answers;
});
</script>

<template>
    <Head :title="`Evaluation Result - ${attempt.quiz?.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('quizzes.index')"
                        class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-slate-600 dark:text-slate-300 transition"
                    >
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            Competency Evaluation Result
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ attempt.quiz?.title }} • Submitted {{ new Date(attempt.completed_at).toLocaleDateString() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('quizzes.attempt.print', attempt.id)"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4" />
                        <span>Print Official Evaluation Report</span>
                    </Link>

                    <Link
                        v-if="canManage"
                        :href="route('admin.quizzes.analytics')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <BarChart3 class="w-4 h-4" />
                        <span>Analytics Hub</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
            <!-- ========================================================= -->
            <!-- Master Scorecard Banner -->
            <!-- ========================================================= -->
            <div
                class="rounded-3xl border shadow-sm p-6 sm:p-8 relative overflow-hidden"
                :class="attempt.passed
                    ? 'bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-white dark:to-slate-900 border-emerald-500/30'
                    : 'bg-gradient-to-br from-rose-500/10 via-amber-500/5 to-white dark:to-slate-900 border-rose-500/30'"
            >
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Left: Teacher Meta & Status -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-3 py-1 rounded-xl font-bold text-xs uppercase tracking-wider inline-flex items-center space-x-1.5"
                                :class="attempt.passed
                                    ? 'bg-emerald-500 text-white'
                                    : 'bg-rose-500 text-white'"
                            >
                                <CheckCircle2 v-if="attempt.passed" class="w-3.5 h-3.5" />
                                <XCircle v-else class="w-3.5 h-3.5" />
                                <span>{{ attempt.passed ? 'COMPETENCY DEMONSTRATED (PASSED)' : 'REQUIRES DEVELOPMENTAL SUPPORT' }}</span>
                            </span>

                            <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700">
                                Grade: {{ attempt.grade }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ attempt.user?.name }}
                            </h3>
                            <div class="pt-1.5 flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-600 dark:text-slate-300">
                                <span class="flex items-center space-x-1">
                                    <Building2 class="w-3.5 h-3.5 text-teal-500" />
                                    <span>{{ attempt.user?.institution || 'Technical College' }}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <MapPin class="w-3.5 h-3.5 text-amber-500" />
                                    <span>{{ attempt.user?.state || 'State' }} State</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <Wrench class="w-3.5 h-3.5 text-blue-500" />
                                    <span>{{ attempt.user?.trade_specialization || 'TVET Educator' }}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <Clock class="w-3.5 h-3.5 text-purple-500" />
                                    <span>Time Spent: {{ attempt.formatted_duration }}</span>
                                </span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed pt-1">
                            Assessed on 50 National Occupational Standard MCQs across Pedagogy, Lesson Planning, Workshop Safety, Rubrics &amp; Inclusive Digital Education.
                        </p>
                    </div>

                    <!-- Right: Large Score & Metrics -->
                    <div class="lg:col-span-5 flex flex-col sm:flex-row items-center justify-around gap-4 p-5 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border border-slate-200/80 dark:border-slate-700/80">
                        <!-- Score Circle -->
                        <div class="text-center">
                            <span class="text-xs uppercase tracking-wider font-bold text-slate-400">Total Score</span>
                            <div class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white mt-1">
                                {{ attempt.score }}<span class="text-xl sm:text-2xl text-slate-400 font-normal">/{{ attempt.max_score }}</span>
                            </div>
                            <span class="text-xs font-bold" :class="attempt.passed ? 'text-emerald-500' : 'text-rose-500'">
                                {{ attempt.percentage }}% Overall
                            </span>
                        </div>

                        <!-- Mini Breakdown -->
                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-slate-700 pt-3 sm:pt-0 sm:pl-5">
                            <div class="flex items-center justify-between space-x-4">
                                <span class="flex items-center space-x-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Correct:</span>
                                </span>
                                <strong class="font-bold text-slate-900 dark:text-white">{{ attempt.correct_answers_count }}</strong>
                            </div>
                            <div class="flex items-center justify-between space-x-4">
                                <span class="flex items-center space-x-1 text-rose-500 font-semibold">
                                    <XCircle class="w-3.5 h-3.5" />
                                    <span>Incorrect:</span>
                                </span>
                                <strong class="font-bold text-slate-900 dark:text-white">{{ attempt.incorrect_answers_count }}</strong>
                            </div>
                            <div class="flex items-center justify-between space-x-4">
                                <span class="flex items-center space-x-1 text-slate-400 font-semibold">
                                    <HelpCircle class="w-3.5 h-3.5" />
                                    <span>Unanswered:</span>
                                </span>
                                <strong class="font-bold text-slate-900 dark:text-white">{{ attempt.unanswered_count }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Module-by-Module Competency Breakdown -->
            <!-- ========================================================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-2">
                        <Layers class="w-4 h-4 text-teal-500" />
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Category-Wise Performance Analysis
                        </h3>
                    </div>
                    <span class="text-xs text-slate-500">5 Distinct TVET Competency Domains</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="cat in (attempt.category_scores || [])"
                        :key="cat.category"
                        class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-2.5"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate pr-2">
                                {{ cat.category }}
                            </span>
                            <span
                                class="text-xs font-black"
                                :class="cat.percentage >= 60 ? 'text-teal-600 dark:text-teal-400' : 'text-rose-500'"
                            >
                                {{ cat.percentage }}%
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div
                                class="h-2 rounded-full transition-all duration-500"
                                :class="cat.percentage >= 80
                                    ? 'bg-emerald-500'
                                    : cat.percentage >= 60
                                        ? 'bg-teal-500'
                                        : 'bg-rose-500'"
                                :style="{ width: `${cat.percentage}%` }"
                            ></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Score: {{ cat.score }} / {{ cat.max_score }}</span>
                            <span>{{ cat.correct }} of {{ cat.total }} correct</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Full 50 Questions Detailed Review & Explanations -->
            <!-- ========================================================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            Question-by-Question Review &amp; Rationale
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Detailed pedagogical explanations for learning reinforcement
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Filter Tabs -->
                        <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-semibold">
                            <button
                                @click="filterReview = 'all'"
                                class="px-2.5 py-1 rounded-lg transition"
                                :class="filterReview === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            >
                                All ({{ attempt.answers?.length || 0 }})
                            </button>
                            <button
                                @click="filterReview = 'incorrect'"
                                class="px-2.5 py-1 rounded-lg transition text-rose-600 dark:text-rose-400"
                                :class="filterReview === 'incorrect' ? 'bg-white dark:bg-slate-700 shadow-xs font-bold' : 'opacity-70 hover:opacity-100'"
                            >
                                Incorrect ({{ attempt.incorrect_answers_count }})
                            </button>
                            <button
                                @click="filterReview = 'correct'"
                                class="px-2.5 py-1 rounded-lg transition text-emerald-600 dark:text-emerald-400"
                                :class="filterReview === 'correct' ? 'bg-white dark:bg-slate-700 shadow-xs font-bold' : 'opacity-70 hover:opacity-100'"
                            >
                                Correct ({{ attempt.correct_answers_count }})
                            </button>
                        </div>

                        <!-- Expand/Collapse All -->
                        <button
                            @click="expandAll"
                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium"
                        >
                            Expand All
                        </button>
                        <button
                            @click="collapseAll"
                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium"
                        >
                            Collapse
                        </button>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="space-y-3">
                    <div
                        v-for="ans in filteredAnswers"
                        :key="ans.question_id"
                        class="rounded-xl border transition overflow-hidden"
                        :class="ans.is_correct
                            ? 'border-emerald-500/30 bg-emerald-500/5'
                            : 'border-rose-500/30 bg-rose-500/5'"
                    >
                        <!-- Item Header / Clickable to toggle -->
                        <div
                            @click="toggleExpand(ans.question_id)"
                            class="p-4 flex items-center justify-between cursor-pointer select-none"
                        >
                            <div class="flex items-start space-x-3 pr-4">
                                <div class="mt-0.5 shrink-0">
                                    <CheckCircle2 v-if="ans.is_correct" class="w-5 h-5 text-emerald-500" />
                                    <XCircle v-else class="w-5 h-5 text-rose-500" />
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" :class="ans.is_correct ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400'">
                                            Question {{ ans.question_number }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-medium">
                                            • {{ ans.category }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 line-clamp-1">
                                        {{ ans.question_text }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0">
                                <span
                                    class="text-xs font-bold px-2 py-0.5 rounded-lg"
                                    :class="ans.is_correct
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300'
                                        : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300'"
                                >
                                    {{ ans.points_awarded }} / {{ ans.points_possible }} pt
                                </span>
                                <ChevronUp v-if="expandedQuestions.has(ans.question_id)" class="w-4 h-4 text-slate-400" />
                                <ChevronDown v-else class="w-4 h-4 text-slate-400" />
                            </div>
                        </div>

                        <!-- Expanded Question Details -->
                        <div
                            v-if="expandedQuestions.has(ans.question_id)"
                            class="px-4 pb-4 pt-1 border-t border-slate-200/60 dark:border-slate-800 space-y-3 text-xs"
                        >
                            <!-- Full Question Text -->
                            <p class="text-sm font-medium text-slate-900 dark:text-white leading-relaxed">
                                {{ ans.question_text }}
                            </p>

                            <!-- Trainee Selection vs Correct Option -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <!-- Trainee's Choice -->
                                <div
                                    class="p-3 rounded-xl border"
                                    :class="ans.is_correct
                                        ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500/40 text-emerald-900 dark:text-emerald-200'
                                        : 'bg-rose-50 dark:bg-rose-950/40 border-rose-500/40 text-rose-900 dark:text-rose-200'"
                                >
                                    <span class="font-bold text-[11px] block uppercase tracking-wider mb-1">
                                        Your Choice: (Option {{ (ans.selected_option || 'None').toUpperCase() }})
                                    </span>
                                    <p class="font-medium text-xs leading-relaxed">
                                        {{ ans.selected_text }}
                                    </p>
                                </div>

                                <!-- Correct Option -->
                                <div class="p-3 rounded-xl border bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500/40 text-emerald-900 dark:text-emerald-200">
                                    <span class="font-bold text-[11px] block uppercase tracking-wider mb-1 text-emerald-700 dark:text-emerald-400">
                                        Correct Answer: (Option {{ ans.correct_option.toUpperCase() }})
                                    </span>
                                    <p class="font-medium text-xs leading-relaxed">
                                        {{ ans.correct_text }}
                                    </p>
                                </div>
                            </div>

                            <!-- Explanation / Learning Rationale -->
                            <div v-if="ans.explanation" class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-700 dark:text-slate-300 space-y-1">
                                <span class="font-bold text-[11px] text-teal-600 dark:text-teal-400 block uppercase tracking-wider">
                                    Pedagogical Rationale:
                                </span>
                                <p class="leading-relaxed">
                                    {{ ans.explanation }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
