<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Compass,
    Save,
    Sparkles,
    CheckCircle2,
    Plus,
    Trash2,
    ArrowRight,
    Copy,
    Printer,
    Wand2,
    ShieldAlert,
    ShieldCheck,
    AlertCircle,
    Info,
    Layers,
    BookOpen,
    HelpCircle,
    FileText,
    Wrench,
    Flame,
    Zap,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    is_editing: Boolean,
    lesson: Object,
    standards: Array,
    trade_categories: Array,
    courses: Array,
    can_update: {
        type: Boolean,
        default: true,
    },
});

const activeTab = ref('standards'); // 'standards', 'outcomes', 'demo', 'practice', 'pbl_faults', 'assessment'

// Form initialization
const form = useForm({
    title: props.lesson.title || '',
    occupational_task: props.lesson.occupational_task || '',
    trade_category_id: props.lesson.trade_category_id || null,
    occupational_standard_id: props.lesson.occupational_standard_id || null,
    course_id: props.lesson.course_id || null,
    duration_minutes: props.lesson.duration_minutes || 120,
    target_level: props.lesson.target_level || 'NSQF Level 3 / Skilled Craftsperson',
    learning_outcomes: Array.isArray(props.lesson.learning_outcomes) && props.lesson.learning_outcomes.length
        ? JSON.parse(JSON.stringify(props.lesson.learning_outcomes))
        : [{ verb: 'Execute', condition: 'Using workshop hand tools and standard PPE', standard: 'Within ±1mm dimensional tolerance', outcome_text: '' }],
    demonstration_plan: Array.isArray(props.lesson.demonstration_plan) && props.lesson.demonstration_plan.length
        ? JSON.parse(JSON.stringify(props.lesson.demonstration_plan))
        : [
            { step: 1, name: 'Preparation & Safety Briefing', instructor_action: '', safety_ppe: 'Mandatory PPE and safety checklist', key_tolerance: 'Zero safety hazard violations', media_resource: '' },
            { step: 2, name: 'Instructor Modeling at Normal Speed', instructor_action: '', safety_ppe: 'Full PPE', key_tolerance: 'Smooth continuous motion', media_resource: '' },
            { step: 3, name: 'Step-by-Step Breakdown with Key Points', instructor_action: '', safety_ppe: 'Eye and hand protection', key_tolerance: 'Adhere to technical tolerances', media_resource: '' },
            { step: 4, name: 'Trainee Imitation & Checkpoints', instructor_action: '', safety_ppe: 'Peer safety buddy cross-check', key_tolerance: 'Immediate corrective feedback', media_resource: '' },
        ],
    guided_practice: Array.isArray(props.lesson.guided_practice) && props.lesson.guided_practice.length
        ? JSON.parse(JSON.stringify(props.lesson.guided_practice))
        : [{ step: 1, activity: '', check_point: '', paired: true }],
    independent_practice: Array.isArray(props.lesson.independent_practice) && props.lesson.independent_practice.length
        ? JSON.parse(JSON.stringify(props.lesson.independent_practice))
        : [{ activity: '', time_minutes: 45, success_metric: '' }],
    project_based_tasks: Array.isArray(props.lesson.project_based_tasks) && props.lesson.project_based_tasks.length
        ? JSON.parse(JSON.stringify(props.lesson.project_based_tasks))
        : [{ project_name: '', client_brief: '', deliverable: '', materials: '' }],
    fault_diagnosis_scenarios: Array.isArray(props.lesson.fault_diagnosis_scenarios) && props.lesson.fault_diagnosis_scenarios.length
        ? JSON.parse(JSON.stringify(props.lesson.fault_diagnosis_scenarios))
        : [{ fault_symptom: '', diagnostic_procedure: '', root_cause: '', rectification: '', safety_rule: '' }],
    work_based_learning: Array.isArray(props.lesson.work_based_learning) && props.lesson.work_based_learning.length
        ? JSON.parse(JSON.stringify(props.lesson.work_based_learning))
        : [{ industry_context: '', apprentice_role: '', logbook_signoff: '' }],
    assessment_criteria: Array.isArray(props.lesson.assessment_criteria) && props.lesson.assessment_criteria.length
        ? JSON.parse(JSON.stringify(props.lesson.assessment_criteria))
        : [
            { criterion: 'Adherence to workshop safety regulations & PPE', evidence_type: 'Direct observation', is_safety_critical: true, passing_score: '100% Mandatory' },
            { criterion: 'Practical technique & tool manipulation', evidence_type: 'Direct observation', is_safety_critical: false, passing_score: '≥ 80%' },
            { criterion: 'Finished product quality / dimensional accuracy', evidence_type: 'Product inspection', is_safety_critical: false, passing_score: 'Within specification' },
        ],
    status: props.lesson.status || 'draft',
});

