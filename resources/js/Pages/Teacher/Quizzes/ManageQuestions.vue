<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {
    HelpCircle,
    Plus,
    UploadCloud,
    FileSpreadsheet,
    Search,
    Filter,
    Edit3,
    Trash2,
    CheckCircle2,
    X,
    Layers,
    ArrowLeft,
    FileText,
    BarChart3,
    CheckSquare,
    AlertCircle,
    Download
} from 'lucide-vue-next';

const props = defineProps({
    quiz: {
        type: Object,
        required: true,
    },
    questions: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '', category: 'all' }),
    },
    availableCategories: {
        type: Array,
        default: () => [],
    },
    categoryCounts: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');

// Modals
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showBulkModal = ref(false);

const questionToEdit = ref(null);
const questionToDelete = ref(null);

// Custom category toggle
const isCustomCategory = ref(false);
const customCategoryInput = ref('');

// Question Form (Single Add)
const questionForm = useForm({
    category: props.availableCategories[0] || 'CBT & Occupational Standards',
    question_text: '',
    option_a: '',
    option_b: '',
    option_c: '',
    option_d: '',
    correct_option: 'a',
    explanation: '',
    points: 1,
});

// Edit Form
const editForm = useForm({
    category: '',
    question_text: '',
    option_a: '',
    option_b: '',
    option_c: '',
    option_d: '',
    correct_option: 'a',
    explanation: '',
    points: 1,
});

// Bulk Upload Form
const bulkForm = useForm({
    csv_file: null,
    mode: 'append', // 'append' or 'replace'
});

const applyFilters = () => {
    router.get(
        route('admin.quizzes.questions.index', props.quiz.id),
        {
            search: searchQuery.value || undefined,
            category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
        },
        { preserveState: true, replace: true }
    );
};

const filterByCategory = (cat) => {
    selectedCategory.value = cat;
    applyFilters();
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    router.get(route('admin.quizzes.questions.index', props.quiz.id), {}, { preserveState: true, replace: true });
};

// Open Add Modal
const openAddModal = () => {
    isCustomCategory.value = false;
    customCategoryInput.value = '';
    questionForm.reset();
    questionForm.category = props.availableCategories[0] || 'CBT & Occupational Standards';
    questionForm.correct_option = 'a';
    questionForm.points = 1;
    showAddModal.value = true;
};

// Submit Add
const submitCreate = () => {
    if (isCustomCategory.value && customCategoryInput.value.trim()) {
        questionForm.category = customCategoryInput.value.trim();
    }
    questionForm.post(route('admin.quizzes.questions.store', props.quiz.id), {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            questionForm.reset();
        },
    });
};

// Open Edit Modal
const openEditModal = (q) => {
    questionToEdit.value = q;
    editForm.category = q.category;
    editForm.question_text = q.question_text;
    editForm.option_a = q.option_a;
    editForm.option_b = q.option_b;
    editForm.option_c = q.option_c;
    editForm.option_d = q.option_d;
    editForm.correct_option = q.correct_option;
    editForm.explanation = q.explanation || '';
    editForm.points = q.points || 1;
    showEditModal.value = true;
};

// Submit Edit
const submitEdit = () => {
    if (!questionToEdit.value) return;
    editForm.put(route('admin.quizzes.questions.update', questionToEdit.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            questionToEdit.value = null;
        },
    });
};

// Confirm & Delete Question
const confirmDelete = (q) => {
    questionToDelete.value = q;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!questionToDelete.value) return;
    router.delete(route('admin.quizzes.questions.destroy', questionToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            questionToDelete.value = null;
        },
    });
};

// Handle CSV File Selection
const handleCsvFile = (e) => {
    bulkForm.csv_file = e.target.files[0] || null;
};

// Submit Bulk CSV
const submitBulkCsv = () => {
    bulkForm.post(route('admin.quizzes.questions.bulk-csv', props.quiz.id), {
        preserveScroll: true,
        onSuccess: () => {
            showBulkModal.value = false;
            bulkForm.reset();
        },
    });
};
</script>

