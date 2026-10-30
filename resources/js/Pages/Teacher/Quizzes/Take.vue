<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import {
    Clock,
    CheckCircle2,
    AlertCircle,
    Flag,
    ArrowLeft,
    ArrowRight,
    Send,
    HelpCircle,
    ShieldAlert,
    CheckSquare,
    Layers,
    ListFilter
} from 'lucide-vue-next';

const props = defineProps({
    quiz: {
        type: Object,
        required: true,
    },
    questions: {
        type: Array,
        required: true,
    },
    previousAttempts: {
        type: Array,
        default: () => [],
    },
});

// Current question pointer (0-indexed: 0 to 49)
const currentIndex = ref(0);

// Answers dictionary: { question_id: 'a' | 'b' | 'c' | 'd' }
const answers = ref({});

// Flagged question IDs for later review
const flaggedQuestions = ref(new Set());

// Start timestamp
const startedAt = ref(new Date().toISOString());

// Countdown timer (in seconds)
const totalSeconds = (props.quiz.time_limit_minutes || 60) * 60;
const timeLeft = ref(totalSeconds);
let timerInterval = null;

// Submission Modal
const showConfirmModal = ref(false);
const isSubmitting = ref(false);

// Filter for Question Palette
const paletteFilter = ref('all'); // 'all', 'unanswered', 'flagged'

// Current question object
const currentQuestion = computed(() => props.questions[currentIndex.value] || {});

// Answered count
const answeredCount = computed(() => {
    return Object.keys(answers.value).filter(id => answers.value[id] !== null && answers.value[id] !== '').length;
});

const unansweredCount = computed(() => {
    return props.questions.length - answeredCount.value;
});

const progressPercentage = computed(() => {
    return props.questions.length > 0 ? Math.round((answeredCount.value / props.questions.length) * 100) : 0;
});

// Formatted timer string: MM:SS
const formattedTime = computed(() => {
    const mins = Math.floor(timeLeft.value / 60);
    const secs = timeLeft.value % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
});

const isTimeLow = computed(() => timeLeft.value <= 300); // 5 minutes or less

// Option letters & keys
const optionsList = [
    { key: 'a', label: 'A' },
    { key: 'b', label: 'B' },
    { key: 'c', label: 'C' },
    { key: 'd', label: 'D' },
];

const selectOption = (optKey) => {
    answers.value[currentQuestion.value.id] = optKey;
};

const toggleFlag = (questionId) => {
    if (flaggedQuestions.value.has(questionId)) {
        flaggedQuestions.value.delete(questionId);
    } else {
        flaggedQuestions.value.add(questionId);
    }
};

const isFlagged = (questionId) => flaggedQuestions.value.has(questionId);
const isAnswered = (questionId) => !!answers.value[questionId];

const goToQuestion = (index) => {
    if (index >= 0 && index < props.questions.length) {
        currentIndex.value = index;
    }
};

const nextQuestion = () => {
    if (currentIndex.value < props.questions.length - 1) {
        currentIndex.value++;
    }
};

const prevQuestion = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
};

const filteredPaletteQuestions = computed(() => {
    if (paletteFilter.value === 'unanswered') {
        return props.questions.filter(q => !answers.value[q.id]);
    }
    if (paletteFilter.value === 'flagged') {
        return props.questions.filter(q => flaggedQuestions.value.has(q.id));
    }
    return props.questions;
});

// Submit Quiz
const submitQuiz = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    router.post(route('quizzes.submit', props.quiz.id), {
        answers: answers.value,
        started_at: startedAt.value,
    }, {
        preserveScroll: false,
        onFinish: () => {
            isSubmitting.value = false;
            showConfirmModal.value = false;
        },
    });
};

onMounted(() => {
    // Start countdown
    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
        } else {
            clearInterval(timerInterval);
            // Time expired, auto-submit
            submitQuiz();
        }
    }, 1000);
});

onUnmounted(() => {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});
</script>

