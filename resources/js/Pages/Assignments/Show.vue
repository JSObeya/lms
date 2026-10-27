<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ClipboardList,
    Clock,
    Award,
    UploadCloud,
    Download,
    CheckCircle2,
    Calendar,
    ArrowLeft,
    CheckCheck,
    MessageSquare,
    ExternalLink,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    assignment: Object,
    my_submission: Object,
    can_grade: Boolean,
    is_teacher: Boolean,
});

// Student Submission Form
const submitForm = useForm({
    online_text: props.my_submission?.online_text || '',
    submission_url: props.my_submission?.submission_url || '',
    files: [],
});

const submitAssignment = () => {
    submitForm.post(route('submissions.store', props.assignment.id), {
        onSuccess: () => {
            submitForm.reset('files');
        },
    });
};

// Teacher Rubric Grading Modal State
const gradingSubmission = ref(null);
const showGradingModal = ref(false);

const rubricForm = useForm({
    rubric_id: props.assignment.rubric_id,
    scores: {}, // criterion_id => { level_id: x, points: y, comment: '' }
    general_feedback: '',
    is_released: true,
});

const openGradingModal = (submission) => {
    gradingSubmission.value = submission;
    rubricForm.rubric_id = props.assignment.rubric_id;
    rubricForm.general_feedback = submission.feedback || '';
    rubricForm.is_released = submission.grade_released ?? true;
    rubricForm.scores = {};

    // Initialize scores from existing rubric assessment or defaults
    if (props.assignment.rubric?.criteria) {
        props.assignment.rubric.criteria.forEach((crit) => {
            const existingItem = submission.rubric_assessment?.items?.find(
                (i) => i.rubric_criterion_id === crit.id
            );
            rubricForm.scores[crit.id] = {
                level_id: existingItem ? existingItem.rubric_level_id : (crit.levels[0]?.id || null),
                points: existingItem ? existingItem.awarded_points : (crit.levels[0]?.points || 0),
                comment: existingItem ? existingItem.assessor_comment : '',
            };
        });
    }

    showGradingModal.value = true;
};

const selectLevel = (criterionId, level) => {
    if (!rubricForm.scores[criterionId]) {
        rubricForm.scores[criterionId] = {};
    }
    rubricForm.scores[criterionId].level_id = level.id;
    rubricForm.scores[criterionId].points = level.points;
};

const calculateTotalScore = () => {
    let total = 0;
    for (const key in rubricForm.scores) {
        total += parseFloat(rubricForm.scores[key].points || 0);
    }
    return total;
};

const submitGrading = () => {
    if (!gradingSubmission.value) return;
    rubricForm.post(route('submissions.grade-rubric', gradingSubmission.value.id), {
        onSuccess: () => {
            showGradingModal.value = false;
        },
    });
};
</script>

