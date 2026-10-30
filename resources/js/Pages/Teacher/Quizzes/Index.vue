<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
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
    Shield,
    Plus,
    Edit3,
    Trash2,
    X,
    Settings
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
    availableCategories: {
        type: Array,
        default: () => [],
    },
});

// Modals
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const examToEdit = ref(null);
const examToDelete = ref(null);

// Custom Category toggles
const isCustomCreateCategory = ref(false);
const customCreateCategoryInput = ref('');
const isCustomEditCategory = ref(false);
const customEditCategoryInput = ref('');

// Forms
const createForm = useForm({
    title: '',
    category: props.availableCategories[0] || 'CBT & Occupational Standards',
    time_limit_minutes: 60,
    passing_percentage: 60,
    description: '',
    instructions: '',
    is_active: true,
});

const editForm = useForm({
    title: '',
    category: '',
    time_limit_minutes: 60,
    passing_percentage: 60,
    description: '',
    instructions: '',
    is_active: true,
});

const openCreateModal = () => {
    isCustomCreateCategory.value = false;
    customCreateCategoryInput.value = '';
    createForm.reset();
    createForm.category = props.availableCategories[0] || 'CBT & Occupational Standards';
    createForm.time_limit_minutes = 60;
    createForm.passing_percentage = 60;
    createForm.is_active = true;
    showCreateModal.value = true;
};

