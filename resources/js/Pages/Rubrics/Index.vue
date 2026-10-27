<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Award,
    Plus,
    Copy,
    Trash2,
    Eye,
    ChevronRight,
    Layers,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    rubrics: Object,
    courses: Array,
});

const showCreateModal = ref(false);

const form = useForm({
    course_id: props.courses[0]?.id || '',
    title: '',
    description: '',
    criteria: [
        {
            title: 'Component Identification',
            description: 'Physical recognition and classification of tools and parts.',
            max_points: 30,
            levels: [
                { title: 'Exemplary', descriptor: 'Correctly identifies 9-10 components.', points: 30 },
                { title: 'Proficient', descriptor: 'Correctly identifies 7-8 components.', points: 24 },
                { title: 'Developing', descriptor: 'Correctly identifies 5-6 components.', points: 15 },
                { title: 'Novice', descriptor: 'Identifies fewer than 5 components.', points: 0 },
            ],
        },
        {
            title: 'Functional Explanations',
            description: 'Explaining operating principles and circuit relationships.',
            max_points: 25,
            levels: [
                { title: 'Exemplary', descriptor: 'Comprehensive technical explanation.', points: 25 },
                { title: 'Proficient', descriptor: 'Clear explanation with minor omissions.', points: 20 },
                { title: 'Developing', descriptor: 'Superficial understanding.', points: 12 },
                { title: 'Novice', descriptor: 'Inaccurate or missing descriptions.', points: 0 },
            ],
        },
        {
            title: 'Safety Hazard Identification',
            description: 'Adherence to PPE, isolation rules, and risk controls.',
            max_points: 20,
            levels: [
                { title: 'Exemplary', descriptor: 'All hazards spotted and mitigations enacted.', points: 20 },
                { title: 'Proficient', descriptor: 'Major hazards identified.', points: 16 },
                { title: 'Developing', descriptor: 'Partial safety compliance.', points: 8 },
                { title: 'Novice', descriptor: 'Critical safety violations observed.', points: 0 },
            ],
        },
    ],
});

const addCriterion = () => {
    form.criteria.push({
        title: 'New Criterion',
        description: '',
        max_points: 25,
        levels: [
            { title: 'Exemplary', descriptor: 'Meets full standard.', points: 25 },
            { title: 'Proficient', descriptor: 'Meets basic standard.', points: 18 },
            { title: 'Developing', descriptor: 'Partial achievement.', points: 10 },
            { title: 'Novice', descriptor: 'Did not meet criteria.', points: 0 },
        ],
    });
};

const removeCriterion = (index) => {
    if (form.criteria.length > 1) {
        form.criteria.splice(index, 1);
    }
};

const calculateTotal = () => {
    return form.criteria.reduce((acc, c) => acc + parseFloat(c.max_points || 0), 0);
};

const submitRubric = () => {
    form.post(route('rubrics.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
        },
    });
};

const duplicateRubric = (rubricId) => {
    router.post(route('rubrics.duplicate', rubricId));
};
</script>

