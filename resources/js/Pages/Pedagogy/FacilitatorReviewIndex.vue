<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Award,
    CheckCircle2,
    AlertCircle,
    Edit3,
    Printer,
    FileText,
    Wand2,
    Compass,
    X,
    Send,
    MessageSquare,
    RotateCcw
} from 'lucide-vue-next';

const props = defineProps({
    reviews: Object,
});

const selectedReview = ref(null);

const formReview = useForm({
    module_type: 'day10',
    status: 'approved',
    rubric_scores: [
        { criterion: 'Demonstration and modeling clarity', score: 20, max_score: 20 },
        { criterion: '3-tier learner differentiation', score: 20, max_score: 20 },
        { criterion: 'Universal Design for Learning (UDL) principles', score: 20, max_score: 20 },
        { criterion: 'Occupational safety standards preservation', score: 20, max_score: 20 },
        { criterion: 'Workshop rotation and accommodation feasibility', score: 20, max_score: 20 },
    ],
    total_score: 100,
    written_feedback: '',
    revision_notes: '',
});

const openGradingModal = (review) => {
    selectedReview.value = review;
    formReview.module_type = review.module_type;
    formReview.status = 'approved';
    formReview.written_feedback = review.written_feedback || 'Excellent competency-based technical design with comprehensive differentiation and uncompromised safety standards.';
    formReview.revision_notes = review.revision_notes || '';

    // Calculate score
    computeTotal();
};

const computeTotal = () => {
    formReview.total_score = formReview.rubric_scores.reduce((sum, item) => sum + (Number(item.score) || 0), 0);
};