const submitCreate = () => {
    if (isCustomCreateCategory.value && customCreateCategoryInput.value.trim()) {
        createForm.category = customCreateCategoryInput.value.trim();
    }
    createForm.post(route('admin.quizzes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

const openEditModal = (quiz) => {
    examToEdit.value = quiz;
    isCustomEditCategory.value = !props.availableCategories.includes(quiz.category);
    customEditCategoryInput.value = isCustomEditCategory.value ? quiz.category : '';
    editForm.title = quiz.title;
    editForm.category = quiz.category;
    editForm.time_limit_minutes = quiz.time_limit_minutes;
    editForm.passing_percentage = quiz.passing_percentage;
    editForm.description = quiz.description || '';
    editForm.instructions = quiz.instructions || '';
    editForm.is_active = !!quiz.is_active;
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!examToEdit.value) return;
    if (isCustomEditCategory.value && customEditCategoryInput.value.trim()) {
        editForm.category = customEditCategoryInput.value.trim();
    }
    editForm.put(route('admin.quizzes.update', examToEdit.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            examToEdit.value = null;
        },
    });
};

const openDeleteModal = (quiz) => {
    examToDelete.value = quiz;
    showDeleteModal.value = true;
};

const submitDelete = () => {
    if (!examToDelete.value) return;
    useForm({}).delete(route('admin.quizzes.destroy', examToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            examToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="MCQ Competency Assessments & Examinations" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-500/20">
                        <CheckSquare class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            MCQ Competency Assessment &amp; Examination Engine
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            National TVET Teacher &amp; Student Technical Pedagogy Evaluation
                        </p>
                    </div>
                </div>

                <div v-if="isEvaluator" class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="openCreateModal"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create New Exam</span>
                    </button>

                    <Link
                        :href="route('admin.quizzes.questions.index')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-sm transition"
                    >
                        <HelpCircle class="w-4 h-4 text-amber-400" />
                        <span>Question Bank &amp; Bulk CSV</span>
                    </Link>

                    <Link
                        :href="route('admin.quizzes.analytics')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-sm transition"
                    >
                        <BarChart3 class="w-4 h-4" />
                        <span>Results Analytics &amp; Printing</span>
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
                    <div class="flex items-center space-x-3">
                        <button
                            type="button"
                            @click="openCreateModal"
                            class="text-xs font-bold px-3 py-1.5 rounded-lg bg-teal-600/30 text-teal-300 border border-teal-500/40 hover:bg-teal-600/50 flex items-center space-x-1 cursor-pointer"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Exam</span>
                        </button>
                        <Link
                            :href="route('admin.quizzes.analytics')"
                            class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center space-x-1"
                        >
                            <span>Open Evaluation Dashboard</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 text-white">
                    <div>
                        <div class="text-[11px] text-slate-400">Total Exams Created</div>
                        <div class="text-xl font-black text-teal-400">{{ quizzes.length }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-400">Total Submissions</div>
                        <div class="text-xl font-black text-purple-300">{{ evaluatorStats.total_submissions }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-400">Evaluated Teachers</div>
                        <div class="text-xl font-black text-amber-300">{{ evaluatorStats.unique_teachers }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-400">Passing Rate</div>
                        <div class="text-xl font-black text-emerald-400">{{ evaluatorStats.pass_rate }}%</div>
                    </div>
                </div>
            </div>

            <!-- Quizzes Grid -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">
                        Available Competency Assessments &amp; Exams ({{ quizzes.length }})
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button
                            v-if="isEvaluator"
                            type="button"
                            @click="openCreateModal"
                            class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center space-x-1 cursor-pointer"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Create New Exam</span>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="quizzes.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-3">
                    <CheckSquare class="w-12 h-12 text-slate-400 mx-auto" />
                    <h4 class="text-base font-bold text-slate-800 dark:text-white">No Examinations Configured</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Create your first examination to set up multiple-choice questions or bulk import questions via CSV.</p>
                    <button
                        v-if="isEvaluator"
                        type="button"
                        @click="openCreateModal"
                        class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold inline-flex items-center space-x-1.5 shadow cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create Examination</span>
                    </button>
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
                                <span
                                    v-if="!quiz.is_active"
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20"
                                >
                                    Draft / Inactive
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <h4 class="text-lg md:text-xl font-bold text-slate-900 dark:text-white leading-snug">
                                    {{ quiz.title }}
                                </h4>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                {{ quiz.description || 'Comprehensive competency-based multiple-choice evaluation covering core skills and practical safety standards.' }}
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
                                    View Breakdown &amp; Printable Report →
                                </Link>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 shrink-0 lg:w-60">
                            <Link
                                v-if="quiz.is_active"
                                :href="route('quizzes.show', quiz.id)"
                                class="w-full inline-flex items-center justify-center space-x-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md hover:shadow-lg transition text-center"
                            >
                                <CheckSquare class="w-4 h-4" />
                                <span>{{ quiz.user_attempts_count > 0 ? 'Retake Exam' : 'Start Exam Now' }}</span>
                            </Link>

                            <Link
                                v-if="isEvaluator"
                                :href="route('admin.quizzes.questions.index', quiz.id)"
                                class="w-full inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-semibold text-xs border border-purple-200 dark:border-purple-800 transition"
                            >
                                <HelpCircle class="w-3.5 h-3.5 text-purple-500" />
                                <span>Set Questions &amp; Bulk CSV</span>
                            </Link>

                            <!-- Evaluator Edit / Delete Buttons -->
                            <div v-if="isEvaluator" class="grid grid-cols-2 gap-2 pt-1">
                                <button
                                    type="button"
                                    @click="openEditModal(quiz)"
                                    class="inline-flex items-center justify-center space-x-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs border border-slate-200 dark:border-slate-700 transition cursor-pointer"
                                >
                                    <Edit3 class="w-3 h-3 text-blue-500" />
                                    <span>Edit</span>
                                </button>
                                <button
                                    type="button"
                                    @click="openDeleteModal(quiz)"
                                    class="inline-flex items-center justify-center space-x-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-300 font-semibold text-xs border border-rose-200 dark:border-rose-800 transition cursor-pointer"
                                >
                                    <Trash2 class="w-3 h-3 text-rose-500" />
                                    <span>Delete</span>
                                </button>
                            </div>

                            <Link
                                v-if="quiz.latest_attempt"
                                :href="route('quizzes.attempt.print', quiz.latest_attempt.id)"
                                class="w-full inline-flex items-center justify-center space-x-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs border border-slate-200 dark:border-slate-700 transition"
                            >
                                <FileText class="w-3.5 h-3.5 text-teal-500" />
                                <span>Print Evaluation Slip</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Exam Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false" max-width="lg">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-teal-500/10 text-teal-600 dark:text-teal-400 rounded-xl">
                            <Plus class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Create New Examination / Quiz</h3>
                            <p class="text-xs text-slate-500">Configure exam settings and start adding questions</p>
                        </div>
                    </div>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <InputLabel value="Examination Title *" />
                        <TextInput
                            v-model="createForm.title"
                            type="text"
                            class="mt-1 block w-full text-sm"
                            placeholder="e.g. Solar PV Installation & Maintenance Mid-Term Exam"
                            required
                        />
                        <InputError :message="createForm.errors.title" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Lecture / Trade Category *" />
                            <select
                                v-if="!isCustomCreateCategory"
                                v-model="createForm.category"
                                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                                required
                            >
                                <option v-for="cat in availableCategories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                            <TextInput
                                v-else
                                v-model="customCreateCategoryInput"
                                type="text"
                                class="mt-1 block w-full text-xs"
                                placeholder="Type custom category name..."
                                required
                            />
                            <button
                                type="button"
                                @click="isCustomCreateCategory = !isCustomCreateCategory"
                                class="text-[11px] text-teal-600 dark:text-teal-400 hover:underline mt-1 block"
                            >
                                {{ isCustomCreateCategory ? '← Choose from standard categories' : '+ Type new custom category' }}
                            </button>
                            <InputError :message="createForm.errors.category" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel value="Time Limit (Minutes) *" />
                            <TextInput
                                v-model="createForm.time_limit_minutes"
                                type="number"
                                min="5"
                                max="300"
                                class="mt-1 block w-full text-sm"
                                required
                            />
                            <InputError :message="createForm.errors.time_limit_minutes" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Passing Mark (%) *" />
                            <TextInput
                                v-model="createForm.passing_percentage"
                                type="number"
                                min="1"
                                max="100"
                                class="mt-1 block w-full text-sm"
                                required
                            />
                            <InputError :message="createForm.errors.passing_percentage" class="mt-1" />
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center space-x-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <input
                                    type="checkbox"
                                    v-model="createForm.is_active"
                                    class="rounded text-teal-600 focus:ring-teal-500"
                                />
                                <span>Active &amp; Available to Students/Teachers</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Description / Overview" />
                        <textarea
                            v-model="createForm.description"
                            rows="2"
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                            placeholder="Briefly describe what this assessment covers..."
                        ></textarea>
                        <InputError :message="createForm.errors.description" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Exam Instructions (Shown before starting)" />
                        <textarea
                            v-model="createForm.instructions"
                            rows="2"
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                            placeholder="Instructions for candidates (e.g. You have 60 minutes to answer all 50 questions)..."
                        ></textarea>
                        <InputError :message="createForm.errors.instructions" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showCreateModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="createForm.processing" class="bg-teal-600 hover:bg-teal-700">
                            {{ createForm.processing ? 'Creating...' : 'Create & Proceed to Questions →' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Exam Modal -->
        <Modal :show="showEditModal" @close="showEditModal = false" max-width="lg">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl">
                            <Edit3 class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Examination Settings</h3>
                            <p class="text-xs text-slate-500">{{ examToEdit?.title }}</p>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <InputLabel value="Examination Title *" />
                        <TextInput
                            v-model="editForm.title"
                            type="text"
                            class="mt-1 block w-full text-sm"
                            required
                        />
                        <InputError :message="editForm.errors.title" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Category *" />
                            <select
                                v-if="!isCustomEditCategory"
                                v-model="editForm.category"
                                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                                required
                            >
                                <option v-for="cat in availableCategories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                            <TextInput
                                v-else
                                v-model="customEditCategoryInput"
                                type="text"
                                class="mt-1 block w-full text-xs"
                                placeholder="Type custom category name..."
                                required
                            />
                            <button
                                type="button"
                                @click="isCustomEditCategory = !isCustomEditCategory"
                                class="text-[11px] text-teal-600 dark:text-teal-400 hover:underline mt-1 block"
                            >
                                {{ isCustomEditCategory ? '← Choose from standard categories' : '+ Type new custom category' }}
                            </button>
                            <InputError :message="editForm.errors.category" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel value="Time Limit (Minutes) *" />
                            <TextInput
                                v-model="editForm.time_limit_minutes"
                                type="number"
                                min="5"
                                max="300"
                                class="mt-1 block w-full text-sm"
                                required
                            />
                            <InputError :message="editForm.errors.time_limit_minutes" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <InputLabel value="Passing Mark (%) *" />
                            <TextInput
                                v-model="editForm.passing_percentage"
                                type="number"
                                min="1"
                                max="100"
                                class="mt-1 block w-full text-sm"
                                required
                            />
                            <InputError :message="editForm.errors.passing_percentage" class="mt-1" />
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center space-x-2 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <input
                                    type="checkbox"
                                    v-model="editForm.is_active"
                                    class="rounded text-blue-600 focus:ring-blue-500"
                                />
                                <span>Active &amp; Visible to Candidates</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Description" />
                        <textarea
                            v-model="editForm.description"
                            rows="2"
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                        ></textarea>
                        <InputError :message="editForm.errors.description" class="mt-1" />
                    </div>

                    <div>
                        <InputLabel value="Instructions" />
                        <textarea
                            v-model="editForm.instructions"
                            rows="2"
                            class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs"
                        ></textarea>
                        <InputError :message="editForm.errors.instructions" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showEditModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="editForm.processing" class="bg-blue-600 hover:bg-blue-700">
                            {{ editForm.processing ? 'Saving...' : 'Update Settings' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Exam Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="p-2.5 bg-rose-100 dark:bg-rose-950/60 rounded-xl">
                        <Trash2 class="w-6 h-6" />
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Examination?</h3>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Are you sure you want to delete <strong class="text-slate-900 dark:text-white font-bold">"{{ examToDelete?.title }}"</strong>?
                    This will permanently remove the exam, its <span class="font-bold">{{ examToDelete?.total_questions }} MCQ questions</span>, and all past candidate submission attempts.
                </p>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="submitDelete">
                        Delete Examination
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>