<template>
    <Head :title="`Taking Assessment: ${quiz.title}`" />

    <AuthenticatedLayout>
        <!-- Sticky Assessment Header -->
        <div class="sticky top-16 z-20 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <span class="p-2 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl">
                        <CheckSquare class="w-5 h-5" />
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate max-w-md sm:max-w-xl">
                            {{ quiz.title }}
                        </h2>
                        <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400">
                            <span>Question {{ currentIndex + 1 }} of {{ questions.length }}</span>
                            <span>•</span>
                            <span class="text-teal-600 dark:text-teal-400 font-semibold">{{ answeredCount }} Answered</span>
                            <span>•</span>
                            <span :class="unansweredCount > 0 ? 'text-amber-500 font-semibold' : 'text-emerald-500 font-semibold'">
                                {{ unansweredCount }} Remaining
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4 self-end md:self-auto">
                    <!-- Timer Pill -->
                    <div
                        class="px-3.5 py-1.5 rounded-xl font-mono text-sm font-bold flex items-center space-x-2 border shadow-xs"
                        :class="isTimeLow
                            ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30 animate-pulse'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700'"
                    >
                        <Clock class="w-4 h-4" />
                        <span>{{ formattedTime }}</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        @click="showConfirmModal = true"
                        class="inline-flex items-center space-x-2 px-5 py-2 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-bold text-xs rounded-xl shadow-sm transition"
                    >
                        <Send class="w-3.5 h-3.5" />
                        <span>Submit Exam</span>
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5">
                <div
                    class="bg-gradient-to-r from-teal-500 to-emerald-500 h-1.5 transition-all duration-300"
                    :style="{ width: `${progressPercentage}%` }"
                ></div>
            </div>
        </div>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- ========================================================= -->
                <!-- Main Question Area (8 Cols) -->
                <!-- ========================================================= -->
                <div class="lg:col-span-8 space-y-5">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
                        <!-- Question Header & Badges -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center space-x-2.5">
                                <span class="px-3 py-1 rounded-xl bg-teal-500/10 text-teal-700 dark:text-teal-400 font-bold text-xs border border-teal-500/20">
                                    Question #{{ currentQuestion.question_number }}
                                </span>
                                <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium text-xs">
                                    {{ currentQuestion.category }}
                                </span>
                            </div>

                            <button
                                type="button"
                                @click="toggleFlag(currentQuestion.id)"
                                class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl text-xs font-semibold transition"
                                :class="isFlagged(currentQuestion.id)
                                    ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30'
                                    : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 border border-transparent'"
                            >
                                <Flag class="w-3.5 h-3.5" :class="{ 'fill-amber-500': isFlagged(currentQuestion.id) }" />
                                <span>{{ isFlagged(currentQuestion.id) ? 'Flagged for Review' : 'Flag Question' }}</span>
                            </button>
                        </div>

                        <!-- Question Text -->
                        <div class="text-base sm:text-lg font-semibold text-slate-900 dark:text-slate-100 leading-relaxed">
                            {{ currentQuestion.question_text }}
                        </div>

                        <!-- Options List (A, B, C, D) -->
                        <div class="space-y-3 pt-2">
                            <div
                                v-for="opt in optionsList"
                                :key="opt.key"
                                @click="selectOption(opt.key)"
                                class="group relative flex items-start p-4 rounded-xl border-2 cursor-pointer transition select-none"
                                :class="answers[currentQuestion.id] === opt.key
                                    ? 'bg-teal-50/70 dark:bg-teal-950/40 border-teal-500 text-teal-950 dark:text-teal-100 shadow-xs'
                                    : 'bg-white dark:bg-slate-800/60 border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600 text-slate-800 dark:text-slate-200'"
                            >
                                <div
                                    class="shrink-0 w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs mr-3.5 transition"
                                    :class="answers[currentQuestion.id] === opt.key
                                        ? 'bg-teal-600 text-white'
                                        : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 group-hover:bg-slate-200'"
                                >
                                    {{ opt.label }}
                                </div>

                                <div class="text-sm font-medium leading-relaxed pt-0.5">
                                    {{ currentQuestion[`option_${opt.key}`] }}
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Navigation Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-800">
                            <button
                                type="button"
                                @click="prevQuestion"
                                :disabled="currentIndex === 0"
                                class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition"
                            >
                                <ArrowLeft class="w-4 h-4" />
                                <span>Previous</span>
                            </button>

                            <button
                                v-if="currentIndex < questions.length - 1"
                                type="button"
                                @click="nextQuestion"
                                class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition"
                            >
                                <span>Next Question</span>
                                <ArrowRight class="w-4 h-4" />
                            </button>

                            <button
                                v-else
                                type="button"
                                @click="showConfirmModal = true"
                                class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition"
                            >
                                <CheckCircle2 class="w-4 h-4" />
                                <span>Finish &amp; Review</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- Sidebar: Question Palette (4 Cols) -->
                <!-- ========================================================= -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center space-x-2">
                                <Layers class="w-4 h-4 text-teal-500" />
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    Question Palette
                                </h3>
                            </div>
                            <span class="text-xs text-slate-500 font-mono">50 Items</span>
                        </div>

                        <!-- Legend -->
                        <div class="grid grid-cols-2 gap-2 text-[11px] pb-2">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-3.5 h-3.5 rounded bg-teal-600"></span>
                                <span class="text-slate-600 dark:text-slate-400">Answered ({{ answeredCount }})</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-3.5 h-3.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600"></span>
                                <span class="text-slate-600 dark:text-slate-400">Unanswered ({{ unansweredCount }})</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-3.5 h-3.5 rounded bg-amber-400"></span>
                                <span class="text-slate-600 dark:text-slate-400">Flagged ({{ flaggedQuestions.size }})</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-3.5 h-3.5 rounded border-2 border-teal-500"></span>
                                <span class="text-slate-600 dark:text-slate-400">Current</span>
                            </div>
                        </div>

                        <!-- 50 Numbers Grid -->
                        <div class="max-h-80 overflow-y-auto pr-1">
                            <div class="grid grid-cols-5 gap-2">
                                <button
                                    v-for="(q, idx) in questions"
                                    :key="q.id"
                                    type="button"
                                    @click="goToQuestion(idx)"
                                    class="h-9 rounded-xl font-bold text-xs flex items-center justify-center transition relative"
                                    :class="[
                                        idx === currentIndex
                                            ? 'ring-2 ring-teal-500 ring-offset-2 dark:ring-offset-slate-900'
                                            : '',
                                        isFlagged(q.id)
                                            ? 'bg-amber-400 text-slate-950 font-black'
                                            : isAnswered(q.id)
                                                ? 'bg-teal-600 text-white'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'
                                    ]"
                                    :title="`Question ${q.question_number} (${q.category})`"
                                >
                                    {{ q.question_number }}
                                </button>
                            </div>
                        </div>

                        <!-- Quick Action Button in Sidebar -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button
                                @click="showConfirmModal = true"
                                class="w-full py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center space-x-2"
                            >
                                <Send class="w-3.5 h-3.5" />
                                <span>Submit &amp; Generate Evaluation</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Confirmation Modal -->
        <Modal :show="showConfirmModal" @close="showConfirmModal = false" max-width="md">
            <div class="p-6 space-y-4">
                <div class="flex items-center space-x-3 text-teal-600 dark:text-teal-400">
                    <div class="p-2 bg-teal-500/10 rounded-2xl">
                        <CheckSquare class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            Submit Competency Assessment?
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Immediate evaluation and official scoring will take place.
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl space-y-2 text-xs">
                    <div class="flex justify-between text-slate-700 dark:text-slate-300">
                        <span>Total MCQs:</span>
                        <strong class="text-slate-900 dark:text-white font-bold">{{ questions.length }} Questions</strong>
                    </div>
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Questions Answered:</span>
                        <strong class="font-bold">{{ answeredCount }} / {{ questions.length }}</strong>
                    </div>
                    <div class="flex justify-between text-amber-600 dark:text-amber-400">
                        <span>Questions Unanswered:</span>
                        <strong class="font-bold">{{ unansweredCount }}</strong>
                    </div>
                    <div v-if="flaggedQuestions.size > 0" class="flex justify-between text-purple-500">
                        <span>Flagged for Review:</span>
                        <strong class="font-bold">{{ flaggedQuestions.size }}</strong>
                    </div>
                </div>

                <div v-if="unansweredCount > 0" class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-500/30 rounded-xl flex items-start space-x-2 text-xs text-amber-800 dark:text-amber-300">
                    <AlertCircle class="w-4 h-4 shrink-0 mt-0.5" />
                    <span>You still have {{ unansweredCount }} unanswered question(s). Unanswered questions receive 0 marks.</span>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <SecondaryButton @click="showConfirmModal = false" :disabled="isSubmitting">
                        Continue Assessment
                    </SecondaryButton>
                    <PrimaryButton
                        @click="submitQuiz"
                        :disabled="isSubmitting"
                        class="bg-teal-600 hover:bg-teal-700"
                    >
                        <span v-if="isSubmitting">Evaluating...</span>
                        <span v-else>Confirm &amp; Submit</span>
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