// Computed selected standard details
const selectedStandard = computed(() => {
    if (!form.occupational_standard_id) return null;
    return props.standards.find(s => s.id === form.occupational_standard_id);
});

// Auto-fill criteria from selected standard
const importStandardCriteria = () => {
    if (!selectedStandard.value || !selectedStandard.value.performance_criteria) return;
    const stdCriteria = selectedStandard.value.performance_criteria;
    stdCriteria.forEach(item => {
        form.assessment_criteria.push({
            criterion: item.criterion,
            evidence_type: 'Direct observation / Test',
            is_safety_critical: item.is_critical_safety || false,
            passing_score: item.is_critical_safety ? '100% Mandatory' : 'Competent',
        });
    });
};

// Array manipulation helpers
const addOutcome = () => {
    form.learning_outcomes.push({ verb: 'Perform', condition: '', standard: '', outcome_text: '' });
};
const removeOutcome = (index) => {
    form.learning_outcomes.splice(index, 1);
};

const addDemoStep = () => {
    const nextStep = form.demonstration_plan.length + 1;
    form.demonstration_plan.push({
        step: nextStep,
        name: `Step ${nextStep}`,
        instructor_action: '',
        safety_ppe: '',
        key_tolerance: '',
        media_resource: '',
    });
};
const removeDemoStep = (index) => {
    form.demonstration_plan.splice(index, 1);
    form.demonstration_plan.forEach((s, i) => { s.step = i + 1; });
};

const addGuided = () => {
    form.guided_practice.push({ step: form.guided_practice.length + 1, activity: '', check_point: '', paired: true });
};
const removeGuided = (index) => {
    form.guided_practice.splice(index, 1);
};

const addIndependent = () => {
    form.independent_practice.push({ activity: '', time_minutes: 30, success_metric: '' });
};
const removeIndependent = (index) => {
    form.independent_practice.splice(index, 1);
};

const addProject = () => {
    form.project_based_tasks.push({ project_name: '', client_brief: '', deliverable: '', materials: '' });
};
const removeProject = (index) => {
    form.project_based_tasks.splice(index, 1);
};

const addFault = () => {
    form.fault_diagnosis_scenarios.push({ fault_symptom: '', diagnostic_procedure: '', root_cause: '', rectification: '', safety_rule: '' });
};
const removeFault = (index) => {
    form.fault_diagnosis_scenarios.splice(index, 1);
};

const addWbl = () => {
    form.work_based_learning.push({ industry_context: '', apprentice_role: '', logbook_signoff: '' });
};
const removeWbl = (index) => {
    form.work_based_learning.splice(index, 1);
};

const addCriteria = () => {
    form.assessment_criteria.push({ criterion: '', evidence_type: 'Direct observation', is_safety_critical: false, passing_score: 'Competent' });
};
const removeCriteria = (index) => {
    form.assessment_criteria.splice(index, 1);
};