const submitGrading = () => {
    if (!selectedReview.value) return;
    formReview.post(route('facilitator.pedagogy-reviews.store', selectedReview.value.competency_lesson_id), {
        onSuccess: () => {
            selectedReview.value = null;
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Facilitator Pedagogy Assessment Portal — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-bold uppercase tracking-wider mb-2 border border-purple-500/20">
                        <Award class="w-3.5 h-3.5" />
                        <span>Facilitator Assessment Suite</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                        Facilitator Review Portal: Days 9 &amp; 10 Submissions
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Review, rubric-grade, and provide formative technical feedback on trainee teachers' competency-based lessons and inclusive adaptations.
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Review Queue Table -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>Submitted Lessons Queue</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ reviews.data ? reviews.data.length : 0 }} Records
                        </span>
                    </h2>
                </div>

                <div v-if="!reviews.data || reviews.data.length === 0" class="py-12 text-center text-slate-500">
                    <Award class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" />
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">No pending submissions awaiting review</h4>
                    <p class="text-xs text-slate-400 mt-1">When trainee teachers submit their Day 9 or Day 10 lessons, they will appear here for grading.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-semibold border-y border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="p-3">Teacher / Trainee</th>
                                <th class="p-3">Lesson &amp; Occupational Task</th>
                                <th class="p-3">Module Type</th>
                                <th class="p-3">Review Status</th>
                                <th class="p-3">Score</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="rev in reviews.data" :key="rev.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ rev.teacher ? rev.teacher.name : 'Trainee' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ rev.teacher ? rev.teacher.email : '' }}</div>
                                    <div class="text-[10px] text-teal-600 font-semibold">{{ rev.teacher ? rev.teacher.institution : '' }}</div>
                                </td>
                                <td class="p-3 max-w-xs">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                        {{ rev.competency_lesson ? rev.competency_lesson.title : 'Technical Lesson' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 line-clamp-1">
                                        {{ rev.competency_lesson ? rev.competency_lesson.occupational_task : '' }}
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span :class="[
                                        'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider',
                                        rev.module_type === 'day9' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300'
                                    ]">
                                        {{ rev.module_type === 'day9' ? 'Day 9 Core' : 'Day 10 Inclusive' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span :class="[
                                        'text-[10px] font-bold px-2 py-0.5 rounded-full capitalize',
                                        rev.status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' :
                                        rev.status === 'revisions_requested' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300' :
                                        'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300'
                                    ]">
                                        {{ rev.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold text-slate-800 dark:text-slate-200">
                                    {{ rev.total_score ? `${rev.total_score} / 100` : '—' }}
                                </td>
                                <td class="p-3 text-right">
                                    <div class="inline-flex items-center space-x-2">
                                        <Link
                                            :href="route('pedagogy.lessons.export', rev.competency_lesson_id)"
                                            class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                                            title="Inspect Formatted Lesson"
                                        >
                                            <Printer class="w-4 h-4" />
                                        </Link>
                                        <button
                                            type="button"
                                            @click="openGradingModal(rev)"
                                            class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs transition"
                                        >
                                            <Award class="w-3.5 h-3.5" />
                                            <span>Grade &amp; Feedback</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rubric Assessment & Feedback Modal -->
        <div v-if="selectedReview" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 my-8">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            Facilitator Rubric Assessment
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ selectedReview.competency_lesson ? selectedReview.competency_lesson.title : 'Technical Lesson' }}
                        </h3>
                        <div class="text-xs text-slate-500">
                            Teacher: {{ selectedReview.teacher ? selectedReview.teacher.name : '' }} · Module: {{ selectedReview.module_type.toUpperCase() }}
                        </div>
                    </div>
                    <button @click="selectedReview = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitGrading" class="space-y-4">
                    <!-- Rubric Criteria Scoring -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Rubric Performance Dimensions (100 Points Total)
                        </h4>

                        <div
                            v-for="(rub, idx) in formReview.rubric_scores"
                            :key="idx"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-4 text-xs"
                        >
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ rub.criterion }}</span>
                            <div class="flex items-center space-x-1 shrink-0">
                                <input
                                    v-model.number="rub.score"
                                    @input="computeTotal"
                                    type="number"
                                    min="0"
                                    :max="rub.max_score"
                                    class="w-16 text-xs text-right font-bold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1"
                                />
                                <span class="text-slate-400 font-bold">/ {{ rub.max_score }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 font-bold text-xs">
                            <span class="text-purple-900 dark:text-purple-200 uppercase tracking-wide">Total Calculated Score</span>
                            <span class="text-base text-purple-700 dark:text-purple-300">{{ formReview.total_score }} / 100</span>
                        </div>
                    </div>

                    <!-- Evaluation Decision -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Assessment Decision *
                        </label>
                        <select
                            v-model="formReview.status"
                            class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                        >
                            <option value="approved">Approve Competency Standard (Pass)</option>
                            <option value="revisions_requested">Request Revisions (Not Yet Competent)</option>
                        </select>
                    </div>

                    <!-- Written Technical Feedback -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Written Technical Feedback &amp; Commendations *
                        </label>
                        <textarea
                            v-model="formReview.written_feedback"
                            rows="3"
                            placeholder="Provide constructive TVET pedagogical feedback on the lesson's demonstration flow, safety precautions, and differentiation."
                            class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            required
                        ></textarea>
                    </div>

                    <!-- Revision Notes if applicable -->
                    <div v-if="formReview.status === 'revisions_requested'">
                        <label class="block text-xs font-bold uppercase tracking-wider text-rose-600 mb-1">
                            Required Revisions &amp; Resubmission Guidance
                        </label>
                        <textarea
                            v-model="formReview.revision_notes"
                            rows="2"
                            placeholder="Specify exactly what elements the teacher must adjust before resubmitting."
                            class="w-full text-xs rounded-xl border-rose-300 dark:border-rose-800 bg-white dark:bg-slate-800 text-rose-900 dark:text-rose-200"
                        ></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                        <button
                            type="button"
                            @click="selectedReview = null"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="formReview.processing"
                            class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm transition"
                        >
                            <Send class="w-3.5 h-3.5" />
                            <span>Save Evaluation &amp; Release Feedback</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