<template>
    <Head title="Assessment Rubrics — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Standardized TVET Assessment Rubrics
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Create multi-criterion grading matrices with explicit achievement standards and automated score aggregation.
                    </p>
                </div>

                <button
                    @click="showCreateModal = true"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition"
                >
                    <Plus class="w-4 h-4" />
                    <span>Design New Rubric</span>
                </button>
            </div>
        </template>

        <!-- Rubrics Grid -->
        <div v-if="rubrics.data.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <Award class="w-12 h-12 mx-auto text-slate-400 mb-3" />
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No assessment rubrics configured yet</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Design your first TVET grading rubric to fulfill Step 7 of the Day 8 competency wizard.
            </p>
            <button
                @click="showCreateModal = true"
                class="mt-4 px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs"
            >
                Launch Rubric Designer
            </button>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="rubric in rubrics.data"
                :key="rubric.id"
                class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between"
            >
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-xs font-bold text-teal-600 dark:text-teal-400">
                            {{ rubric.course?.code || 'Global Rubric' }}
                        </span>
                        <span class="text-xs font-black px-2 py-0.5 rounded-full bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300">
                            {{ rubric.total_points }} pts
                        </span>
                    </div>

                    <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-1">
                        {{ rubric.title }}
                    </h3>

                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                        {{ rubric.description || 'Standard assessment criteria rubric.' }}
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 flex items-center justify-between">
                        <span>{{ rubric.criteria_count || rubric.criteria?.length || 0 }} Criteria</span>
                        <span>{{ rubric.assignments_count || 0 }} Assignments</span>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <Link
                        :href="route('rubrics.show', rubric.id)"
                        class="text-xs font-bold text-teal-600 hover:underline flex items-center space-x-1"
                    >
                        <span>Inspect Rubric</span>
                        <ChevronRight class="w-3.5 h-3.5" />
                    </Link>

                    <button
                        @click="duplicateRubric(rubric.id)"
                        class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 hover:text-slate-900"
                        title="Duplicate Rubric"
                    >
                        <Copy class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Visual Rubric Designer Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Visual Assessment Rubric Designer
                        </h3>
                        <p class="text-xs text-slate-500">
                            Configure criteria and achievement levels. Total points update in real time.
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400 font-bold uppercase">Calculated Total</div>
                        <div class="text-2xl font-black text-teal-600">{{ calculateTotal() }} Marks</div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                Rubric Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.title"
                                placeholder="e.g. Solar PV Practical Assessment Rubric"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                Associated Course
                            </label>
                            <select
                                v-model="form.course_id"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            >
                                <option value="">Global Template (All Courses)</option>
                                <option v-for="c in courses" :key="c.id" :value="c.id">
                                    {{ c.title }} ({{ c.code }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Criteria Builder -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assessment Criteria Matrix</span>
                            <button
                                type="button"
                                @click="addCriterion"
                                class="text-xs font-bold text-teal-600 hover:underline flex items-center space-x-1"
                            >
                                <Plus class="w-3.5 h-3.5" />
                                <span>Add Criterion</span>
                            </button>
                        </div>

                        <div
                            v-for="(crit, cIndex) in form.criteria"
                            :key="cIndex"
                            class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        v-model="crit.title"
                                        placeholder="Criterion Name (e.g. Safety Hazard Identification)"
                                        class="w-full font-bold text-sm bg-transparent border-0 border-b border-slate-300 focus:ring-0 focus:border-teal-500 p-1"
                                        required
                                    />
                                </div>
                                <div class="w-32">
                                    <input
                                        type="number"
                                        v-model="crit.max_points"
                                        placeholder="Max pts"
                                        class="w-full text-xs font-bold rounded-lg border-slate-300"
                                        required
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="removeCriterion(cIndex)"
                                    class="text-rose-500 hover:text-rose-700 p-1"
                                    title="Delete Criterion"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <!-- Levels -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 pt-2">
                                <div
                                    v-for="(lvl, lIndex) in crit.levels"
                                    :key="lIndex"
                                    class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs space-y-1.5"
                                >
                                    <div class="flex items-center justify-between">
                                        <input
                                            type="text"
                                            v-model="lvl.title"
                                            class="w-20 font-bold text-[11px] p-0 border-0 bg-transparent"
                                        />
                                        <input
                                            type="number"
                                            v-model="lvl.points"
                                            class="w-12 text-[11px] font-bold p-0 text-right border-0 bg-transparent text-teal-600"
                                        />
                                    </div>
                                    <textarea
                                        v-model="lvl.descriptor"
                                        rows="2"
                                        placeholder="Level descriptor..."
                                        class="w-full text-[10px] rounded-lg border-slate-200 p-1.5"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                    <button
                        type="button"
                        @click="showCreateModal = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitRubric"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow transition"
                    >
                        Save &amp; Activate Rubric ({{ calculateTotal() }} pts)
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