// Submit handler
const submit = (saveStatus = 'draft') => {
    form.status = saveStatus;
    if (props.is_editing) {
        form.put(route('pedagogy.lessons.update', props.lesson.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('pedagogy.lessons.store'), {
            preserveScroll: true,
        });
    }
};

// Duplicate handler
const formDuplicate = useForm({});
const duplicate = () => {
    if (confirm('Create a duplicate copy of this competency lesson?')) {
        formDuplicate.post(route('pedagogy.lessons.duplicate', props.lesson.id));
    }
};

// Day 9 Completion Validator
const day9Status = computed(() => {
    const hasTask = form.occupational_task && form.occupational_task.trim().length > 5;
    const hasDemo = form.demonstration_plan.some(d => d.instructor_action && d.instructor_action.trim().length > 10);
    const hasPractice = form.guided_practice.some(g => g.activity && g.activity.trim().length > 5) ||
                        form.independent_practice.some(i => i.activity && i.activity.trim().length > 5);
    const hasCriteria = form.assessment_criteria.some(c => c.criterion && c.criterion.trim().length > 5);

    const count = [hasTask, hasDemo, hasPractice, hasCriteria].filter(Boolean).length;
    return {
        hasTask,
        hasDemo,
        hasPractice,
        hasCriteria,
        percent: Math.round((count / 4) * 100),
        isComplete: count === 4,
    };
});
</script>

<template>
    <Head :title="is_editing ? `Edit: ${form.title} — TVET Lesson Builder` : 'Competency-Based Lesson Builder — Greg ICT Hub LMS'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('pedagogy.studio')" class="hover:underline flex items-center space-x-1">
                            <Layers class="w-3.5 h-3.5 text-rose-500" />
                            <span>TVET Pedagogy Studio</span>
                        </Link>
                        <span>/</span>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">Module A · Day 9 Builder</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Compass class="w-7 h-7 text-amber-500" />
                        <span>{{ is_editing ? form.title || 'Edit Competency Lesson' : 'Competency-Based Lesson Builder' }}</span>
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="submit('draft')"
                        :disabled="form.processing || !can_update"
                        class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition"
                    >
                        <Save class="w-3.5 h-3.5" />
                        <span>Save Draft</span>
                    </button>

                    <button
                        type="button"
                        @click="submit('published')"
                        :disabled="form.processing || !can_update"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <CheckCircle2 class="w-3.5 h-3.5" />
                        <span>Publish Lesson</span>
                    </button>

                    <button
                        v-if="is_editing"
                        type="button"
                        @click="duplicate"
                        :disabled="formDuplicate.processing"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                        title="Duplicate Lesson"
                    >
                        <Copy class="w-3.5 h-3.5" />
                        <span>Duplicate</span>
                    </button>

                    <Link
                        v-if="is_editing"
                        :href="route('pedagogy.lessons.export', lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                        title="Export / Print Formatted Plan"
                    >
                        <Printer class="w-3.5 h-3.5" />
                        <span>Export</span>
                    </Link>

                    <Link
                        v-if="is_editing"
                        :href="route('pedagogy.inclusive-designer.edit', lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition"
                        title="Open in Day 10 Inclusive Lesson Designer"
                    >
                        <Wand2 class="w-3.5 h-3.5" />
                        <span>Extend to Day 10</span>
                        <ArrowRight class="w-3.5 h-3.5 ml-1" />
                    </Link>
                </div>
            </div>
        </template>

        <!-- Day 9 Readiness Header Checklist -->
        <div class="mb-6 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm',
                    day9Status.isComplete ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600'
                ]">
                    {{ day9Status.percent }}%
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Day 9 Practical Assessment Criteria</div>
                    <div class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ day9Status.isComplete ? 'Day 9 Ready for Verification' : 'Essential Practical Components Required' }}
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span :class="day9Status.hasTask ? 'text-emerald-600 dark:text-emerald-400 font-semibold flex items-center space-x-1' : 'text-slate-400 flex items-center space-x-1'">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Occupational Task</span>
                </span>
                <span :class="day9Status.hasDemo ? 'text-emerald-600 dark:text-emerald-400 font-semibold flex items-center space-x-1' : 'text-slate-400 flex items-center space-x-1'">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Instructor Demonstration</span>
                </span>
                <span :class="day9Status.hasPractice ? 'text-emerald-600 dark:text-emerald-400 font-semibold flex items-center space-x-1' : 'text-slate-400 flex items-center space-x-1'">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Learner Practice</span>
                </span>
                <span :class="day9Status.hasCriteria ? 'text-emerald-600 dark:text-emerald-400 font-semibold flex items-center space-x-1' : 'text-slate-400 flex items-center space-x-1'">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Measurable Criteria</span>
                </span>
            </div>
        </div>

        <!-- Tabbed Navigation -->
        <div class="border-b border-slate-200 dark:border-slate-800 mb-6 flex overflow-x-auto space-x-2 pb-1">
            <button
                type="button"
                @click="activeTab = 'standards'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'standards'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Compass class="w-4 h-4" />
                <span>1. Occupational Task &amp; Standards</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'outcomes'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'outcomes'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <FileText class="w-4 h-4" />
                <span>2. Measurable Outcomes ({{ form.learning_outcomes.length }})</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'demo'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'demo'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Zap class="w-4 h-4" />
                <span>3. 4-Step Demonstration Plan</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'practice'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'practice'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Wrench class="w-4 h-4" />
                <span>4. Guided &amp; Independent Practice</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'pbl_faults'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'pbl_faults'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Flame class="w-4 h-4" />
                <span>5. PBL &amp; Fault Diagnosis</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'assessment'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'assessment'
                        ? 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <ShieldCheck class="w-4 h-4" />
                <span>6. Assessment Criteria &amp; Evidence</span>
            </button>
        </div>

        <form @submit.prevent="submit('draft')" class="space-y-6">
            <!-- TAB 1: Standards and Occupational Task -->
            <div v-show="activeTab === 'standards'" class="space-y-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">General TVET Lesson Identification</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Lesson Title *
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Solar PV Combiner Box Termination and DC Disconnect Wiring"
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                                required
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Occupational Task / Real-World Trade Duty *
                            </label>
                            <textarea
                                v-model="form.occupational_task"
                                rows="2"
                                placeholder="Describe the specific industrial task trainees will perform (e.g. Terminate 4-string monocrystalline PV array into DC combiner box with 1000V DC surge protection and rotary isolator switch)."
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-teal-500 focus:border-teal-500"
                                required
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Occupational Trade Category *
                            </label>
                            <select
                                v-model="form.trade_category_id"
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            >
                                <option :value="null">-- Select TVET Trade Category --</option>
                                <option v-for="cat in trade_categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Target Qualification Level
                            </label>
                            <input
                                v-model="form.target_level"
                                type="text"
                                placeholder="e.g. NSQF Level 3 / Skilled Craftsperson"
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Associated LMS Course
                            </label>
                            <select
                                v-model="form.course_id"
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            >
                                <option :value="null">-- Independent TVET Module (Not Linked) --</option>
                                <option v-for="c in courses" :key="c.id" :value="c.id">
                                    {{ c.title }} ({{ c.is_practice ? 'Sandbox' : 'Institutional' }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Estimated Practical Duration (Minutes)
                            </label>
                            <input
                                v-model.number="form.duration_minutes"
                                type="number"
                                min="15"
                                max="480"
                                class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- Occupational Standard Selector -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span>National Occupational Standard (NOS) Reference</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 font-semibold">
                                    Nigeria Approved Sources
                                </span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Occupational standards must be linked to an approved authority (NBTE NOS) or explicitly tagged as instructor-created examples.
                            </p>
                        </div>

                        <button
                            v-if="selectedStandard && selectedStandard.performance_criteria"
                            type="button"
                            @click="importStandardCriteria"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Import Performance Criteria</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Select Occupational Standard *
                        </label>
                        <select
                            v-model="form.occupational_standard_id"
                            class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                        >
                            <option :value="null">-- Select Standard Source or Custom Exemplar --</option>
                            <optgroup label="Official NBTE National Occupational Standards (NOS)">
                                <option
                                    v-for="std in standards.filter(s => s.is_official)"
                                    :key="std.id"
                                    :value="std.id"
                                >
                                    {{ std.code }} · {{ std.title }} ({{ std.version }})
                                </option>
                            </optgroup>
                            <optgroup label="Instructor-Created Teaching Standard Examples">
                                <option
                                    v-for="std in standards.filter(s => !s.is_official)"
                                    :key="std.id"
                                    :value="std.id"
                                >
                                    {{ std.code }} · {{ std.title }} [Instructor Example]
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Selected Standard Details Badge -->
                    <div v-if="selectedStandard" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ selectedStandard.title }}</span>
                            <span :class="[
                                'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full',
                                selectedStandard.is_official
                                    ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200'
                                    : 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200'
                            ]">
                                {{ selectedStandard.is_official ? 'Official Approved Standard' : 'Instructor Exemplar' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            <strong>Source Authority:</strong> {{ selectedStandard.source_authority }} · <strong>Version:</strong> {{ selectedStandard.version }}
                        </div>

                        <div v-if="selectedStandard.performance_criteria" class="pt-2 border-t border-slate-200 dark:border-slate-700">
                            <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Standard Performance Criteria:</div>
                            <ul class="space-y-1">
                                <li
                                    v-for="(crit, idx) in selectedStandard.performance_criteria"
                                    :key="idx"
                                    class="text-xs text-slate-600 dark:text-slate-400 flex items-start space-x-2"
                                >
                                    <span class="text-teal-600 font-bold shrink-0">{{ idx + 1 }}.</span>
                                    <span>{{ crit.criterion }}</span>
                                    <span v-if="crit.is_critical_safety" class="shrink-0 text-[10px] px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 font-bold">
                                        Critical Safety
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Measurable Learning Outcomes -->
            <div v-show="activeTab === 'outcomes'" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Measurable Learning Outcomes</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Specify behavioral action verbs, conditions of performance, and acceptable standards of quality.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="addOutcome"
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                    >
                        <Plus class="w-3.5 h-3.5" />
                        <span>Add Outcome</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(outcome, index) in form.learning_outcomes"
                        :key="index"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3 relative"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400">Outcome #{{ index + 1 }}</span>
                            <button
                                v-if="form.learning_outcomes.length > 1"
                                type="button"
                                @click="removeOutcome(index)"
                                class="text-slate-400 hover:text-rose-500"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Action Verb (Psychomotor / Cognitive)
                                </label>
                                <input
                                    v-model="outcome.verb"
                                    type="text"
                                    placeholder="e.g. Execute, Solder, Terminate, Calibrate"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Condition / Tools Given
                                </label>
                                <input
                                    v-model="outcome.condition"
                                    type="text"
                                    placeholder="e.g. Given a schematic blueprint and ratcheting crimper"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Measurable Standard / Tolerance
                                </label>
                                <input
                                    v-model="outcome.standard"
                                    type="text"
                                    placeholder="e.g. Within ±1mm tolerance, 0 polarity faults"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                Full Statement
                            </label>
                            <input
                                v-model="outcome.outcome_text"
                                type="text"
                                placeholder="e.g. The trainee will terminate a 4-string combiner box adhering to torque specifications without conductor strand shear."
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: 4-Step Instructor Demonstration Plan -->
            <div v-show="activeTab === 'demo'" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <span>Instructor 4-Step Demonstration Plan</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-semibold">
                                Core Practical Modeling
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Plan the 4 classical TVET modeling phases: 1) Preparation &amp; PPE, 2) Normal pace, 3) Step-by-step with safety tolerances, 4) Trainee imitation.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="addDemoStep"
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                    >
                        <Plus class="w-3.5 h-3.5" />
                        <span>Add Demo Phase</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(step, index) in form.demonstration_plan"
                        :key="index"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center space-x-2">
                                <span>Phase {{ step.step }}:</span>
                                <input
                                    v-model="step.name"
                                    type="text"
                                    class="text-xs font-bold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1"
                                />
                            </span>
                            <button
                                v-if="form.demonstration_plan.length > 2"
                                type="button"
                                @click="removeDemoStep(index)"
                                class="text-slate-400 hover:text-rose-500"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                Instructor Physical Action &amp; Explanation *
                            </label>
                            <textarea
                                v-model="step.instructor_action"
                                rows="2"
                                placeholder="Detail what the instructor shows, explains, and points out to trainees during this phase."
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                required
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Safety Precautions &amp; PPE *
                                </label>
                                <input
                                    v-model="step.safety_ppe"
                                    type="text"
                                    placeholder="e.g. Eye protection, insulated gloves, LOTO"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Key Tolerance / Standard
                                </label>
                                <input
                                    v-model="step.key_tolerance"
                                    type="text"
                                    placeholder="e.g. Stripping 7mm ±0.5mm, Torque 2.2 Nm"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Teaching Media / Mockup
                                </label>
                                <input
                                    v-model="step.media_resource"
                                    type="text"
                                    placeholder="e.g. Cutaway model, A1 schematic poster"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Guided & Independent Practice -->
            <div v-show="activeTab === 'practice'" class="space-y-6">
                <!-- Guided Practice -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Guided Learner Practice</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Step-by-step hands-on activities with instructor checkpoints and peer cross-checking.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addGuided"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Guided Step</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(guided, idx) in form.guided_practice"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-teal-600 dark:text-teal-400">Guided Step #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.guided_practice.length > 1"
                                    type="button"
                                    @click="removeGuided(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Hands-on Activity
                                    </label>
                                    <input
                                        v-model="guided.activity"
                                        type="text"
                                        placeholder="e.g. Trainee strips cable and crimps bootlace ferrule"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Instructor Checkpoint &amp; Verification
                                    </label>
                                    <input
                                        v-model="guided.check_point"
                                        type="text"
                                        placeholder="e.g. Instructor verifies pull test and 0 severed strands"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <label class="flex items-center space-x-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                <input
                                    v-model="guided.paired"
                                    type="checkbox"
                                    class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                />
                                <span>Pair trainees for peer-buddy cross-checking</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Independent Practice -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Independent Practice</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Unassisted execution under industrial tolerances to build speed, accuracy, and confidence.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addIndependent"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Independent Task</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(indep, idx) in form.independent_practice"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Independent Task #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.independent_practice.length > 1"
                                    type="button"
                                    @click="removeIndependent(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Independent Task Description
                                    </label>
                                    <input
                                        v-model="indep.activity"
                                        type="text"
                                        placeholder="e.g. Wire full 4-string combiner box autonomously"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Time Allocated (Minutes)
                                    </label>
                                    <input
                                        v-model.number="indep.time_minutes"
                                        type="number"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Success Metric / Standard
                                </label>
                                <input
                                    v-model="indep.success_metric"
                                    type="text"
                                    placeholder="e.g. Zero polarity errors, terminals torqued to 2.2 Nm, completed within 45 mins"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: PBL & Fault Diagnosis Scenarios -->
            <div v-show="activeTab === 'pbl_faults'" class="space-y-6">
                <!-- Project-Based Learning -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Project-Based Learning (PBL) Client Brief</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Real-world commercial TVET projects solving community or industry needs in Benue State.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addProject"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Project Brief</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(pbl, idx) in form.project_based_tasks"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400">PBL Project #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.project_based_tasks.length > 1"
                                    type="button"
                                    @click="removeProject(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Project Title
                                    </label>
                                    <input
                                        v-model="pbl.project_name"
                                        type="text"
                                        placeholder="e.g. Solar Hybrid Primary Clinic Power Pod"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Final Deliverable
                                    </label>
                                    <input
                                        v-model="pbl.deliverable"
                                        type="text"
                                        placeholder="e.g. Weatherproof IP65 combiner box with QA sign-off sheet"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Client / Community Scenario
                                </label>
                                <textarea
                                    v-model="pbl.client_brief"
                                    rows="2"
                                    placeholder="Describe the authentic client context, specifications, and constraints."
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Bill of Materials &amp; Consumables
                                </label>
                                <input
                                    v-model="pbl.materials"
                                    type="text"
                                    placeholder="e.g. Enclosure box, DC fuses, 1000V DC SPD, cable glands"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Problem-Based Learning: Fault Diagnosis Scenarios -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span>Problem-Based Learning &amp; Fault Diagnosis Scenarios</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 font-semibold">
                                    Troubleshooting Competency
                                </span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Define realistic equipment faults, diagnostic flowcharts, root causes, and safety rules.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addFault"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Fault Scenario</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(fault, idx) in form.fault_diagnosis_scenarios"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Fault Scenario #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.fault_diagnosis_scenarios.length > 1"
                                    type="button"
                                    @click="removeFault(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Fault Symptom / Manifestation
                                    </label>
                                    <input
                                        v-model="fault.fault_symptom"
                                        type="text"
                                        placeholder="e.g. Inverter error code 02 (DC reverse polarity) upon closing isolator"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Probable Root Cause
                                    </label>
                                    <input
                                        v-model="fault.root_cause"
                                        type="text"
                                        placeholder="e.g. Cross-connected polarity at array junction box"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Methodical Diagnostic Procedure
                                </label>
                                <textarea
                                    v-model="fault.diagnostic_procedure"
                                    rows="2"
                                    placeholder="1. Open isolator. 2. Remove string fuses. 3. Test string voltage with multimeter probes. 4. Tag erroneous string."
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Rectification &amp; Verification Action
                                    </label>
                                    <input
                                        v-model="fault.rectification"
                                        type="text"
                                        placeholder="e.g. Swap polarity leads and re-verify voltage before reclosing switch"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1 text-rose-600">
                                        Non-Negotiable Safety Rule
                                    </label>
                                    <input
                                        v-model="fault.safety_rule"
                                        type="text"
                                        placeholder="e.g. Never pull DC fuses under load to prevent arc flash"
                                        class="w-full text-xs rounded-lg border-rose-300 dark:border-rose-800 bg-white dark:bg-slate-800 text-rose-700 dark:text-rose-300"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work-Based Learning Activities -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Work-Based Learning (WBL) Integration</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Link this practical lesson to real workshop apprenticeships and technical logbook sign-offs.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addWbl"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add WBL Element</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(wbl, idx) in form.work_based_learning"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-purple-600 dark:text-purple-400">WBL Link #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.work_based_learning.length > 1"
                                    type="button"
                                    @click="removeWbl(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Industry / Enterprise Context
                                    </label>
                                    <input
                                        v-model="wbl.industry_context"
                                        type="text"
                                        placeholder="e.g. Solar Contractor Installation Team"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Apprentice Role / Responsibility
                                    </label>
                                    <input
                                        v-model="wbl.apprentice_role"
                                        type="text"
                                        placeholder="e.g. Junior Combiner Technician"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Logbook Sign-off Milestone
                                    </label>
                                    <input
                                        v-model="wbl.logbook_signoff"
                                        type="text"
                                        placeholder="e.g. Pre-commissioning quality checklist signed by certified engineer"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: Assessment Criteria & Evidence Requirements -->
            <div v-show="activeTab === 'assessment'" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Assessment Criteria &amp; Evidence Requirements</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Define objective evaluation standards, evidence collection methods, and critical safety non-negotiables.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="addCriteria"
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                    >
                        <Plus class="w-3.5 h-3.5" />
                        <span>Add Criterion</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(crit, idx) in form.assessment_criteria"
                        :key="idx"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Criterion #{{ idx + 1 }}</span>
                            <button
                                v-if="form.assessment_criteria.length > 1"
                                type="button"
                                @click="removeCriteria(idx)"
                                class="text-slate-400 hover:text-rose-500"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Assessment Criterion Statement *
                                </label>
                                <input
                                    v-model="crit.criterion"
                                    type="text"
                                    placeholder="e.g. Conductor stripping and ferrule crimp quality"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Evidence Type
                                </label>
                                <select
                                    v-model="crit.evidence_type"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                >
                                    <option value="Direct observation">Direct observation</option>
                                    <option value="Product inspection">Product inspection</option>
                                    <option value="Calibrated gauge test">Calibrated gauge test</option>
                                    <option value="Oral questioning">Oral questioning</option>
                                    <option value="Technical logbook">Technical logbook</option>
                                    <option value="Photo/Video portfolio">Photo/Video portfolio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Passing Metric
                                </label>
                                <input
                                    v-model="crit.passing_score"
                                    type="text"
                                    placeholder="e.g. ≥ 85%, Within ±1mm, 100%"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>

                        <label class="flex items-center space-x-2 text-xs text-rose-600 dark:text-rose-400 font-semibold cursor-pointer">
                            <input
                                v-model="crit.is_safety_critical"
                                type="checkbox"
                                class="rounded border-rose-300 text-rose-600 focus:ring-rose-500"
                            />
                            <span>Critical Safety Requirement (Immediate fail if violated)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bottom Floating Action Bar -->
            <div class="sticky bottom-4 p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 shadow-xl flex items-center justify-between">
                <div class="flex items-center space-x-3 text-xs text-slate-600 dark:text-slate-400">
                    <span :class="day9Status.isComplete ? 'text-emerald-500 font-bold' : 'text-amber-500 font-bold'">
                        {{ day9Status.isComplete ? '✓ Day 9 Ready' : `${day9Status.percent}% Completed` }}
                    </span>
                    <span>·</span>
                    <span>Status: <strong class="capitalize">{{ form.status }}</strong></span>
                </div>

                <div class="flex items-center space-x-3">
                    <button
                        type="button"
                        @click="submit('draft')"
                        :disabled="form.processing || !can_update"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition"
                    >
                        Save Draft
                    </button>
                    <button
                        type="button"
                        @click="submit('published')"
                        :disabled="form.processing || !can_update"
                        class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        Save &amp; Publish Lesson
                    </button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