<template>
    <Head :title="assignment.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Link
                    :href="route('courses.show', assignment.course_id)"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100"
                >
                    <ArrowLeft class="w-4 h-4 text-slate-500" />
                </Link>
                <div>
                    <div class="text-xs text-teal-600 font-bold uppercase tracking-wider">
                        {{ assignment.course?.title }} · {{ assignment.section?.title || 'Course Assessment' }}
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ assignment.title }}
                    </h1>
                </div>
            </div>
        </template>

        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Assignment Brief & Instructions -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center space-x-4 text-xs text-slate-500">
                        <span class="flex items-center space-x-1">
                            <Award class="w-4 h-4 text-teal-600" />
                            <span>Max Points: <strong>{{ assignment.max_score }}</strong></span>
                        </span>
                        <span>·</span>
                        <span class="flex items-center space-x-1">
                            <Clock class="w-4 h-4 text-amber-500" />
                            <span>Due Date: <strong>{{ assignment.due_date ? new Date(assignment.due_date).toLocaleString() : 'No deadline' }}</strong></span>
                        </span>
                    </div>

                    <span
                        v-if="assignment.rubric"
                        class="text-xs font-semibold px-2.5 py-1 rounded-full bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300"
                    >
                        Rubric Assessed ({{ assignment.rubric.total_points }} pts)
                    </span>
                </div>

                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Practical Instructions</h2>
                    <div class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                        {{ assignment.instructions }}
                    </div>
                </div>
            </div>

            <!-- Learner Rubric Guidance Table -->
            <div v-if="assignment.rubric?.criteria?.length > 0" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                    <Award class="w-5 h-5 text-teal-600" />
                    <span>Grading Rubric Criteria</span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-slate-200 dark:border-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-slate-500">
                            <tr>
                                <th class="p-3 border-b">Criterion</th>
                                <th class="p-3 border-b">Max Points</th>
                                <th class="p-3 border-b">Achievement Standards</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="crit in assignment.rubric.criteria" :key="crit.id">
                                <td class="p-3 font-bold text-slate-900 dark:text-white align-top w-48">
                                    {{ crit.title }}
                                    <div class="text-[11px] text-slate-400 font-normal mt-0.5">{{ crit.description }}</div>
                                </td>
                                <td class="p-3 font-black text-teal-600 align-top w-24">
                                    {{ crit.max_points }} pts
                                </td>
                                <td class="p-3 align-top">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                                        <div
                                            v-for="lvl in crit.levels"
                                            :key="lvl.id"
                                            class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700"
                                        >
                                            <div class="font-bold text-[11px] text-slate-800 dark:text-slate-200">
                                                {{ lvl.title }} ({{ lvl.points }} pts)
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-1">{{ lvl.descriptor }}</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION A: Student Submission Box (if student or has submission) -->
            <div v-if="!can_grade || my_submission" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                    <UploadCloud class="w-5 h-5 text-teal-600" />
                    <span>My Assignment Submission</span>
                </h2>

                <!-- If already submitted & graded -->
                <div v-if="my_submission && my_submission.grade_released" class="p-5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-teal-700 dark:text-teal-300 font-semibold uppercase">Official Assessment Score</div>
                            <div class="text-2xl font-black text-teal-900 dark:text-teal-100 mt-0.5">
                                {{ my_submission.final_score }} / {{ assignment.max_score }}
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">
                            Graded
                        </span>
                    </div>

                    <div v-if="my_submission.feedback" class="mt-4 pt-3 border-t border-teal-200/60 text-xs text-slate-700 dark:text-slate-300">
                        <strong>Assessor Feedback:</strong> {{ my_submission.feedback }}
                    </div>
                </div>

                <!-- Submission Form -->
                <form v-if="!my_submission || !my_submission.grade_released" @submit.prevent="submitAssignment" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Online Text Response
                        </label>
                        <textarea
                            v-model="submitForm.online_text"
                            rows="4"
                            placeholder="Enter your inspection observations, calculations, or answer details..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Attach Report Files (PDF, DOCX max 15MB)
                        </label>
                        <input
                            type="file"
                            multiple
                            @input="submitForm.files = $event.target.files"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"
                        />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="submitForm.processing"
                            class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow transition"
                        >
                            {{ my_submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION B: Teacher / Facilitator Submissions Table -->
            <div v-if="can_grade" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <CheckCheck class="w-5 h-5 text-teal-600" />
                            <span>Learner Submissions ({{ assignment.submissions?.length || 0 }})</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Evaluate submissions using the attached visual rubric.</p>
                    </div>

                    <Link
                        :href="route('gradebook.show', assignment.course_id)"
                        class="text-xs font-bold text-teal-600 hover:underline"
                    >
                        View Full Gradebook &rarr;
                    </Link>
                </div>

                <div v-if="assignment.submissions?.length === 0" class="text-center py-8 text-xs text-slate-400">
                    No submissions received yet for this assignment.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-slate-500">
                            <tr>
                                <th class="p-3">Student</th>
                                <th class="p-3">Submission Date</th>
                                <th class="p-3">Score</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="sub in assignment.submissions" :key="sub.id" class="hover:bg-slate-50/50">
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ sub.student?.name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ sub.student?.email }}</div>
                                </td>
                                <td class="p-3 text-slate-500">
                                    {{ sub.submitted_at ? new Date(sub.submitted_at).toLocaleString() : 'Draft' }}
                                </td>
                                <td class="p-3">
                                    <span v-if="sub.final_score !== null" class="font-black text-teal-600 text-sm">
                                        {{ sub.final_score }} / {{ assignment.max_score }}
                                    </span>
                                    <span v-else class="text-slate-400">Ungraded</span>
                                </td>
                                <td class="p-3">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full font-semibold text-[10px]',
                                            sub.status === 'graded' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                                        ]"
                                    >
                                        {{ sub.status }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <button
                                        @click="openGradingModal(sub)"
                                        class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs"
                                    >
                                        {{ sub.status === 'graded' ? 'Regrade with Rubric' : 'Grade with Rubric' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Visual Rubric Grading Modal -->
        <div v-if="showGradingModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Rubric Evaluation: {{ gradingSubmission?.student?.name }}
                        </h3>
                        <p class="text-xs text-slate-500">
                            Select the achievement level for each TVET criterion. Score is automatically calculated.
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400 uppercase font-bold">Total Score</div>
                        <div class="text-2xl font-black text-teal-600">
                            {{ calculateTotalScore() }} / {{ assignment.max_score }}
                        </div>
                    </div>
                </div>

                <!-- Scrollable Rubric Body -->
                <div class="flex-1 overflow-y-auto py-4 space-y-6">
                    <!-- Student Work Preview -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 text-xs">
                        <div class="font-bold text-slate-800 dark:text-slate-200 mb-1">Student Online Submission Text:</div>
                        <p class="text-slate-600 dark:text-slate-300 whitespace-pre-line">
                            {{ gradingSubmission?.online_text || 'No text submitted.' }}
                        </p>
                    </div>

                    <!-- Criteria Scoring -->
                    <div v-for="crit in assignment.rubric?.criteria" :key="crit.id" class="space-y-2 border-b border-slate-100 pb-4">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 dark:text-white">{{ crit.title }}</span>
                            <span class="text-xs text-slate-500 font-semibold">Max: {{ crit.max_points }} pts</span>
                        </div>

                        <!-- Levels Selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                            <button
                                type="button"
                                v-for="lvl in crit.levels"
                                :key="lvl.id"
                                @click="selectLevel(crit.id, lvl)"
                                :class="[
                                    'p-3 rounded-xl border text-left transition text-xs',
                                    rubricForm.scores[crit.id]?.level_id === lvl.id
                                        ? 'bg-teal-50 dark:bg-teal-950/60 border-teal-500 ring-2 ring-teal-500/20'
                                        : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:bg-slate-50'
                                ]"
                            >
                                <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                                    <span>{{ lvl.title }}</span>
                                    <span class="text-teal-600 font-black">{{ lvl.points }} pts</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1 leading-snug">{{ lvl.descriptor }}</div>
                            </button>
                        </div>
                    </div>

                    <!-- General Assessor Feedback -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            General Assessor Feedback &amp; Recommendations
                        </label>
                        <textarea
                            v-model="rubricForm.general_feedback"
                            rows="3"
                            placeholder="Provide constructive technical feedback to help the learner improve their practical trade skills..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        ></textarea>
                    </div>

                    <!-- Release Toggle -->
                    <div class="flex items-center space-x-2">
                        <input
                            type="checkbox"
                            id="is_released"
                            v-model="rubricForm.is_released"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 h-4 w-4"
                        />
                        <label for="is_released" class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Release grade and feedback immediately to learner
                        </label>
                    </div>
                </div>

                <!-- Footer -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                    <button
                        type="button"
                        @click="showGradingModal = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitGrading"
                        :disabled="rubricForm.processing"
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow transition"
                    >
                        Save Rubric Evaluation ({{ calculateTotalScore() }} pts)
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