<template>
    <Head :title="`Set MCQ Questions - ${quiz.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('admin.quizzes.analytics')"
                        class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-slate-600 dark:text-slate-300 transition"
                    >
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            MCQ Question Bank &amp; Category Setting
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ quiz.title }} • Currently {{ quiz.total_questions }} Questions
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="route('admin.quizzes.questions.sample-csv')"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 transition"
                    >
                        <Download class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
                        <span>Sample CSV</span>
                    </a>

                    <button
                        @click="showBulkModal = true"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <UploadCloud class="w-4 h-4" />
                        <span>Bulk CSV Upload</span>
                    </button>

                    <button
                        @click="openAddModal"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add Question</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
            <!-- ========================================================= -->
            <!-- Category Pills Overview (Lecture Categories Distribution) -->
            <!-- ========================================================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-2">
                        <Layers class="w-4 h-4 text-amber-500" />
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Lecture &amp; Trade Categories
                        </h3>
                    </div>
                    <span class="text-xs text-slate-500">{{ Object.keys(categoryCounts).length }} active categories</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        @click="filterByCategory('all')"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                        :class="selectedCategory === 'all'
                            ? 'bg-amber-500 text-slate-950 font-bold shadow-xs'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                    >
                        All Categories ({{ quiz.total_questions }})
                    </button>

                    <button
                        v-for="(count, catName) in categoryCounts"
                        :key="catName"
                        @click="filterByCategory(catName)"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition flex items-center space-x-1.5"
                        :class="selectedCategory === catName
                            ? 'bg-teal-600 text-white font-bold shadow-xs'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                    >
                        <span>{{ catName }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="selectedCategory === catName ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'">
                            {{ count }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Search & Filter Bar -->
            <!-- ========================================================= -->
            <div class="p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-96">
                    <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search question text or options..."
                        class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-900 dark:text-white"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                    <button
                        @click="resetFilters"
                        class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 underline font-medium"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Questions List -->
            <!-- ========================================================= -->
            <div class="space-y-4">
                <div
                    v-for="q in questions.data"
                    :key="q.id"
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4 hover:border-slate-300 dark:hover:border-slate-700 transition"
                >
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-800 dark:text-amber-400 font-bold text-xs border border-amber-500/20">
                                Question #{{ q.question_number }}
                            </span>
                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                                {{ q.category }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] bg-slate-50 dark:bg-slate-800/80 text-slate-500">
                                {{ q.points || 1 }} Point(s)
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-1 shrink-0">
                            <button
                                @click="openEditModal(q)"
                                class="p-2 text-slate-400 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                                title="Edit Question"
                            >
                                <Edit3 class="w-4 h-4" />
                            </button>
                            <button
                                @click="confirmDelete(q)"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition"
                                title="Delete Question"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="text-base font-semibold text-slate-900 dark:text-slate-100 leading-relaxed">
                        {{ q.question_text }}
                    </div>

                    <!-- Options Grid (A, B, C, D) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        <div
                            v-for="opt in ['a', 'b', 'c', 'd']"
                            :key="opt"
                            class="p-3 rounded-xl border text-xs flex items-start space-x-2.5"
                            :class="q.correct_option === opt
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500/50 text-emerald-900 dark:text-emerald-200 font-semibold shadow-2xs'
                                : 'bg-slate-50/60 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300'"
                        >
                            <span
                                class="w-5 h-5 rounded-md flex items-center justify-center font-bold text-[11px] uppercase shrink-0"
                                :class="q.correct_option === opt ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                            >
                                {{ opt }}
                            </span>
                            <span class="leading-relaxed">{{ q[`option_${opt}`] }}</span>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div v-if="q.explanation" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs text-slate-600 dark:text-slate-400">
                        <strong class="text-teal-600 dark:text-teal-400 font-bold block mb-0.5">Pedagogical Explanation:</strong>
                        <p class="leading-relaxed">{{ q.explanation }}</p>
                    </div>
                </div>

                <div v-if="!questions.data || questions.data.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <HelpCircle class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No questions found</h4>
                    <p class="text-xs text-slate-400 mt-1">Try resetting your filters or adding questions manually or via CSV upload.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="questions.links && questions.links.length > 3" class="p-4 flex items-center justify-center space-x-1">
                <Link
                    v-for="(link, i) in questions.links"
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                    :class="[
                        link.active
                            ? 'bg-amber-500 text-slate-950 font-bold'
                            : link.url
                                ? 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 border border-slate-200 dark:border-slate-700'
                                : 'text-slate-400 cursor-not-allowed opacity-50'
                    ]"
                />
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- Add Question Modal -->
        <!-- ========================================================= -->
        <Modal :show="showAddModal" @close="showAddModal = false" max-width="3xl">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-amber-500/10 rounded-xl text-amber-600 dark:text-amber-400">
                            <Plus class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Add New MCQ Question
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Set question text, category, 4 options, and correct answer
                            </p>
                        </div>
                    </div>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-4">
                    <!-- Category Selection -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <InputLabel value="Lecture / Trade Category *" />
                            <button
                                type="button"
                                @click="isCustomCategory = !isCustomCategory"
                                class="text-xs text-teal-600 hover:underline font-semibold"
                            >
                                {{ isCustomCategory ? 'Select from list' : '+ Type new custom category' }}
                            </button>
                        </div>

                        <select
                            v-if="!isCustomCategory"
                            v-model="questionForm.category"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500"
                            required
                        >
                            <option v-for="cat in availableCategories" :key="cat" :value="cat">
                                {{ cat }}
                            </option>
                        </select>

                        <TextInput
                            v-else
                            v-model="customCategoryInput"
                            type="text"
                            placeholder="e.g. Artificial Intelligence, Solar System Sizing..."
                            class="w-full text-xs"
                            required
                        />
                        <InputError :message="questionForm.errors.category" class="mt-1" />
                    </div>

                    <!-- Question Text -->
                    <div>
                        <InputLabel for="add-qtext" value="Question Text *" />
                        <textarea
                            id="add-qtext"
                            v-model="questionForm.question_text"
                            rows="3"
                            placeholder="Type the complete question scenario or prompt..."
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500"
                            required
                        ></textarea>
                        <InputError :message="questionForm.errors.question_text" class="mt-1" />
                    </div>

                    <!-- 4 Options (A, B, C, D) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                                    Option {{ opt }} *
                                </label>
                                <label class="text-[11px] font-semibold flex items-center space-x-1 cursor-pointer" :class="questionForm.correct_option === opt ? 'text-emerald-600 font-bold' : 'text-slate-400'">
                                    <input
                                        type="radio"
                                        :value="opt"
                                        v-model="questionForm.correct_option"
                                        class="text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5"
                                    />
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <input
                                v-model="questionForm[`option_${opt}`]"
                                type="text"
                                :placeholder="`Text for option ${opt.toUpperCase()}...`"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500"
                                required
                            />
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div>
                        <InputLabel for="add-expl" value="Pedagogical Explanation / Learning Rationale (Optional)" />
                        <textarea
                            id="add-expl"
                            v-model="questionForm.explanation"
                            rows="2"
                            placeholder="Explain why the correct answer is right and why distractors are wrong..."
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showAddModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="questionForm.processing" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold">
                            Save Question
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ========================================================= -->
        <!-- Edit Question Modal -->
        <!-- ========================================================= -->
        <Modal :show="showEditModal" @close="showEditModal = false" max-width="3xl">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-teal-500/10 rounded-xl text-teal-600 dark:text-teal-400">
                            <Edit3 class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Edit Question #{{ questionToEdit?.question_number }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Update question scenario, options, or correct answer
                            </p>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <InputLabel value="Category *" />
                        <input
                            v-model="editForm.category"
                            type="text"
                            class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel value="Question Text *" />
                        <textarea
                            v-model="editForm.question_text"
                            rows="3"
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500"
                            required
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                                    Option {{ opt }} *
                                </label>
                                <label class="text-[11px] font-semibold flex items-center space-x-1 cursor-pointer" :class="editForm.correct_option === opt ? 'text-emerald-600 font-bold' : 'text-slate-400'">
                                    <input
                                        type="radio"
                                        :value="opt"
                                        v-model="editForm.correct_option"
                                        class="text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5"
                                    />
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <input
                                v-model="editForm[`option_${opt}`]"
                                type="text"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Pedagogical Explanation" />
                        <textarea
                            v-model="editForm.explanation"
                            rows="2"
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showEditModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="editForm.processing" class="bg-teal-600 hover:bg-teal-700">
                            Save Changes
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ========================================================= -->
        <!-- Bulk CSV Upload Modal -->
        <!-- ========================================================= -->
        <Modal :show="showBulkModal" @close="showBulkModal = false" max-width="2xl">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-teal-500/10 rounded-xl text-teal-600 dark:text-teal-400">
                            <UploadCloud class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Bulk CSV Question Upload
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Import dozens or hundreds of MCQ questions across categories in seconds
                            </p>
                        </div>
                    </div>
                    <button @click="showBulkModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitBulkCsv" class="space-y-4">
                    <!-- Sample Template Download Pill -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div class="text-xs text-slate-600 dark:text-slate-300 space-y-0.5">
                            <strong class="block font-bold text-slate-900 dark:text-white">Need the CSV Template?</strong>
                            <span>Columns: category, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, points</span>
                        </div>
                        <a
                            :href="route('admin.quizzes.questions.sample-csv')"
                            class="px-3 py-1.5 bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 font-bold text-xs rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 flex items-center space-x-1 shrink-0"
                        >
                            <Download class="w-3.5 h-3.5" />
                            <span>Download CSV</span>
                        </a>
                    </div>

                    <!-- File Picker -->
                    <div>
                        <InputLabel value="Select CSV File (.csv) *" />
                        <input
                            type="file"
                            accept=".csv,text/csv"
                            @change="handleCsvFile"
                            class="mt-1 block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 dark:file:bg-teal-950/60 dark:file:text-teal-300 hover:file:bg-teal-100"
                            required
                        />
                        <InputError :message="bulkForm.errors.csv_file" class="mt-1" />
                    </div>

                    <!-- Import Mode (Append or Replace) -->
                    <div class="space-y-2 pt-2">
                        <InputLabel value="Import Action Mode *" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="p-3 rounded-xl border cursor-pointer flex items-start space-x-2.5 transition"
                                :class="bulkForm.mode === 'append' ? 'bg-teal-50 dark:bg-teal-950/40 border-teal-500' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'"
                            >
                                <input
                                    type="radio"
                                    value="append"
                                    v-model="bulkForm.mode"
                                    class="text-teal-600 focus:ring-teal-500 mt-0.5"
                                />
                                <div class="text-xs">
                                    <strong class="font-bold text-slate-900 dark:text-white block">Append to Existing</strong>
                                    <span class="text-slate-500">Adds the imported questions to current questions.</span>
                                </div>
                            </label>

                            <label
                                class="p-3 rounded-xl border cursor-pointer flex items-start space-x-2.5 transition"
                                :class="bulkForm.mode === 'replace' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700'"
                            >
                                <input
                                    type="radio"
                                    value="replace"
                                    v-model="bulkForm.mode"
                                    class="text-rose-600 focus:ring-rose-500 mt-0.5"
                                />
                                <div class="text-xs">
                                    <strong class="font-bold text-rose-700 dark:text-rose-400 block">Replace All Questions</strong>
                                    <span class="text-slate-500">Deletes existing questions and replaces them with this CSV.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showBulkModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="bulkForm.processing || !bulkForm.csv_file" class="bg-teal-600 hover:bg-teal-700">
                            <span v-if="bulkForm.processing">Processing CSV...</span>
                            <span v-else>Upload &amp; Ingest Questions</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ========================================================= -->
        <!-- Delete Question Modal -->
        <!-- ========================================================= -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="p-2 bg-rose-100 dark:bg-rose-900/40 rounded-full">
                        <Trash2 class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        Delete Question #{{ questionToDelete?.question_number }}
                    </h3>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Are you sure you want to delete this question? Question numbers will be automatically re-sequenced.
                </p>

                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200">
                    "{{ questionToDelete?.question_text }}"
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="executeDelete">
                        Delete Question
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
